<?php defined('BASEPATH') or exit('No direct script access allowed');
$po     = $purchase_order ?? null;
$isEdit = !empty($po);
$this->load->model('currencies_model');
$this->load->helper('otmain/otmain');
$currency = $isEdit ? $this->currencies_model->get($po->currency) : null;
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-6xl tw-mx-auto">
            <div class="tw-mb-3">
                <a href="<?php echo admin_url('otmain/purchase_order'); ?>" class="btn btn-default">
                    <i class="fa fa-angle-left tw-mr-1"></i><?php echo _l('otmain_purchase_orders'); ?>
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
                            <a href="#tab_view" role="tab" data-toggle="tab"><?php echo _l('otmain_purchase_order'); ?></a>
                        </li>
                        <?php if (staff_can('edit', 'otmain_purchase_order')) { ?>
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
                                <a href="<?php echo admin_url('otmain/purchase_order/pdf/' . $po->id); ?>" class="btn btn-default">
                                    <i class="fa fa-download tw-mr-1"></i><?php echo _l('download'); ?>
                                </a>
                                <?php if (staff_can('delete', 'otmain_purchase_order')) { ?>
                                <a href="<?php echo admin_url('otmain/purchase_order/delete/' . $po->id); ?>" class="btn btn-danger _delete mleft5">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                                <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="bold"><?php echo e($po->document_title ?? _l('otmain_purchase_order')); ?> #<?php echo e($po->formatted_number); ?></p>
                                    <p><strong><?php echo _l('date'); ?>:</strong> <?php echo _d($po->date); ?></p>
                                    <p><strong><?php echo _l('otmain_po_to'); ?>:</strong> <?php echo e(get_company_name($po->supplierid)); ?></p>
                                    <p><?php echo process_text_content_for_display($po->supplier_address ?? ''); ?></p>
                                    <p class="bold mtop15"><?php echo _l('otmain_supplier_contact'); ?></p>
                                    <p><strong><?php echo _l('otmain_contact_person'); ?>:</strong> <?php echo e($po->contact_person); ?></p>
                                    <p><strong><?php echo _l('client_email'); ?>:</strong> <?php echo e($po->email); ?></p>
                                    <p><strong><?php echo _l('client_phonenumber'); ?>:</strong> <?php echo e($po->phone); ?></p>
                                    <p><strong><?php echo _l('otmain_supplier_quote_ref'); ?>:</strong> <?php echo e($po->supplier_quote_ref); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="bold"><?php echo _l('otmain_po_issuer'); ?></p>
                                    <p><?php echo e($po->company_name); ?><br>
                                    <?php echo e($po->company_address); ?><br>
                                    <?php echo e($po->company_postal_code); ?> <?php echo e($po->company_city); ?><br>
                                    <?php echo e($po->company_country); ?></p>
                                    <p><strong><?php echo _l('client_phonenumber'); ?>:</strong> <?php echo e($po->company_phone); ?></p>
                                    <p><strong><?php echo _l('otmain_email_invoices'); ?>:</strong> <?php echo e($po->company_email_invoices); ?></p>
                                    <p><strong><?php echo _l('website'); ?>:</strong> <?php echo e($po->company_website); ?></p>
                                    <p><strong><?php echo _l('otmain_vat_number'); ?>:</strong> <?php echo e($po->company_vat); ?></p>
                                    <p><strong><?php echo _l('otmain_coc_number'); ?>:</strong> <?php echo e($po->company_coc); ?></p>
                                    <p><strong><?php echo _l('otmain_iban'); ?>:</strong> <?php echo e($po->iban); ?></p>
                                </div>
                            </div>
                            <?php $summary = otmain_pdf_po_calculate_vat_summary($po->items); ?>
                            <hr />
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-right"><?php echo _l('otmain_qty'); ?></th>
                                            <th><?php echo _l('invoice_table_item_heading'); ?></th>
                                            <th class="text-right"><?php echo _l('invoice_table_rate_heading'); ?></th>
                                            <th class="text-right">VAT %</th>
                                            <th class="text-right"><?php echo _l('total'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($po->items as $item) {
                                            $lineTotal = (float) $item['qty'] * (float) $item['unit_price'];
                                        ?>
                                        <tr>
                                            <td class="text-right"><?php echo e($item['qty']); ?></td>
                                            <td><?php echo e($item['description']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($item['unit_price']); ?></td>
                                            <td class="text-right"><?php echo e($item['taxrate']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($lineTotal); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mtop15">
                                <div class="col-md-6 col-md-offset-6">
                                    <table class="table text-right">
                                        <?php $currencyLabel = $currency ? $currency->name : otmain_po_currency_name($po); ?>
                                        <tr><td><strong><?php echo _l('otmain_subtotal'); ?> <?php echo e($currencyLabel); ?></strong></td><td><?php echo app_format_money($summary['subtotal'], $currency); ?></td></tr>
                                        <?php foreach (($summary['by_rate'] ?? []) as $rate => $amount) { ?>
                                        <tr><td><strong>VAT <?php echo e((string) $rate); ?>%</strong></td><td><?php echo app_format_money($amount, $currency); ?></td></tr>
                                        <?php } ?>
                                        <tr><td><strong><?php echo _l('otmain_total'); ?> <?php echo e($currencyLabel); ?></strong></td><td><strong><?php echo app_format_money($summary['total'], $currency); ?></strong></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (staff_can('edit', 'otmain_purchase_order')) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_edit">
                    <?php $this->load->view('purchase_order/purchase_order_form', [
                        'po'             => $po,
                        'currencies'     => $currencies,
                        'po_defaults'    => $po_defaults ?? otmain_get_po_company_defaults(),
                        'next_po_number' => $next_po_number ?? otmain_preview_purchase_order_number(),
                    ]); ?>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <?php $this->load->view('purchase_order/purchase_order_form', [
                'po'             => null,
                'currencies'     => $currencies,
                'po_defaults'    => $po_defaults ?? otmain_get_po_company_defaults(),
                'next_po_number' => $next_po_number ?? otmain_preview_purchase_order_number(),
            ]); ?>
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
