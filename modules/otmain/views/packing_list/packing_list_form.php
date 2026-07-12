<?php defined('BASEPATH') or exit('No direct script access allowed');
$pl = $pl ?? null;
$quoteRefIds = [];
if (!empty($pl) && !empty($pl->quote_ref_ids)) {
    $quoteRefIds = array_filter(array_map('trim', explode(',', $pl->quote_ref_ids)));
}
?>
<div class="panel_s">
    <div class="panel-body otmain-edit-form">
        <?php echo form_open($this->uri->uri_string(), ['id' => 'otmain-packing-list-form']); ?>
        <input type="hidden" id="otmain-eur-usd-rate" value="<?php echo e(get_option('otmain_eur_to_usd_rate')); ?>">

        <?php otmain_form_section_open(_l('otmain_section_document')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <?php
                $value = isset($pl) ? ($pl->document_title ?? 'Packing List & Invoice') : 'Packing List & Invoice';
                echo render_input('document_title', _l('otmain_document_title'), $value);
                ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <?php echo render_date_input('date', 'date', isset($pl) ? _d($pl->date) : _d(date('Y-m-d'))); ?>
                <?php echo render_input('vessel', 'otmain_vessel', $pl->vessel ?? ''); ?>
                <?php
                if (empty($currencies)) {
                    $CI = &get_instance();
                    $CI->load->model('currencies_model');
                    $currencies = $CI->currencies_model->get();
                }
                $selectedCurrency = isset($pl) && !empty($pl->currency)
                    ? (int) $pl->currency
                    : (get_base_currency() ? (int) get_base_currency()->id : '');
                echo render_select(
                    'currency',
                    $currencies,
                    ['id', 'name', 'symbol'],
                    'currency',
                    $selectedCurrency,
                    ['data-show-subtext' => true],
                    [],
                    '',
                    '',
                    false
                );
                ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_party')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <?php echo render_select('clientid', [], [], 'client', $pl->clientid ?? '', [], [], '', 'ajax-search'); ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <div class="form-group select-placeholder">
                    <label for="packing_quote_ref" class="control-label"><?php echo _l('otmain_quote_reference'); ?></label>
                    <select id="packing_quote_ref" name="quote_ref_ids[]" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('otmain_select_quote'); ?>" multiple data-abs-preserve-selected="true">
                        <?php
                        if (!empty($quoteRefIds)) {
                            $CI = &get_instance();
                            $CI->load->model('proposals_model');
                            foreach ($quoteRefIds as $proposalId) {
                                if (!is_numeric($proposalId)) {
                                    continue;
                                }
                                $proposal = $CI->proposals_model->get($proposalId);
                                if ($proposal) {
                                    echo '<option value="' . (int) $proposalId . '" selected>' . e(format_proposal_number($proposalId)) . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                $quoteRefValue = !empty($pl->quote_ref) ? clear_textarea_breaks($pl->quote_ref) : '';
                echo render_textarea('quote_ref', _l('otmain_quote_reference_display'), $quoteRefValue, ['rows' => 3]);
                ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_contact')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <div class="form-group">
                    <label class="control-label"><?php echo _l('otmain_contact_person_select'); ?></label>
                    <select name="otmain_contact_id" id="otmain_pl_contact_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php if (!empty($pl->otmain_contact_id)) { ?>
                        <option value="<?php echo (int) $pl->otmain_contact_id; ?>" selected></option>
                        <?php } ?>
                    </select>
                </div>
                <?php
                $value = isset($pl) ? ($pl->contact_person_name ?? '') : '';
                echo render_input('contact_person_name', _l('otmain_contact_person'), $value);
                ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <?php
                $value = isset($pl) ? ($pl->contact_person_email ?? '') : '';
                echo render_input('contact_person_email', _l('otmain_contact_person_email'), $value, 'email');
                $value = isset($pl) ? ($pl->contact_person_phone ?? '') : '';
                echo render_input('contact_person_phone', _l('otmain_contact_person_phone'), $value);
                ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_parties')); ?>
        <div class="row">
            <div class="col-md-6 otmain-col-left">
                <h5 class="bold tw-text-sm tw-mb-3"><?php echo _l('otmain_consignee'); ?></h5>
                <?php echo render_input('consignee_name', 'name', $pl->consignee_name ?? ''); ?>
                <?php echo render_textarea('consignee_address', 'address', isset($pl) ? clear_textarea_breaks($pl->consignee_address ?? '') : '', ['rows' => 3]); ?>
                <?php echo render_input('consignee_phone', 'client_phonenumber', $pl->consignee_phone ?? ''); ?>
                <?php echo render_input('consignee_email', 'client_email', $pl->consignee_email ?? ''); ?>
            </div>
            <div class="col-md-6 otmain-col-right">
                <h5 class="bold tw-text-sm tw-mb-3"><?php echo _l('otmain_purchaser'); ?></h5>
                <?php echo render_input('purchaser_name', 'name', $pl->purchaser_name ?? ''); ?>
                <?php echo render_textarea('purchaser_address', 'address', isset($pl) ? clear_textarea_breaks($pl->purchaser_address ?? '') : '', ['rows' => 3]); ?>
                <?php echo render_input('purchaser_phone', 'client_phonenumber', $pl->purchaser_phone ?? ''); ?>
                <?php echo render_input('purchaser_email', 'client_email', $pl->purchaser_email ?? ''); ?>
            </div>
        </div>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_items')); ?>
        <div class="table-responsive" id="otmain-packing-items">
            <table class="table items table-main-estimate-edit has-calculations no-margin">
                <thead>
                    <tr>
                        <th><?php echo _l('invoice_table_quantity_heading'); ?></th>
                        <th><?php echo _l('invoice_table_item_heading'); ?></th>
                        <th><?php echo _l('otmain_hs_code'); ?></th>
                        <th><?php echo _l('invoice_table_rate_heading'); ?></th>
                        <th width="10%">VAT %</th>
                        <th><?php echo _l('total'); ?></th>
                        <th><?php echo _l('otmain_dimensions'); ?></th>
                        <th><?php echo _l('otmain_gross_weight'); ?></th>
                        <th><?php echo _l('otmain_net_weight'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pl->items)) { foreach ($pl->items as $i => $item) { ?>
                    <tr class="item-row">
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][qty]" class="form-control otmain-packing-qty" value="<?php echo e($item['qty']); ?>"></td>
                        <td><input type="text" name="items[<?php echo $i; ?>][description]" class="form-control" value="<?php echo e($item['description']); ?>"></td>
                        <td><input type="text" name="items[<?php echo $i; ?>][hs_code]" class="form-control" value="<?php echo e($item['hs_code']); ?>"></td>
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][unit_price]" class="form-control otmain-packing-rate" value="<?php echo e($item['unit_price']); ?>"></td>
                        <td><input type="number" step="any" min="0" name="items[<?php echo $i; ?>][taxrate]" class="form-control otmain-packing-tax" value="<?php echo e($item['taxrate'] ?? 0); ?>"></td>
                        <td><input type="text" class="form-control otmain-packing-line-total" readonly value="<?php echo e(app_format_number($item['total'])); ?>"></td>
                        <td><input type="text" name="items[<?php echo $i; ?>][packing_detail]" class="form-control" value="<?php echo e($item['packing_detail']); ?>"></td>
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][gross_weight]" class="form-control otmain-packing-gross-weight" value="<?php echo e($item['gross_weight']); ?>"></td>
                        <td><input type="number" step="any" name="items[<?php echo $i; ?>][net_weight]" class="form-control" value="<?php echo e($item['net_weight']); ?>"></td>
                        <td><button type="button" class="btn btn-danger btn-sm otmain-remove-row"><i class="fa fa-times"></i></button></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-default mtop10" id="otmain-add-packing-row">
            <i class="fa fa-plus tw-mr-1"></i><?php echo _l('add_item'); ?>
        </button>
        <?php otmain_form_section_close(); ?>

        <?php otmain_form_section_open(_l('otmain_section_totals'), 'otmain-pl-totals-section'); ?>
        <div class="row">
            <div class="col-md-6 col-md-offset-6 otmain-col-right">
                <table class="table text-right" id="otmain-packing-totals-table">
                    <tr>
                        <td><strong><?php echo _l('otmain_total_weight'); ?></strong></td>
                        <td id="otmain-packing-total-weight">0</td>
                    </tr>
                    <tr id="otmain-packing-subtotal-row">
                        <td><strong id="otmain-packing-subtotal-label"><?php echo _l('otmain_subtotal'); ?></strong></td>
                        <td id="otmain-packing-subtotal-eur">0</td>
                    </tr>
                    <tr id="otmain-packing-usd-row">
                        <td><strong><?php echo _l('otmain_subtotal_usd'); ?></strong></td>
                        <td id="otmain-packing-subtotal-usd">0</td>
                    </tr>
                    <tr id="otmain-packing-total-row">
                        <td><strong id="otmain-packing-total-label"><?php echo _l('otmain_total'); ?></strong></td>
                        <td id="otmain-packing-total"><strong>0</strong></td>
                    </tr>
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
<script>
init_ajax_search('customer', 'select[name="clientid"].ajax-search');
</script>
