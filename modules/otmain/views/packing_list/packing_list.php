<?php defined('BASEPATH') or exit('No direct script access allowed');
$pl     = $packing_list ?? null;
$isEdit = !empty($pl);
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-6xl tw-mx-auto">
            <div class="tw-mb-3">
                <a href="<?php echo admin_url('otmain/packing_list'); ?>" class="btn btn-default">
                    <i class="fa fa-angle-left tw-mr-1"></i><?php echo _l('otmain_packing_lists'); ?>
                </a>
            </div>
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700"><?php echo e($title); ?></h4>

            <?php if ($isEdit) { ?>
            <div class="horizontal-scrollable-tabs">
                <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                <div class="horizontal-tabs">
                    <ul class="nav nav-tabs nav-tabs-horizontal nav-tabs-segmented tw-mb-3" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#tab_view" role="tab" data-toggle="tab"><?php echo _l('otmain_packing_list'); ?></a>
                        </li>
                        <?php if (staff_can('edit', 'otmain_packing_list')) { ?>
                        <li role="presentation">
                            <a href="#tab_edit" id="tab_edit_link" role="tab" data-toggle="tab"><?php echo _l('edit'); ?></a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_view">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="tw-flex tw-justify-end tw-mb-6">
                                <a href="<?php echo admin_url('otmain/packing_list/pdf/' . $pl->id); ?>" class="btn btn-default mleft5">
                                    <i class="fa fa-download tw-mr-1"></i><?php echo _l('download'); ?>
                                </a>
                                <?php if (staff_can('delete', 'otmain_packing_list')) { ?>
                                <a href="<?php echo admin_url('otmain/packing_list/delete/' . $pl->id); ?>" class="btn btn-danger _delete mleft5">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                                <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="bold"><?php echo _l('otmain_packing_list'); ?> #<?php echo e($pl->formatted_number); ?></p>
                                    <p><strong><?php echo _l('date'); ?>:</strong> <?php echo _d($pl->date); ?></p>
                                    <p><strong><?php echo _l('client'); ?>:</strong> <?php echo e(get_company_name($pl->clientid)); ?></p>
                                    <p><strong><?php echo _l('currency'); ?>:</strong> <?php echo e(otmain_packing_currency_name($pl)); ?></p>
                                    <p><strong><?php echo _l('otmain_vessel'); ?>:</strong> <?php echo e($pl->vessel); ?></p>
                                    <p><strong><?php echo _l('otmain_quote_reference'); ?>:</strong> <?php echo e($pl->quote_ref); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="bold"><?php echo _l('otmain_consignee'); ?></p>
                                    <p><?php echo e($pl->consignee_name); ?><br><?php echo process_text_content_for_display($pl->consignee_address); ?><br><?php echo e($pl->consignee_phone); ?><br><?php echo e($pl->consignee_email); ?></p>
                                    <p class="bold mtop15"><?php echo _l('otmain_purchaser'); ?></p>
                                    <p><?php echo e($pl->purchaser_name); ?><br><?php echo process_text_content_for_display($pl->purchaser_address); ?><br><?php echo e($pl->purchaser_phone); ?><br><?php echo e($pl->purchaser_email); ?></p>
                                </div>
                            </div>
                            <hr />
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('invoice_table_item_heading'); ?></th>
                                            <th><?php echo _l('otmain_hs_code'); ?></th>
                                            <th class="text-right"><?php echo _l('invoice_table_quantity_heading'); ?></th>
                                            <th class="text-right"><?php echo _l('invoice_table_rate_heading'); ?></th>
                                            <th class="text-right">VAT %</th>
                                            <th><?php echo _l('otmain_unit_type'); ?></th>
                                            <th class="text-right"><?php echo _l('otmain_packing_qty'); ?></th>
                                            <th><?php echo _l('otmain_dimensions'); ?></th>
                                            <th class="text-right"><?php echo _l('otmain_gross_weight'); ?></th>
                                            <th class="text-right"><?php echo _l('otmain_net_weight'); ?></th>
                                            <th><?php echo _l('otmain_volume'); ?></th>
                                            <th class="text-right"><?php echo _l('total'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pl->items as $item) {
                                            $unitDisp = otmain_packing_unit_display($item['unit_type'] ?? 'box', $item['unit_label'] ?? '');
                                            $packQty = $item['packing_qty'] ?? $item['qty'];
                                            $dimParts = [];
                                            if (!empty($item['length']) || !empty($item['width']) || !empty($item['height'])) {
                                                $dimParts[] = 'L' . ($item['length'] ?? '-') . ' x W' . ($item['width'] ?? '-') . ' x H' . ($item['height'] ?? '-') . 'mm';
                                            } elseif (!empty($item['packing_detail'])) {
                                                $dimParts[] = $item['packing_detail'];
                                            }
                                        ?>
                                        <tr>
                                            <td><?php echo e($item['description']); ?></td>
                                            <td><?php echo e($item['hs_code']); ?></td>
                                            <td class="text-right"><?php echo e($item['qty']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($item['unit_price']); ?></td>
                                            <td class="text-right"><?php echo e($item['taxrate'] ?? 0); ?></td>
                                            <td><?php echo e($unitDisp); ?></td>
                                            <td class="text-right"><?php echo e($packQty); ?></td>
                                            <td><?php echo e(implode(' ', $dimParts)); ?></td>
                                            <td class="text-right"><?php echo e($item['gross_weight']); ?></td>
                                            <td class="text-right"><?php echo e($item['net_weight']); ?></td>
                                            <td><?php echo e($item['volume']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($item['total']); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            $plSummary = otmain_pdf_po_calculate_vat_summary($pl->items);
                            $CI = &get_instance();
                            $CI->load->model('currencies_model');
                            $plCurrencyObj = !empty($pl->currency) ? $CI->currencies_model->get($pl->currency) : null;
                            $plCurrencyLabel = $plCurrencyObj ? $plCurrencyObj->name : otmain_packing_currency_name($pl);
                            ?>
                            <div class="row mtop15">
                                <div class="col-md-6 col-md-offset-6">
                                    <table class="table text-right">
                                        <tr><td><strong><?php echo _l('otmain_total_weight'); ?></strong></td><td><?php echo isset($pl->total_weight) ? app_format_number($pl->total_weight) . ' KGS' : '-'; ?></td></tr>
                                        <tr><td><strong><?php echo _l('otmain_total_cbm'); ?></strong></td><td><?php echo isset($pl->total_cbm) ? app_format_number($pl->total_cbm) : '0.00'; ?></td></tr>
                                        <tr><td><strong><?php echo _l('otmain_subtotal'); ?> <?php echo e(otmain_currency_display_code($plCurrencyLabel)); ?></strong></td><td><?php echo otmain_format_money_text($plSummary['subtotal'], $plCurrencyObj ?: $plCurrencyLabel); ?></td></tr>
                                        <?php foreach (($plSummary['by_rate'] ?? []) as $rate => $amount) { ?>
                                        <tr><td><strong>VAT <?php echo e((string) $rate); ?>%</strong></td><td><?php echo otmain_format_money_text($amount, $plCurrencyObj ?: $plCurrencyLabel); ?></td></tr>
                                        <?php } ?>
                                        <tr><td><strong><?php echo _l('otmain_total'); ?> <?php echo e(otmain_currency_display_code($plCurrencyLabel)); ?></strong></td><td><strong><?php echo otmain_format_money_text($plSummary['total'], $plCurrencyObj ?: $plCurrencyLabel); ?></strong></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (staff_can('edit', 'otmain_packing_list')) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_edit">
                    <?php $this->load->view('packing_list/packing_list_form', ['pl' => $pl, 'currencies' => $currencies ?? []]); ?>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <?php $this->load->view('packing_list/packing_list_form', ['pl' => null, 'currencies' => $currencies ?? []]); ?>
            <?php } ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php if ($isEdit) { ?>
<script>
$(function() {
    if (window.location.hash === '#tab_edit') {
        $('a[href="#tab_edit"]').tab('show');
    }
});
</script>
<?php } ?>
</body>
</html>
