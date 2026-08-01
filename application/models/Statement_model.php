<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Statement_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Resolve which currency a statement should use.
     * Prefer explicit id, else customer default when they have invoices in that currency,
     * else the most-used invoice currency, else base currency.
     *
     * @param  mixed $customer_id
     * @param  mixed $currency_id
     * @return int
     */
    public function resolve_statement_currency_id($customer_id, $currency_id = null)
    {
        $currency_id = (int) $currency_id;
        if ($currency_id > 0) {
            return $currency_id;
        }

        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        $this->db->select(db_prefix() . 'invoices.currency, COUNT(*) as total_invoices');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->where('clientid', (int) $customer_id);
        $this->db->where(db_prefix() . 'invoices.status !=', Invoices_model::STATUS_DRAFT);
        $this->db->where(db_prefix() . 'invoices.status !=', Invoices_model::STATUS_CANCELLED);
        $this->db->group_by(db_prefix() . 'invoices.currency');
        $this->db->order_by('total_invoices', 'DESC');
        $used = $this->db->get()->result_array();

        $usedIds = [];
        foreach ($used as $row) {
            $usedIds[] = (int) $row['currency'];
        }

        $customerDefault = (int) $this->clients_model->get_customer_default_currency($customer_id);
        if ($customerDefault > 0 && (empty($usedIds) || in_array($customerDefault, $usedIds, true))) {
            return $customerDefault;
        }

        if (!empty($usedIds)) {
            return $usedIds[0];
        }

        $this->load->model('currencies_model');
        $base = $this->currencies_model->get_base_currency();

        return $base ? (int) $base->id : 0;
    }

    /**
     * Currencies that appear on this customer's invoices (for statement selector).
     *
     * @param  mixed $customer_id
     * @return array
     */
    public function get_statement_currencies_for_customer($customer_id)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        $this->load->model('currencies_model');

        $this->db->select(db_prefix() . 'currencies.id, ' . db_prefix() . 'currencies.name, ' . db_prefix() . 'currencies.symbol');
        $this->db->from(db_prefix() . 'invoices');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'invoices.currency', 'inner');
        $this->db->where(db_prefix() . 'invoices.clientid', (int) $customer_id);
        $this->db->where(db_prefix() . 'invoices.status !=', Invoices_model::STATUS_DRAFT);
        $this->db->where(db_prefix() . 'invoices.status !=', Invoices_model::STATUS_CANCELLED);
        $this->db->group_by(db_prefix() . 'currencies.id');
        $this->db->order_by(db_prefix() . 'currencies.name', 'ASC');
        $rows = $this->db->get()->result_array();

        if (!empty($rows)) {
            return $rows;
        }

        $base = $this->currencies_model->get_base_currency();
        if ($base) {
            return [[
                'id'     => $base->id,
                'name'   => $base->name,
                'symbol' => $base->symbol,
            ]];
        }

        return [];
    }

    /**
     * Get customer statement formatted
     * @param  mixed $customer_id customer id
     * @param  string $from        date from
     * @param  string $to          date to
     * @param  mixed  $currency_id optional currency id (filters invoices/payments to that currency)
     * @return array
     */
    public function get_statement($customer_id, $from, $to, $currency_id = null)
    {
        if (!class_exists('Invoices_model', false)) {
            $this->load->model('invoices_model');
        }

        $currency_id = $this->resolve_statement_currency_id($customer_id, $currency_id);
        $currencySql = ' AND ' . db_prefix() . 'invoices.currency = ' . (int) $currency_id;
        $creditCurrencySql = ' AND ' . db_prefix() . 'creditnotes.currency = ' . (int) $currency_id;

        $sql = 'SELECT
        ' . db_prefix() . 'invoices.id as invoice_id,
        hash,
        ' . db_prefix() . 'invoices.date as date,
        ' . db_prefix() . 'invoices.duedate,
        concat(' . db_prefix() . 'invoices.date, \' \', RIGHT(' . db_prefix() . 'invoices.datecreated,LOCATE(\' \',' . db_prefix() . 'invoices.datecreated) - 3)) as tmp_date,
        ' . db_prefix() . 'invoices.duedate as duedate,
        ' . db_prefix() . 'invoices.total as invoice_amount
        FROM ' . db_prefix() . 'invoices WHERE clientid =' . $this->db->escape_str($customer_id);

        if ($from == $to) {
            $sqlDate = 'date="' . $this->db->escape_str($from) . '"';
        } else {
            $sqlDate = '(date BETWEEN "' . $this->db->escape_str($from) . '" AND "' . $this->db->escape_str($to) . '")';
        }

        $sql .= ' AND ' . $sqlDate;

        $invoices = $this->db->query($sql . '
            AND status != ' . Invoices_model::STATUS_DRAFT . '
            AND status != ' . Invoices_model::STATUS_CANCELLED . '
            ' . $currencySql . '
            ORDER By date DESC')->result_array();

        // Credit notes
        $sql_credit_notes = 'SELECT
        ' . db_prefix() . 'creditnotes.id as credit_note_id,
        ' . db_prefix() . 'creditnotes.date as date,
        concat(' . db_prefix() . 'creditnotes.date, \' \', RIGHT(' . db_prefix() . 'creditnotes.datecreated,LOCATE(\' \',' . db_prefix() . 'creditnotes.datecreated) - 3)) as tmp_date,
        ' . db_prefix() . 'creditnotes.total as credit_note_amount
        FROM ' . db_prefix() . 'creditnotes WHERE clientid =' . $this->db->escape_str($customer_id) . ' AND status != 3';

        $sql_credit_notes .= ' AND ' . $sqlDate . $creditCurrencySql;

        $credit_notes = $this->db->query($sql_credit_notes)->result_array();

        // Credits applied
        $sql_credits_applied = 'SELECT
        ' . db_prefix() . 'credits.id as credit_id,
        invoice_id as credit_invoice_id,
        ' . db_prefix() . 'credits.credit_id as credit_applied_credit_note_id,
        ' . db_prefix() . 'credits.date as date,
        concat(' . db_prefix() . 'credits.date, \' \', RIGHT(' . db_prefix() . 'credits.date_applied,LOCATE(\' \',' . db_prefix() . 'credits.date_applied) - 3)) as tmp_date,
        ' . db_prefix() . 'credits.amount as credit_amount
        FROM ' . db_prefix() . 'credits
        JOIN ' . db_prefix() . 'creditnotes ON ' . db_prefix() . 'creditnotes.id = ' . db_prefix() . 'credits.credit_id
        ';

        $sql_credits_applied .= '
        WHERE clientid =' . $this->db->escape_str($customer_id);

        $sqlDateCreditsAplied = str_replace('date', db_prefix() . 'credits.date', $sqlDate);

        $sql_credits_applied .= ' AND ' . $sqlDateCreditsAplied . $creditCurrencySql;
        $credits_applied = $this->db->query($sql_credits_applied)->result_array();

        // Replace error ambigious column in where clause
        $sqlDatePayments = str_replace('date', db_prefix() . 'invoicepaymentrecords.date', $sqlDate);

        $sql_payments = 'SELECT
        ' . db_prefix() . 'invoicepaymentrecords.id as payment_id,
        ' . db_prefix() . 'invoicepaymentrecords.date as date,
        concat(' . db_prefix() . 'invoicepaymentrecords.date, \' \', RIGHT(' . db_prefix() . 'invoicepaymentrecords.daterecorded,LOCATE(\' \',' . db_prefix() . 'invoicepaymentrecords.daterecorded) - 3)) as tmp_date,
        ' . db_prefix() . 'invoicepaymentrecords.invoiceid as payment_invoice_id,
        ' . db_prefix() . 'invoicepaymentrecords.amount as payment_total
        FROM ' . db_prefix() . 'invoicepaymentrecords
        JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid
        WHERE ' . $sqlDatePayments . ' AND ' . db_prefix() . 'invoices.clientid = ' . $this->db->escape_str($customer_id) . '
        ' . $currencySql . '
        ORDER by ' . db_prefix() . 'invoicepaymentrecords.date DESC';

        $payments = $this->db->query($sql_payments)->result_array();

        $sqlCreditNoteRefunds = str_replace('date', 'refunded_on', $sqlDate);

        $sql_credit_notes_refunds = 'SELECT id as credit_note_refund_id,
        credit_note_id as refund_credit_note_id,
        amount as refund_amount,
        concat(' . db_prefix() . 'creditnote_refunds.refunded_on, \' \', RIGHT(' . db_prefix() . 'creditnote_refunds.created_at,LOCATE(\' \',' . db_prefix() . 'creditnote_refunds.created_at) - 3)) as tmp_date,
        refunded_on as date FROM ' . db_prefix() . 'creditnote_refunds
        WHERE ' . $sqlCreditNoteRefunds . ' AND credit_note_id IN (SELECT id FROM ' . db_prefix() . 'creditnotes WHERE clientid=' . $this->db->escape_str($customer_id) . ' AND currency=' . (int) $currency_id . ')
        ';

        $credit_notes_refunds = $this->db->query($sql_credit_notes_refunds)->result_array();

        // merge results
        $merged = array_merge($invoices, $payments, $credit_notes, $credits_applied, $credit_notes_refunds);

        // sort by date
        usort($merged, function ($a, $b) {
            // fake date select sorting
            return strtotime($a['tmp_date']) - strtotime($b['tmp_date']);
        });

        // Define final result variable
        $result = [];
        // Store in result array key
        $result['result'] = $merged;

        // Invoiced amount during the period
        $result['invoiced_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'invoices.total) as invoiced_amount
        FROM ' . db_prefix() . 'invoices
        WHERE clientid = ' . $this->db->escape_str($customer_id) . '
        AND ' . $sqlDate . ' AND status != ' . Invoices_model::STATUS_DRAFT . ' AND status != ' . Invoices_model::STATUS_CANCELLED . '
        ' . $currencySql)
            ->row()->invoiced_amount;

        if ($result['invoiced_amount'] === null) {
            $result['invoiced_amount'] = 0;
        }

        $result['credit_notes_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'creditnotes.total) as credit_notes_amount
        FROM ' . db_prefix() . 'creditnotes
        WHERE clientid = ' . $this->db->escape_str($customer_id) . '
        AND ' . $sqlDate . ' AND status != 3
        ' . $creditCurrencySql)
            ->row()->credit_notes_amount;

        if ($result['credit_notes_amount'] === null) {
            $result['credit_notes_amount'] = 0;
        }

        $result['refunds_amount'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'creditnote_refunds.amount) as refunds_amount
        FROM ' . db_prefix() . 'creditnote_refunds
        WHERE ' . $sqlCreditNoteRefunds . ' AND credit_note_id IN (SELECT id FROM ' . db_prefix() . 'creditnotes WHERE clientid=' . $this->db->escape_str($customer_id) . ' AND currency=' . (int) $currency_id . ')
        ')->row()->refunds_amount;

        if ($result['refunds_amount'] === null) {
            $result['refunds_amount'] = 0;
        }

        $result['invoiced_amount'] = $result['invoiced_amount'] - $result['credit_notes_amount'];

        // Amount paid during the period
        $result['amount_paid'] = $this->db->query('SELECT
        SUM(' . db_prefix() . 'invoicepaymentrecords.amount) as amount_paid
        FROM ' . db_prefix() . 'invoicepaymentrecords
        JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid
        WHERE ' . $sqlDatePayments . ' AND ' . db_prefix() . 'invoices.clientid = ' . $this->db->escape_str($customer_id) . '
        ' . $currencySql)
            ->row()->amount_paid;

        if ($result['amount_paid'] === null) {
            $result['amount_paid'] = 0;
        }

        // Beginning balance is all invoices amount before the FROM date - payments received before FROM date
        $result['beginning_balance'] = $this->db->query('
            SELECT (
            COALESCE(SUM(' . db_prefix() . 'invoices.total),0) - (
            (
            SELECT COALESCE(SUM(' . db_prefix() . 'invoicepaymentrecords.amount),0)
            FROM ' . db_prefix() . 'invoicepaymentrecords
            JOIN ' . db_prefix() . 'invoices ON ' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid
            WHERE ' . db_prefix() . 'invoicepaymentrecords.date < "' . $this->db->escape_str($from) . '"
            AND ' . db_prefix() . 'invoices.clientid=' . $this->db->escape_str($customer_id) . '
            AND ' . db_prefix() . 'invoices.currency=' . (int) $currency_id . '
            ) + (
                SELECT COALESCE(SUM(' . db_prefix() . 'creditnotes.total),0)
                FROM ' . db_prefix() . 'creditnotes
                WHERE ' . db_prefix() . 'creditnotes.date < "' . $this->db->escape_str($from) . '"
                AND ' . db_prefix() . 'creditnotes.clientid=' . $this->db->escape_str($customer_id) . '
                AND ' . db_prefix() . 'creditnotes.currency=' . (int) $currency_id . '
            ) - (
                SELECT COALESCE(SUM(' . db_prefix() . 'creditnote_refunds.amount),0)
                FROM ' . db_prefix() . 'creditnote_refunds
                WHERE ' . db_prefix() . 'creditnote_refunds.refunded_on < "' . $this->db->escape_str($from) . '"
                AND ' . db_prefix() . 'creditnote_refunds.credit_note_id IN (SELECT id FROM ' . db_prefix() . 'creditnotes WHERE clientid=' . $this->db->escape_str($customer_id) . ' AND currency=' . (int) $currency_id . ')
            )
        )
            )
            as beginning_balance FROM ' . db_prefix() . 'invoices
            WHERE date < "' . $this->db->escape_str($from) . '"
            AND clientid = ' . $this->db->escape_str($customer_id) . '
            AND status != ' . Invoices_model::STATUS_DRAFT . '
            AND status != ' . Invoices_model::STATUS_CANCELLED . '
            AND currency = ' . (int) $currency_id)
              ->row()->beginning_balance;

        if ($result['beginning_balance'] === null) {
            $result['beginning_balance'] = 0;
        }

        $dec = get_decimal_places();

        if (function_exists('bcsub')) {
            $result['balance_due'] = bcsub($result['invoiced_amount'], $result['amount_paid'], $dec);
            $result['balance_due'] = bcadd($result['balance_due'], $result['beginning_balance'], $dec);
            $result['balance_due'] = bcadd($result['balance_due'], $result['refunds_amount'], $dec);
        } else {
            $result['balance_due'] = number_format($result['invoiced_amount'] - $result['amount_paid'], $dec, '.', '');
            $result['balance_due'] = $result['balance_due'] + number_format($result['beginning_balance'], $dec, '.', '');
            $result['balance_due'] = $result['balance_due'] + number_format($result['refunds_amount'], $dec, '.', '');
        }

        // Subtract amount paid - refund, because the refund is not actually paid amount
        $result['amount_paid'] = $result['amount_paid'] - $result['refunds_amount'];

        $result['client_id'] = $customer_id;
        $result['client']    = $this->clients_model->get($customer_id);
        $result['from']      = $from;
        $result['to']        = $to;

        $this->load->model('currencies_model');
        $currency = $this->currencies_model->get($currency_id);
        if (!$currency) {
            $currency = $this->currencies_model->get_base_currency();
        }

        $result['currency']    = $currency;
        $result['currency_id'] = $currency_id;

        return hooks()->apply_filters('statement', $result);
    }

    /**
     * Send customer statement to email
     * @param  mixed $customer_id customer id
     * @param  array $send_to     array of contact emails to send
     * @param  string $from        date from
     * @param  string $to          date to
     * @param  string $cc          email CC
     * @param  mixed  $currency_id optional currency id
     * @return boolean
     */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '', $currency_id = null)
    {
        $sent = false;
        if (is_array($send_to) && count($send_to) > 0) {
            $statement = $this->get_statement($customer_id, to_sql_date($from), to_sql_date($to), $currency_id);
            set_mailing_constant();
            $pdf = statement_pdf($statement);

            $pdf_file_name = slug_it(_l('customer_statement') . '-' . $statement['client']->company);

            $attach = $pdf->Output($pdf_file_name . '.pdf', 'S');

            $i = 0;
            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $contact = $this->clients_model->get_contact($contact_id);

                    $template = mail_template('customer_statement', $contact->email, $contact_id, $statement, $cc);

                    $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => $pdf_file_name . '.pdf',
                            'type'       => 'application/pdf',
                        ]);

                    if ($template->send()) {
                        $sent = true;
                    }
                }
                $i++;
            }

            if ($sent) {
                return true;
            }
        }

        return false;
    }
}
