<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Otmain extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('otmain/otmain');
    }

    public function get_estimate_data($id)
    {
        if (!$id) {
            echo json_encode([]);
            die;
        }

        $this->load->model('estimates_model');
        $estimate = $this->estimates_model->get($id);

        if (!$estimate) {
            echo json_encode([]);
            die;
        }

        $items = [];
        foreach ($estimate->items as $item) {
            $taxes = get_estimate_item_taxes($item['id']);
            $taxname = [];
            foreach ($taxes as $tax) {
                $taxname[] = $tax['taxname'] . '|' . $tax['taxrate'];
            }

            $items[] = [
                'description'      => $item['description'],
                'long_description' => $item['long_description'],
                'qty'              => $item['qty'],
                'rate'             => $item['rate'],
                'unit'             => $item['unit'],
                'taxname'          => $taxname,
            ];
        }

        echo json_encode([
            'clientid'           => $estimate->clientid,
            'currency'           => $estimate->currency,
            'billing_shipping'   => $this->clients_model->get_customer_billing_and_shipping_details($estimate->clientid),
            'items'              => $items,
            'estimate_number'    => format_estimate_number($estimate),
            'invoice_title'      => $estimate->quote_title,
            'payment_terms_text' => clear_textarea_breaks($estimate->payment_terms_text ?? ''),
            'delivery_time'      => $estimate->delivery_time ?? '',
            'availability'       => $estimate->availability ?? '',
            'shipment_terms'     => $estimate->shipment_terms ?? '',
        ]);
    }

    public function get_client_contacts($clientid)
    {
        if (!$clientid) {
            echo json_encode([]);
            die;
        }

        $contacts = $this->clients_model->get_contacts($clientid);
        echo json_encode($contacts);
    }

    public function get_bank_details()
    {
        $bankAccount = strtoupper(trim((string) $this->input->get('bank_account')));
        if ($bankAccount === 'EUR' || $bankAccount === 'USD') {
            $name = $bankAccount;
        } else {
            $currencyId = $this->input->get('currency_id');
            $this->load->model('currencies_model');
            $currency = $this->currencies_model->get($currencyId);
            $name     = $currency ? $currency->name : 'EUR';
        }

        echo json_encode([
            'html' => otmain_format_bank_details_html($name),
        ]);
    }

    public function get_proposal_data($id)
    {
        if (!$id) {
            echo json_encode([]);
            die;
        }

        $this->load->model('proposals_model');
        $proposal = $this->proposals_model->get($id);

        if (!$proposal) {
            echo json_encode([]);
            die;
        }

        $items = [];
        foreach ($proposal->items as $item) {
            $hsCode = '';
            if (!empty($item['long_description']) && preg_match('/HS\s*Code\s*:\s*([^\s<]+)/i', strip_tags($item['long_description']), $matches)) {
                $hsCode = trim($matches[1]);
            }

            $taxrate = 0.0;
            $taxes   = get_proposal_item_taxes($item['id']);
            if (!empty($taxes[0]['taxrate'])) {
                $taxrate = (float) $taxes[0]['taxrate'];
            }

            $items[] = [
                'description'      => $item['description'],
                'long_description' => $item['long_description'] ?? '',
                'hs_code'          => $hsCode,
                'qty'              => $item['qty'],
                'rate'             => $item['rate'],
                'taxrate'          => $taxrate,
            ];
        }

        echo json_encode([
            'clientid'         => $proposal->rel_type === 'customer' ? (int) $proposal->rel_id : 0,
            'proposal_number'  => format_proposal_number($proposal->id),
            'vessel'           => $proposal->quote_title ?? '',
            'items'            => $items,
        ]);
    }

    /**
     * Proposal data shaped for invoice Quote Ref fill (mirrors get_estimate_data).
     */
    public function get_proposal_invoice_data($id)
    {
        if (!$id) {
            echo json_encode([]);
            die;
        }

        $this->load->model('proposals_model');
        $proposal = $this->proposals_model->get($id);

        if (!$proposal) {
            echo json_encode([]);
            die;
        }

        $items = [];
        foreach ($proposal->items as $item) {
            $taxes   = get_proposal_item_taxes($item['id']);
            $taxname = [];
            foreach ($taxes as $tax) {
                $taxname[] = $tax['taxname'] . '|' . $tax['taxrate'];
            }

            $row = [
                'description'      => $item['description'],
                'long_description' => $item['long_description'] ?? '',
                'qty'              => $item['qty'],
                'rate'             => $item['rate'],
                'unit'             => $item['unit'] ?? '',
                'taxname'          => $taxname,
            ];
            if (array_key_exists('profit_percent', $item)) {
                $row['profit_percent'] = $item['profit_percent'];
            }
            if (array_key_exists('purchase_amount', $item)) {
                $row['purchase_amount'] = $item['purchase_amount'];
            }
            $items[] = $row;
        }

        $clientId = $proposal->rel_type === 'customer' ? (int) $proposal->rel_id : 0;

        echo json_encode([
            'clientid'             => $clientId,
            'currency'             => $proposal->currency ?? null,
            'items'                => $items,
            'proposal_number'      => format_proposal_number($proposal->id),
            'invoice_title'        => $proposal->quote_title ?? '',
            'payment_terms_text'   => clear_textarea_breaks($proposal->payment_terms_text ?? ''),
            'delivery_time'        => clear_textarea_breaks($proposal->delivery_time ?? ''),
            'availability'         => clear_textarea_breaks($proposal->availability ?? ''),
            'shipment_terms'       => clear_textarea_breaks($proposal->shipment_terms ?? ''),
            'contact_person_name'  => $proposal->contact_person_name ?? '',
            'contact_person_email' => $proposal->contact_person_email ?? '',
            'contact_person_phone' => $proposal->contact_person_phone ?? '',
        ]);
    }

    public function get_client_packing_data($clientid)
    {
        if (!$clientid) {
            echo json_encode([]);
            die;
        }

        $client = $this->clients_model->get($clientid);
        if (!$client) {
            echo json_encode([]);
            die;
        }

        $contact = otmain_get_primary_contact($clientid);
        $phone   = $contact['phonenumber'] ?? ($client->phonenumber ?? '');
        $email   = $contact['email'] ?? '';

        echo json_encode([
            'company'           => $client->company,
            'address'           => otmain_format_client_address_lines($clientid),
            'phone'             => $phone,
            'email'             => $email,
            'default_currency'  => !empty($client->default_currency) ? (int) $client->default_currency : 0,
        ]);
    }

    public function get_supplier_data($clientid)
    {
        if (!$clientid) {
            echo json_encode([]);
            die;
        }

        $client = $this->clients_model->get($clientid);
        if (!$client) {
            echo json_encode([]);
            die;
        }

        $contacts = $this->clients_model->get_contacts($clientid);
        $primary  = otmain_get_primary_contact($clientid);

        echo json_encode([
            'company' => $client->company,
            'address' => otmain_format_client_address_lines($clientid),
            'contacts' => $contacts,
            'primary_contact_id' => $primary['id'] ?? null,
            'contact_person' => $primary ? trim(($primary['firstname'] ?? '') . ' ' . ($primary['lastname'] ?? '')) : '',
            'email' => $primary['email'] ?? '',
            'phone' => $primary['phonenumber'] ?? ($client->phonenumber ?? ''),
        ]);
    }
}
