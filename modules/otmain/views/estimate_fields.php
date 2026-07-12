<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-6">
        <?php
        $value = isset($estimate) ? $estimate->client_ref : '';
        echo render_input('client_ref', _l('otmain_client_ref'), $value);
        ?>
    </div>
    <div class="col-md-6">
        <?php
        $value = isset($estimate) ? $estimate->quote_title : '';
        echo render_input('quote_title', _l('otmain_quote_title'), $value);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $expiryDays = isset($estimate) && !empty($estimate->expiry_days) ? $estimate->expiry_days : get_option('estimate_due_after');
        echo render_input('expiry_days', _l('otmain_expiry_days'), $expiryDays, 'number', ['min' => 0]);
        ?>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label"><?php echo _l('otmain_contact_person_select'); ?></label>
            <select name="otmain_contact_id" id="otmain_contact_id" class="selectpicker" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value=""></option>
                <?php if (isset($estimate) && !empty($estimate->otmain_contact_id)) { ?>
                <option value="<?php echo (int) $estimate->otmain_contact_id; ?>" selected></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? ($estimate->contact_person_name ?? '') : '';
        echo render_input('contact_person_name', _l('otmain_contact_person'), $value);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? ($estimate->contact_person_email ?? '') : '';
        echo render_input('contact_person_email', _l('otmain_contact_person_email'), $value, 'email');
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? ($estimate->contact_person_phone ?? '') : '';
        echo render_input('contact_person_phone', _l('otmain_contact_person_phone'), $value);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? $estimate->payment_terms_text : '';
        echo render_textarea('payment_terms_text', _l('otmain_payment_terms'), $value, ['rows' => 2]);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? $estimate->shipment_terms : 'EXW (Ex Works)';
        echo render_input('shipment_terms', _l('otmain_shipment_terms'), $value);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? $estimate->delivery_time : '';
        echo render_input('delivery_time', _l('otmain_delivery_time'), $value);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? $estimate->availability : '';
        echo render_input('availability', _l('otmain_availability'), $value);
        ?>
    </div>
    <?php echo otmain_render_conversion_fields_html(isset($estimate) ? $estimate : null, ['id_prefix' => 'otmain-estimate', 'col_class' => 'col-md-4']); ?>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? ($estimate->total_usd_display ?? '') : '';
        echo render_input('total_usd_display', _l('otmain_total_usd_display'), $value, 'text', ['placeholder' => 'e.g. 9,00 USD']);
        ?>
    </div>
    <div class="col-md-4">
        <?php
        $value = isset($estimate) ? ($estimate->total_gold_display ?? '') : '';
        echo render_input('total_gold_display', _l('otmain_total_gold_display'), $value, 'text', ['placeholder' => 'e.g. 999.9 in Gram']);
        ?>
    </div>
</div>

