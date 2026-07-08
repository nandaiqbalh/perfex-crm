<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('otmain/otmain');
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $po = $this->db->get(db_prefix() . 'otmain_purchase_orders')->row();
            if ($po) {
                $po->items = $this->get_items($id);
                $po        = otmain_po_apply_missing_defaults($po);
            }

            return $po;
        }

        return $this->db->get(db_prefix() . 'otmain_purchase_orders')->result_array();
    }

    public function get_items($id)
    {
        $this->db->where('purchase_order_id', $id);
        $this->db->order_by('item_order', 'asc');

        return $this->db->get(db_prefix() . 'otmain_purchase_order_items')->result_array();
    }

    public function add($data)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $data = $this->normalize_data($data);
        $data = $this->apply_defaults($data);
        $data = $this->filter_data($data);

        $data['number']           = get_option('next_otmain_purchase_order_number');
        $data['prefix']           = get_option('otmain_purchase_order_prefix');
        $data['date']             = to_sql_date($data['date']);
        $data['datecreated']      = date('Y-m-d H:i:s');
        $data['addedfrom']        = get_staff_user_id();
        $data['formatted_number'] = date('Y', strtotime($data['date'])) . '-' . $data['prefix'] . str_pad($data['number'], 3, '0', STR_PAD_LEFT);

        $totals = $this->calculate_totals($items);
        $data['subtotal']  = $totals['subtotal'];
        $data['total_tax'] = $totals['total_tax'];
        $data['total']     = $totals['total'];

        $this->db->insert(db_prefix() . 'otmain_purchase_orders', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            update_option('next_otmain_purchase_order_number', (int) get_option('next_otmain_purchase_order_number') + 1);
            $this->save_items($insert_id, $items);
        }

        return $insert_id;
    }

    public function update($data, $id)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $data = $this->normalize_data($data);
        $data = $this->filter_data($data);
        $data['date'] = to_sql_date($data['date']);

        $totals = $this->calculate_totals($items);
        $data['subtotal']  = $totals['subtotal'];
        $data['total_tax'] = $totals['total_tax'];
        $data['total']     = $totals['total'];

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'otmain_purchase_orders', $data);

        $this->db->where('purchase_order_id', $id);
        $this->db->delete(db_prefix() . 'otmain_purchase_order_items');
        $this->save_items($id, $items);

        return true;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'otmain_purchase_orders');
        $this->db->where('purchase_order_id', $id);
        $this->db->delete(db_prefix() . 'otmain_purchase_order_items');

        return true;
    }

    private function allowed_fields()
    {
        return [
            'document_title',
            'supplierid',
            'supplier_address',
            'supplier_quote_ref',
            'otmain_contact_id',
            'contact_person',
            'email',
            'phone',
            'company_name',
            'company_address',
            'company_postal_code',
            'company_city',
            'company_country',
            'company_phone',
            'company_email_invoices',
            'company_website',
            'company_vat',
            'company_coc',
            'iban',
            'currency',
            'adminnote',
            'date',
            'subtotal',
            'total_tax',
            'total',
        ];
    }

    private function filter_data(array $data)
    {
        return array_intersect_key($data, array_flip($this->allowed_fields()));
    }

    private function apply_defaults(array $data)
    {
        foreach (otmain_get_po_company_defaults() as $field => $value) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $data[$field] = $value;
            }
        }

        if (empty($data['supplier_address']) && !empty($data['supplierid'])) {
            $data['supplier_address'] = nl2br_save_html(otmain_format_client_address_lines($data['supplierid']));
        }

        return $data;
    }

    private function normalize_data(array $data)
    {
        if (isset($data['supplier_address'])) {
            $data['supplier_address'] = nl2br_save_html($data['supplier_address']);
        }

        if (empty($data['otmain_contact_id'])) {
            $data['otmain_contact_id'] = null;
        }

        return $data;
    }

    private function calculate_totals($items)
    {
        $summary = otmain_pdf_po_calculate_vat_summary($items);

        return [
            'subtotal'  => $summary['subtotal'],
            'total_tax' => $summary['vat21'] + $summary['vat0'],
            'total'     => $summary['total'],
        ];
    }

    private function save_items($id, $items)
    {
        $order = 1;
        foreach ($items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty   = (float) ($item['qty'] ?? 1);
            $rate  = (float) ($item['unit_price'] ?? 0);
            $tax   = (float) ($item['taxrate'] ?? 0);
            $total = $qty * $rate;

            $this->db->insert(db_prefix() . 'otmain_purchase_order_items', [
                'purchase_order_id' => $id,
                'description'       => $item['description'],
                'qty'               => $qty,
                'unit_price'        => $rate,
                'taxname'           => $item['taxname'] ?? '',
                'taxrate'           => $tax,
                'total'             => $total,
                'item_order'        => $order++,
            ]);
        }
    }
}
