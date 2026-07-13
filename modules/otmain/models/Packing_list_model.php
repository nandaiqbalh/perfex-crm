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
            'conversion_rate',
            'conversion_currency',
        ];

        if (isset($data['conversion_rate']) || isset($data['conversion_currency'])) {
            $data = otmain_normalize_conversion_fields($data);
        }

        return array_intersect_key($data, array_flip($allowed));
    }

    private function save_items($id, $items)
    {
        $order       = 1;
        $subtotal    = 0;
        $totalTax    = 0;
        $totalWeight = 0;
        $totalCbm    = 0;

        foreach ($items as $item) {
            if (empty($item['description'])) {
                continue;
            }

            $qty     = (float) ($item['qty'] ?? 1);
            $rate    = (float) ($item['unit_price'] ?? 0);
            $taxrate = (float) ($item['taxrate'] ?? 0);
            $total   = $qty * $rate;
            $subtotal += $total;
            $totalTax += $total * ($taxrate / 100);
            $totalWeight += (float) ($item['gross_weight'] ?? 0);

            $unitType = strtolower(trim((string) ($item['unit_type'] ?? 'box')));
            if (!in_array($unitType, ['box', 'pallet', 'other'], true)) {
                $unitType = 'box';
            }
            $unitLabel = trim((string) ($item['unit_label'] ?? ''));
            $length = isset($item['length']) && $item['length'] !== '' ? (float) $item['length'] : null;
            $width  = isset($item['width']) && $item['width'] !== '' ? (float) $item['width'] : null;
            $height = isset($item['height']) && $item['height'] !== '' ? (float) $item['height'] : null;

            $cbm = 0.0;
            $packingDetail = trim((string) ($item['packing_detail'] ?? ''));
            $volume = trim((string) ($item['volume'] ?? ''));
            $hasPackaging = ($length !== null && $width !== null && $height !== null && $length > 0 && $width > 0 && $height > 0)
                || $packingDetail !== ''
                || $volume !== ''
                || (isset($item['gross_weight']) && $item['gross_weight'] !== '' && (float) $item['gross_weight'] > 0)
                || (isset($item['net_weight']) && $item['net_weight'] !== '' && (float) $item['net_weight'] > 0);

            // packing_qty only for lines that actually have packaging; do not copy commercial qty.
            // Column is NOT NULL — use 0 for commercial-only lines (Dimensions skips via has_packaging).
            if (isset($item['packing_qty']) && $item['packing_qty'] !== '') {
                $packingQty = (float) $item['packing_qty'];
            } elseif ($hasPackaging) {
                $packingQty = $qty;
            } else {
                $packingQty = 0;
            }

            if ($length !== null && $width !== null && $height !== null && $length > 0 && $width > 0 && $height > 0) {
                $cbm = otmain_calc_cbm_mm($length, $width, $height, $packingQty ?: 1);
                $packingDetail = otmain_format_packing_dimensions_string($packingQty ?: 1, $unitType, $unitLabel, $length, $width, $height);
                $volume = $cbm > 0 ? number_format($cbm, 3, '.', '') . ' CBM' : '';
            } elseif ($packingDetail !== '') {
                $cbm = otmain_cbm_from_dimensions_text($packingDetail, $packingQty ?: 1);
                if ($cbm > 0 && $volume === '') {
                    $volume = number_format($cbm, 3, '.', '') . ' CBM';
                }
            }

            $totalCbm += $cbm;

            $row = [
                'packing_list_id' => $id,
                'description'     => $item['description'],
                'hs_code'         => $item['hs_code'] ?? '',
                'qty'             => $qty,
                'unit_price'      => $rate,
                'taxrate'         => $taxrate,
                'total'           => $total,
                'packing_detail'  => $hasPackaging ? $packingDetail : '',
                'gross_weight'    => isset($item['gross_weight']) && $item['gross_weight'] !== '' ? $item['gross_weight'] : null,
                'net_weight'      => isset($item['net_weight']) && $item['net_weight'] !== '' ? $item['net_weight'] : null,
                'volume'          => $hasPackaging ? $volume : '',
                'item_order'      => $order++,
            ];

            $table = db_prefix() . 'otmain_packing_list_items';
            if ($this->db->field_exists('packing_qty', $table)) {
                $row['packing_qty'] = $packingQty;
            }
            if ($this->db->field_exists('unit_type', $table)) {
                // unit_type is NOT NULL with default 'box'
                $row['unit_type'] = $hasPackaging ? $unitType : 'box';
            }
            if ($this->db->field_exists('unit_label', $table)) {
                $row['unit_label'] = ($hasPackaging && $unitType === 'other') ? $unitLabel : '';
            }
            if ($this->db->field_exists('length', $table)) {
                $row['length'] = $length;
            }
            if ($this->db->field_exists('width', $table)) {
                $row['width'] = $width;
            }
            if ($this->db->field_exists('height', $table)) {
                $row['height'] = $height;
            }

            $this->db->insert($table, $row);
        }

        $rate = 0;
        $subtotalConverted = 0;
        $docCurrencyId = 0;
        $headerRow = null;
        if (!empty($id)) {
            $selectCols = 'currency';
            $table = db_prefix() . 'otmain_packing_lists';
            if ($this->db->field_exists('conversion_rate', $table)) {
                $selectCols .= ', conversion_rate';
            }
            if ($this->db->field_exists('conversion_currency', $table)) {
                $selectCols .= ', conversion_currency';
            }
            $headerRow = $this->db->select($selectCols)->where('id', $id)->get($table)->row();
            if ($headerRow) {
                $rate = otmain_get_conversion_rate($headerRow);
                $docCurrencyId = !empty($headerRow->currency) ? (int) $headerRow->currency : 0;
            }
        }
        if ($rate <= 0) {
            $rate = otmain_get_conversion_rate(null);
        }
        $targetCurrencyId = otmain_get_conversion_currency_id($headerRow);
        if ($rate > 0 && $targetCurrencyId > 0 && $targetCurrencyId !== $docCurrencyId) {
            $subtotalConverted = $subtotal * $rate;
        }

        $update = [
            'subtotal'     => $subtotal,
            'subtotal_usd' => $subtotalConverted,
            'total_weight' => $totalWeight,
        ];

        if ($this->db->field_exists('total_tax', db_prefix() . 'otmain_packing_lists')) {
            $update['total_tax'] = $totalTax;
        }
        if ($this->db->field_exists('total', db_prefix() . 'otmain_packing_lists')) {
            $update['total'] = $subtotal + $totalTax;
        }
        if ($this->db->field_exists('total_cbm', db_prefix() . 'otmain_packing_lists')) {
            $update['total_cbm'] = $totalCbm;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'otmain_packing_lists', $update);
    }
}
