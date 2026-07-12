<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Packing_list_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $packing = $this->db->get(db_prefix() . 'otmain_packing_lists')->row();
            if ($packing) {
                $packing->items = $this->get_items($id);
            }

            return $packing;
        }

        return $this->db->get(db_prefix() . 'otmain_packing_lists')->result_array();
    }

    public function get_items($id)
    {
        $this->db->where('packing_list_id', $id);
        $this->db->order_by('item_order', 'asc');

        return $this->db->get(db_prefix() . 'otmain_packing_list_items')->result_array();
    }

    public function add($data)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $data = $this->normalize_data($data);

        $data['number']             = get_option('next_otmain_packing_list_number');
        $data['prefix']             = get_option('otmain_packing_list_prefix');
        $data['date']               = to_sql_date($data['date']);
        $data['datecreated']        = date('Y-m-d H:i:s');
        $data['addedfrom']          = get_staff_user_id();
        $data['formatted_number']   = date('Y', strtotime($data['date'])) . '-' . $data['prefix'] . str_pad($data['number'], 3, '0', STR_PAD_LEFT);

        $this->db->insert(db_prefix() . 'otmain_packing_lists', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            update_option('next_otmain_packing_list_number', (int) get_option('next_otmain_packing_list_number') + 1);
            $this->save_items($insert_id, $items);
        }

        return $insert_id;
    }

    public function update($data, $id)
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $data = $this->normalize_data($data);
        $data['date'] = to_sql_date($data['date']);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'otmain_packing_lists', $data);

        $this->db->where('packing_list_id', $id);
        $this->db->delete(db_prefix() . 'otmain_packing_list_items');
        $this->save_items($id, $items);

        return true;
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'otmain_packing_lists');
        $this->db->where('packing_list_id', $id);
        $this->db->delete(db_prefix() . 'otmain_packing_list_items');

        return true;
    }

    private function normalize_data(array $data)
    {
        if (isset($data['quote_ref_ids']) && is_array($data['quote_ref_ids'])) {
            $data['quote_ref_ids'] = implode(',', array_filter($data['quote_ref_ids']));
        }

        if (empty($data['document_title'])) {
            $data['document_title'] = 'Packing List & Invoice';
        }

        if (empty($data['otmain_contact_id'])) {
            $data['otmain_contact_id'] = null;
        }

        if (empty($data['currency'])) {
            $base = get_base_currency();
            $data['currency'] = $base ? (int) $base->id : 0;
        } else {
            $data['currency'] = (int) $data['currency'];
        }

        foreach (['consignee_address', 'purchaser_address', 'quote_ref', 'adminnote'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = nl2br_save_html($data[$field]);
            }
        }

        $allowed = [
            'document_title',
            'clientid',
            'quote_ref',
            'quote_ref_ids',
            'consignee_name',
            'consignee_address',
            'consignee_phone',
            'consignee_email',
            'purchaser_name',
            'purchaser_address',
            'purchaser_phone',
            'purchaser_email',
            'vessel',
            'currency',
            'otmain_contact_id',
            'contact_person_name',
            'contact_person_email',
            'contact_person_phone',
            'adminnote',
            'date',
        ];

        return array_intersect_key($data, array_flip($allowed));
    }

    private function save_items($id, $items)
    {
        $order       = 1;
        $subtotal    = 0;
        $totalWeight = 0;

        foreach ($items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty   = (float) ($item['qty'] ?? 1);
            $rate  = (float) ($item['unit_price'] ?? 0);
            $total = $qty * $rate;
            $subtotal += $total;
            $totalWeight += (float) ($item['gross_weight'] ?? 0);

            $this->db->insert(db_prefix() . 'otmain_packing_list_items', [
                'packing_list_id' => $id,
                'description'     => $item['description'],
                'hs_code'         => $item['hs_code'] ?? '',
                'qty'             => $qty,
                'unit_price'      => $rate,
                'total'           => $total,
                'packing_detail'  => $item['packing_detail'] ?? '',
                'gross_weight'    => $item['gross_weight'] !== '' ? $item['gross_weight'] : null,
                'net_weight'      => $item['net_weight'] !== '' ? $item['net_weight'] : null,
                'volume'          => $item['volume'] ?? '',
                'item_order'      => $order++,
            ]);
        }

        $rate = (float) str_replace(',', '.', (string) get_option('otmain_eur_to_usd_rate'));
        $subtotalUsd = 0;
        $currencyName = '';
        if (!empty($id)) {
            $row = $this->db->select('currency')->where('id', $id)->get(db_prefix() . 'otmain_packing_lists')->row();
            if ($row && !empty($row->currency)) {
                $this->load->model('currencies_model');
                $currency = $this->currencies_model->get($row->currency);
                $currencyName = $currency ? strtoupper($currency->name) : '';
            }
        }
        if ($rate > 0 && ($currencyName === '' || $currencyName === 'EUR')) {
            $subtotalUsd = $subtotal * $rate;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'otmain_packing_lists', [
            'subtotal'      => $subtotal,
            'subtotal_usd'  => $subtotalUsd,
            'total_weight'  => $totalWeight,
        ]);
    }
}
