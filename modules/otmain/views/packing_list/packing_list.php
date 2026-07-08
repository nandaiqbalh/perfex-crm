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
                                            <th><?php echo _l('otmain_packing_detail'); ?></th>
                                            <th class="text-right"><?php echo _l('otmain_gross_weight'); ?></th>
                                            <th class="text-right"><?php echo _l('otmain_net_weight'); ?></th>
                                            <th><?php echo _l('otmain_volume'); ?></th>
                                            <th class="text-right"><?php echo _l('total'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pl->items as $item) { ?>
                                        <tr>
                                            <td><?php echo e($item['description']); ?></td>
                                            <td><?php echo e($item['hs_code']); ?></td>
                                            <td class="text-right"><?php echo e($item['qty']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($item['unit_price']); ?></td>
                                            <td><?php echo e($item['packing_detail']); ?></td>
                                            <td class="text-right"><?php echo e($item['gross_weight']); ?></td>
                                            <td class="text-right"><?php echo e($item['net_weight']); ?></td>
                                            <td><?php echo e($item['volume']); ?></td>
                                            <td class="text-right"><?php echo app_format_number($item['total']); ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (staff_can('edit', 'otmain_packing_list')) { ?>
                <div role="tabpanel" class="tab-pane" id="tab_edit">
                    <?php $this->load->view('packing_list/packing_list_form', ['pl' => $pl]); ?>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <?php $this->load->view('packing_list/packing_list_form', ['pl' => null]); ?>
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
