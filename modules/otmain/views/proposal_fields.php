<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Renders one OT-Main proposal field group.
 * @var string $section document|contact|terms|extras|notes
 * @var object|null $proposal
 */
$section  = $section ?? 'document';
$proposal = $proposal ?? null;
?>
<?php if ($section === 'document') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->document_title ?? '') : 'Quotation';
        echo render_input('document_title', _l('otmain_document_title'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->client_ref ?? '') : '';
        echo render_input('client_ref', _l('otmain_client_ref'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->quote_title ?? '') : '';
        echo render_input('quote_title', _l('otmain_quote_title'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $expiryDays = isset($proposal) && !empty($proposal->expiry_days) ? $proposal->expiry_days : get_option('proposal_due_after');
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
            </select>
        </div>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->contact_person_name ?? '') : '';
        echo render_input('contact_person_name', _l('otmain_contact_person'), $value, 'text', ['placeholder' => 'e.g. John Doe']);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->contact_person_email ?? '') : '';
        echo render_input('contact_person_email', _l('otmain_contact_person_email'), $value, 'email');
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->contact_person_phone ?? '') : '';
        echo render_input('contact_person_phone', _l('otmain_contact_person_phone'), $value, 'text');
        ?>
    </div>
</div>
<?php } elseif ($section === 'terms') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->payment_terms_text ?? '') : '';
        echo render_textarea('payment_terms_text', _l('otmain_payment_terms'), $value, ['rows' => 2]);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->shipment_terms ?? 'EXW (Ex Works)') : 'EXW (Ex Works)';
        echo render_input('shipment_terms', _l('otmain_shipment_terms'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->delivery_time ?? '') : '';
        echo render_input('delivery_time', _l('otmain_delivery_time'), $value);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->availability ?? '') : '';
        echo render_input('availability', _l('otmain_availability'), $value);
        ?>
    </div>
</div>
<?php } elseif ($section === 'extras') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->total_usd_display ?? '') : '';
        echo render_input('total_usd_display', _l('otmain_total_usd_display'), $value, 'text', ['placeholder' => 'e.g. $ 9,00']);
        ?>
    </div>
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->total_gold_display ?? '') : '';
        echo render_input('total_gold_display', _l('otmain_total_gold_display'), $value, 'text', ['placeholder' => 'e.g. Gold 999.9 in Gram']);
        ?>
    </div>
</div>
<?php } elseif ($section === 'notes') { ?>
<div class="row">
    <div class="col-md-12">
        <?php
        $value = isset($proposal) ? ($proposal->notes ?? '') : '';
        echo render_textarea('notes', _l('otmain_notes'), $value, ['rows' => 2]);
        ?>
    </div>
</div>
<?php } ?>
