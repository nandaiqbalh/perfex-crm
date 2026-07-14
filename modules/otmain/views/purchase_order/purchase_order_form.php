<?php defined('BASEPATH') or exit('No direct script access allowed');
$po       = $po ?? null;
$defaults = $po_defaults ?? otmain_get_po_company_defaults();
$poNumber = isset($po) ? $po->formatted_number : ($next_po_number ?? otmain_preview_purchase_order_number());
?>
<div class="panel_s">
    <div class="panel-body otmain-edit-form">
        <?php echo form_open($this->uri->uri_string(), ['id' => 'otmain-purchase-order-form']); ?>

        <?php otmain_form_section_open(_l('otmain_section_document')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <?php echo render_input('document_title', _l('otmain_document_title'), isset($po) ? ($po->document_title ?? $defaults['document_title']) : $defaults['document_title']); ?>
                <?php
                if (empty($currencies)) {
                    $CI = &get_instance();
                    $CI->load->model('currencies_model');
                    $currencies = $CI->currencies_model->get();
                }
                $selectedPoCurrency = isset($po) && !empty($po->currency)
                    ? (int) $po->currency
                    : (get_base_currency() ? (int) get_base_currency()->id : '');
                ?>
                <div class="form-group" app-field-wrapper="currency">
                    <label for="otmain_po_currency" class="control-label"><?php echo _l('currency'); ?></label>
                    <select name="currency" id="otmain_po_currency" class="form-control otmain-native-currency-select">
                        <?php foreach ($currencies as $currencyOption) {
                            $cid = (int) ($currencyOption['id'] ?? 0);
                            if ($cid < 1) {
                                continue;
                            }
                            $cname  = (string) ($currencyOption['name'] ?? '');
                            $csymbol = trim((string) ($currencyOption['symbol'] ?? ''));
                            $label  = $cname;
                            if ($csymbol !== '' && strcasecmp($csymbol, $cname) !== 0) {
                                $label .= ' (' . $csymbol . ')';
                            }
                            ?>
                            <option value="<?php echo $cid; ?>"
                                data-subtext="<?php echo e($csymbol); ?>"
                                <?php echo $selectedPoCurrency === $cid ? 'selected' : ''; ?>>
                                <?php echo e($label); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6 otmain-col-right">
                <div class="form-group">
                    <label class="control-label"><?php echo _l('otmain_po_number'); ?></label>
                    <input type="text" class="form-control" id="otmain-po-number-preview" value="<?php echo e($poNumber); ?>" readonly>
                </div>
                <?php echo render_date_input('date', 'date', isset($po) ? _d($po->date) : _d(date('Y-m-d'))); ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_supplier')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <div class="form-group select-placeholder">
                    <label class="control-label"><?php echo _l('otmain_po_to'); ?></label>
                    <select name="supplierid" id="supplierid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php if (!empty($po->supplierid)) {
                            echo '<option value="' . $po->supplierid . '" selected>' . e(get_company_name($po->supplierid)) . '</option>';
                        } ?>
                    </select>
                </div>
                <?php echo render_input('supplier_quote_ref', 'otmain_supplier_quote_ref', isset($po) ? ($po->supplier_quote_ref ?? '') : ''); ?>
                <div class="form-group select-placeholder">
                    <label class="control-label"><?php echo _l('otmain_quote_reference'); ?> <small class="text-muted">(<?php echo _l('otmain_optional'); ?>)</small></label>
                    <select name="proposal_id" id="po_proposal_id" class="ajax-search" data-width="100%" data-live-search="true" data-allow-clear="true" data-none-selected-text="<?php echo _l('otmain_select_quote'); ?>">
                        <option value=""></option>
                        <?php
                        if (isset($po) && !empty($po->proposal_id)) {
                            echo '<option value="' . (int) $po->proposal_id . '" selected>' . e(format_proposal_number((int) $po->proposal_id)) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6 otmain-col-right">
                <?php
                $supplierAddress = isset($po) ? clear_textarea_breaks($po->supplier_address ?? '') : '';
                echo render_textarea('supplier_address', 'otmain_supplier_address', $supplierAddress, [
                    'rows'         => 4,
                    'autocomplete' => 'off',
                ]);
                ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_contact')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <div class="form-group">
                    <label class="control-label"><?php echo _l('otmain_contact_person_select'); ?></label>
                    <select name="otmain_contact_id" id="otmain_po_contact_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php if (isset($po) && !empty($po->otmain_contact_id)) { ?>
                        <option value="<?php echo (int) $po->otmain_contact_id; ?>" selected></option>
                        <?php } ?>
                    </select>
                </div>
                <?php echo render_input('contact_person', 'otmain_contact_person', isset($po) ? ($po->contact_person ?? '') : ''); ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <?php echo render_input('email', 'client_email', isset($po) ? ($po->email ?? '') : ''); ?>
                <?php echo render_input('phone', 'client_phonenumber', isset($po) ? ($po->phone ?? '') : ''); ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_issuer')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <?php echo render_input('company_name', 'name', isset($po) ? ($po->company_name ?? $defaults['company_name']) : $defaults['company_name']); ?>
                <?php echo render_input('company_address', 'address', isset($po) ? ($po->company_address ?? $defaults['company_address']) : $defaults['company_address']); ?>
                <div class="row">
                    <div class="col-md-6"><?php echo render_input('company_postal_code', 'zip', isset($po) ? ($po->company_postal_code ?? $defaults['company_postal_code']) : $defaults['company_postal_code']); ?></div>
                    <div class="col-md-6"><?php echo render_input('company_city', 'city', isset($po) ? ($po->company_city ?? $defaults['company_city']) : $defaults['company_city']); ?></div>
                </div>
                <?php echo render_input('company_country', 'country', isset($po) ? ($po->company_country ?? $defaults['company_country']) : $defaults['company_country']); ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <?php echo render_input('company_phone', 'client_phonenumber', isset($po) ? ($po->company_phone ?? $defaults['company_phone']) : $defaults['company_phone']); ?>
                <?php echo render_input('company_email_invoices', 'otmain_email_invoices', isset($po) ? ($po->company_email_invoices ?? $defaults['company_email_invoices']) : $defaults['company_email_invoices']); ?>
                <?php echo render_input('company_website', 'website', isset($po) ? ($po->company_website ?? $defaults['company_website']) : $defaults['company_website']); ?>
                <?php echo render_input('company_vat', 'otmain_vat_number', isset($po) ? ($po->company_vat ?? $defaults['company_vat']) : $defaults['company_vat']); ?>
                <?php echo render_input('company_coc', 'otmain_coc_number', isset($po) ? ($po->company_coc ?? $defaults['company_coc']) : $defaults['company_coc']); ?>
                <?php echo render_input('iban', 'otmain_iban', isset($po) ? ($po->iban ?? $defaults['iban']) : $defaults['iban']); ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_items')); ?>
        <div class="table-responsive">
            <table class="table items table-main-estimate-edit has-calculations no-margin" id="otmain-po-items">
                <thead>
                    <tr>
                        <th width="8%"><?php echo _l('otmain_qty'); ?></th>
                        <th><?php echo _l('invoice_table_item_heading'); ?></th>
                        <th width="15%"><?php echo _l('invoice_table_rate_heading'); ?></th>
                        <th width="10%">VAT %</th>
                        <th width="15%"><?php echo _l('total'); ?></th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($po->items)) { foreach ($po->items as $i => $item) {
                        $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
                    ?>
                    <tr class="item-row">
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][qty]" class="form-control otmain-po-qty" value="<?php echo e($item['qty']); ?>"></td>
                        <td><input type="text" name="items[<?php echo $i; ?>][description]" class="form-control" value="<?php echo e($item['description']); ?>"></td>
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][unit_price]" class="form-control otmain-po-rate" value="<?php echo e($item['unit_price']); ?>"></td>
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][taxrate]" class="form-control otmain-po-tax" value="<?php echo e($item['taxrate']); ?>"></td>
                        <td><input type="text" class="form-control otmain-po-line-total" readonly value="<?php echo e(app_format_number($lineTotal)); ?>"></td>
                        <td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-default mtop10" id="otmain-add-po-row">
            <i class="fa fa-plus tw-mr-1"></i><?php echo _l('add_item'); ?>
        </button>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_totals')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <div class="row">
                    <?php echo otmain_render_conversion_fields_html(isset($po) ? $po : null, [
                        'id_prefix'     => 'otmain-po',
                        'native_select' => true,
                    ]); ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $value = isset($po) ? ($po->total_usd_display ?? '') : '';
                        echo render_input('total_usd_display', _l('otmain_total_usd_display'), $value, 'text', ['placeholder' => 'e.g. 9,00 USD']);
                        ?>
                    </div>
                    <div class="col-md-12">
                        <?php
                        $value = isset($po) ? ($po->total_gold_display ?? '') : '';
                        echo render_input('total_gold_display', _l('otmain_total_gold_display'), $value, 'text', ['placeholder' => 'e.g. 999.9 in GR.']);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 otmain-col-right">
                <table class="table text-right" id="otmain-po-totals-table">
                    <tr id="otmain-po-subtotal-row"><td><strong id="otmain-po-subtotal-label"><?php echo _l('otmain_subtotal'); ?></strong></td><td id="otmain-po-subtotal">0.00</td></tr>
                    <tr id="otmain-po-total-row"><td><strong id="otmain-po-total-label"><?php echo _l('otmain_total'); ?></strong></td><td id="otmain-po-total"><strong>0.00</strong></td></tr>
                </table>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <div class="text-right mtop15">
            <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
