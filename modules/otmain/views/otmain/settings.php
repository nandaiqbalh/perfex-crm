<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$eur = otmain_get_bank_details('EUR');
$usd = otmain_get_bank_details('USD');
$fields = otmain_bank_detail_fields();
$labels = [
    'account_holder' => _l('otmain_bank_account_holder'),
    'iban'           => _l('otmain_iban'),
    'account_number' => _l('otmain_bank_account_number'),
    'routing_number' => _l('otmain_bank_routing_number'),
    'swift'          => _l('otmain_bank_swift'),
    'bank'           => _l('otmain_bank_name'),
    'address'        => _l('otmain_bank_address'),
];
?>

<div class="row">
    <div class="col-md-6">
        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('otmain_bank_details_eur'); ?></h4>
        <?php foreach ($fields as $field) {
            $name = 'settings[otmain_bank_eur_' . $field . ']';
            $value = $eur[$field] ?? '';
            if ($field === 'address') {
                echo render_textarea($name, $labels[$field], $value, ['rows' => 3]);
            } else {
                echo render_input($name, $labels[$field], $value);
            }
        } ?>
    </div>
    <div class="col-md-6">
        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('otmain_bank_details_usd'); ?></h4>
        <?php foreach ($fields as $field) {
            $name = 'settings[otmain_bank_usd_' . $field . ']';
            $value = $usd[$field] ?? '';
            if ($field === 'address') {
                echo render_textarea($name, $labels[$field], $value, ['rows' => 3]);
            } else {
                echo render_input($name, $labels[$field], $value);
            }
        } ?>
    </div>
</div>

<p class="text-muted mtop15"><?php echo _l('otmain_bank_settings_help'); ?></p>

<hr class="hr-panel-separator" />

<div class="row mtop15">
    <div class="col-md-6">
        <h4 class="tw-font-semibold tw-mb-4"><?php echo _l('otmain_default_conversion_rate'); ?></h4>
        <?php
        $CI = &get_instance();
        $CI->load->model('currencies_model');
        $settingsCurrencies = $CI->currencies_model->get();
        $defaultConvCurrency = (int) get_option('otmain_default_conversion_currency');
        if ($defaultConvCurrency <= 0) {
            $usd = get_currency('USD');
            $defaultConvCurrency = $usd ? (int) $usd->id : 0;
        }
        echo render_select(
            'settings[otmain_default_conversion_currency]',
            $settingsCurrencies,
            ['id', 'name', 'symbol'],
            _l('otmain_default_conversion_currency'),
            $defaultConvCurrency,
            ['data-show-subtext' => true],
            [],
            '',
            '',
            false
        );
        echo render_input(
            'settings[otmain_eur_to_usd_rate]',
            _l('otmain_conversion_rate'),
            get_option('otmain_eur_to_usd_rate'),
            'number',
            ['step' => 'any', 'min' => '0', 'placeholder' => 'e.g. 1.09']
        );
        ?>
        <p class="text-muted"><?php echo _l('otmain_default_conversion_rate_help'); ?></p>
    </div>
</div>
