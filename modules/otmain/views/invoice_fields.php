<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Renders one OT-Main invoice field group.
 * @var string $section document|contact|terms|addresses|extras|notes
 * @var object|null $invoice
 */
$section = $section ?? 'document';
$invoice = $invoice ?? null;
?>
<?php if ($section === 'document') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->document_title ?? '') : 'Commercial Invoice';
        echo render_input('document_title', _l('otmain_document_title'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <div class="form-group select-placeholder">
            <label for="quote_ref" class="control-label"><?php echo _l('otmain_quote_reference'); ?></label>
            <select name="quote_ref" id="quote_ref" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('otmain_select_quote'); ?>">
                <?php
                if (isset($invoice) && !empty($invoice->quote_ref)) {
                    $CI = &get_instance();
                    $CI->load->model('estimates_model');
                    $estimate = $CI->estimates_model->get($invoice->quote_ref);
                    if ($estimate) {
                        echo '<option value="' . $estimate->id . '" selected>' . format_estimate_number($estimate) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? $invoice->invoice_title : '';
        echo render_input('invoice_title', _l('otmain_invoice_title'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $expiryDays = isset($invoice) && !empty($invoice->expiry_days) ? $invoice->expiry_days : get_option('invoice_due_after');
        echo render_input('expiry_days', _l('otmain_expiry_days'), $expiryDays, 'number', ['min' => 0]);
        ?>
    </div>
</div>
<?php } elseif ($section === 'contact') { ?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label"><?php echo _l('otmain_contact_person_select'); ?></label>
            <select name="otmain_contact_id" id="otmain_contact_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value=""></option>
                <?php if (isset($invoice) && !empty($invoice->otmain_contact_id)) { ?>
                <option value="<?php echo (int) $invoice->otmain_contact_id; ?>" selected></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->contact_person_name ?? '') : '';
        echo render_input('contact_person_name', _l('otmain_contact_person'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->contact_person_email ?? '') : '';
        echo render_input('contact_person_email', _l('otmain_contact_person_email'), $value, 'email');
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->contact_person_phone ?? '') : '';
        echo render_input('contact_person_phone', _l('otmain_contact_person_phone'), $value);
        ?>
    </div>
</div>
<?php } elseif ($section === 'terms') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->payment_terms_text ?? '') : '';
        echo render_textarea('payment_terms_text', _l('otmain_payment_terms'), $value, ['rows' => 2]);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->delivery_terms ?? 'EXW (Ex Works)') : 'EXW (Ex Works)';
        echo render_input('delivery_terms', _l('otmain_shipment_terms'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->lead_time ?? '') : '';
        echo render_input('lead_time', _l('otmain_delivery_time'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->availability ?? '') : '';
        echo render_input('availability', _l('otmain_availability'), $value);
        ?>
    </div>
</div>
<?php } elseif ($section === 'addresses') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->delivery_address ?? '') : '';
        echo render_textarea('delivery_address', _l('otmain_delivery_address'), $value, ['rows' => 2]);
        ?>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default" id="otmain-packing-details-panel">
            <div class="panel-heading">
                <?php echo _l('otmain_packing_details'); ?>
                <button type="button" class="btn btn-primary btn-xs pull-right" id="otmain-add-packing-row">
                    <i class="fa fa-plus"></i> <?php echo _l('otmain_add_row'); ?>
                </button>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="otmain-packing-items">
                        <thead>
                            <tr>
                                <th width="10%"><?php echo _l('otmain_unit_type'); ?></th>
                                <th width="8%"><?php echo _l('otmain_packing_qty'); ?></th>
                                <th width="10%"><?php echo _l('otmain_length_short'); ?></th>
                                <th width="10%"><?php echo _l('otmain_width_short'); ?></th>
                                <th width="10%"><?php echo _l('otmain_height_short'); ?></th>
                                <th width="10%"><?php echo _l('otmain_cbm'); ?></th>
                                <th width="12%"><?php echo _l('otmain_gw'); ?></th>
                                <th width="12%"><?php echo _l('otmain_nw'); ?></th>
                                <th width="8%"><?php echo _l('otmain_action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $packingItems = isset($invoice) ? json_decode($invoice->packing_items ?? '[]', true) : [];
                            if (!empty($packingItems)) {
                                foreach ($packingItems as $i => $pItem) {
                                    $qtyP = (float) ($pItem['qty'] ?? 1);
                                    $qtyPDisplay = (fmod($qtyP, 1.0) === 0.0) ? (string) (int) $qtyP : $qtyP;
                                    $unitType = $pItem['unit_type'] ?? 'box';
                                    $unitLabel = $pItem['unit_label'] ?? '';
                                    $length = $pItem['length'] ?? '';
                                    $width  = $pItem['width'] ?? '';
                                    $height = $pItem['height'] ?? '';
                                    $hasStructured = ($length !== '' && $length !== null) || ($width !== '' && $width !== null) || ($height !== '' && $height !== null);
                                    $legacyDims = !$hasStructured ? ($pItem['dimensions'] ?? '') : '';
                                    $cbmVal = (float) ($pItem['cbm'] ?? 0);
                                    ?>
                            <tr class="item-row">
                                <td>
                                    <?php echo otmain_packing_unit_select_html($unitType, 'packing_items[' . $i . '][unit_type]'); ?>
                                    <input type="text" name="packing_items[<?php echo $i; ?>][unit_label]" class="form-control otmain-packing-unit-label mtop5" placeholder="<?php echo e(_l('otmain_unit_label')); ?>" value="<?php echo e($unitLabel); ?>" style="<?php echo $unitType === 'other' ? '' : 'display:none;'; ?>">
                                </td>
                                <td><input type="number" step="any" min="0" name="packing_items[<?php echo $i; ?>][qty]" class="form-control otmain-packing-qty" value="<?php echo e($qtyPDisplay); ?>"></td>
                                <td><input type="number" step="any" min="0" name="packing_items[<?php echo $i; ?>][length]" class="form-control otmain-packing-length" value="<?php echo e($length !== null && $length !== '' ? $length : ''); ?>"></td>
                                <td><input type="number" step="any" min="0" name="packing_items[<?php echo $i; ?>][width]" class="form-control otmain-packing-width" value="<?php echo e($width !== null && $width !== '' ? $width : ''); ?>"></td>
                                <td><input type="number" step="any" min="0" name="packing_items[<?php echo $i; ?>][height]" class="form-control otmain-packing-height" value="<?php echo e($height !== null && $height !== '' ? $height : ''); ?>"></td>
                                <td>
                                    <input type="text" class="form-control otmain-packing-cbm-display" readonly value="<?php echo $cbmVal > 0 ? app_format_number($cbmVal) : '0.00'; ?>">
                                    <?php if ($legacyDims !== '') { ?>
                                    <textarea name="packing_items[<?php echo $i; ?>][dimensions]" class="form-control otmain-packing-dims mtop5" rows="2" placeholder="<?php echo e(_l('otmain_legacy_dimensions')); ?>"><?php echo e($legacyDims); ?></textarea>
                                    <?php } else { ?>
                                    <input type="hidden" name="packing_items[<?php echo $i; ?>][dimensions]" value="">
                                    <?php } ?>
                                </td>
                                <td><input type="number" step="any" name="packing_items[<?php echo $i; ?>][gw]" class="form-control otmain-packing-gw" value="<?php echo (float) ($pItem['gw'] ?? 0); ?>"></td>
                                <td><input type="number" step="any" name="packing_items[<?php echo $i; ?>][nw]" class="form-control otmain-packing-nw" value="<?php echo (float) ($pItem['nw'] ?? 0); ?>"></td>
                                <td><button type="button" class="btn btn-danger btn-sm otmain-remove-packing-row"><i class="fa fa-times"></i></button></td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong><?php echo _l('otmain_total_gw'); ?>: </strong><span id="otmain-total-gw"><?php echo isset($invoice) ? app_format_number($invoice->total_gw ?? 0) : '0.00'; ?></span>
                        &nbsp;&nbsp;
                        <strong><?php echo _l('otmain_total_nw'); ?>: </strong><span id="otmain-total-nw"><?php echo isset($invoice) ? app_format_number($invoice->total_nw ?? 0) : '0.00'; ?></span>
                        &nbsp;&nbsp;
                        <strong><?php echo _l('otmain_total_cbm'); ?>: </strong><span id="otmain-total-cbm"><?php echo isset($invoice) ? app_format_number($invoice->total_cbm ?? 0) : '0.00'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } elseif ($section === 'extras') { ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" id="otmain-bank-details-panel">
            <div class="panel-heading">
                <?php echo _l('otmain_bank_details'); ?>
                <a href="<?php echo admin_url('settings?group=otmain'); ?>" class="pull-right tw-text-sm">
                    <?php echo _l('settings'); ?>
                </a>
            </div>
            <div class="panel-body" id="otmain-bank-details-preview"></div>
        </div>
    </div>
    <div class="col-md-6">
        <?php
        $CI = &get_instance();
        $CI->load->model('currencies_model');
        $currencies = $CI->currencies_model->get();
        $defaultConvRate = get_option('otmain_eur_to_usd_rate');
        $invConvRate = isset($invoice) && isset($invoice->conversion_rate) && $invoice->conversion_rate !== null && $invoice->conversion_rate !== ''
            ? $invoice->conversion_rate
            : $defaultConvRate;
        $invConvCurrency = otmain_get_conversion_currency_id(isset($invoice) ? $invoice : null);
        echo render_select(
            'conversion_currency',
            $currencies,
            ['id', 'name', 'symbol'],
            _l('otmain_conversion_currency'),
            $invConvCurrency,
            ['data-show-subtext' => true, 'id' => 'otmain-invoice-conversion-currency'],
            [],
            '',
            '',
            false
        );
        ?>
    </div>
    <div class="col-md-6">
        <?php
        echo render_input(
            'conversion_rate',
            _l('otmain_conversion_rate'),
            $invConvRate,
            'number',
            [
                'step'        => 'any',
                'min'         => '0',
                'id'          => 'otmain-invoice-conversion-rate',
                'placeholder' => $defaultConvRate !== '' ? $defaultConvRate : 'e.g. 1.09',
            ]
        );
        ?>
        <p class="text-muted"><?php echo _l('otmain_conversion_rate_help'); ?></p>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->total_usd_display ?? '') : '';
        echo render_input('total_usd_display', _l('otmain_total_usd_display'), $value, 'text', ['placeholder' => 'e.g. 9,00 USD']);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->total_gold_display ?? '') : '';
        echo render_input('total_gold_display', _l('otmain_total_gold_display'), $value, 'text', ['placeholder' => 'e.g. 999.9 in GR.']);
        ?>
    </div>
</div>
<?php } elseif ($section === 'notes') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($invoice) ? ($invoice->notes ?? '') : '';
        echo render_textarea('notes', _l('otmain_notes'), $value, ['rows' => 2]);
        ?>
    </div>
</div>
<?php } ?>
