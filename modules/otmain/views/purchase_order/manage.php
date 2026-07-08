<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('create', 'otmain_purchase_order')) { ?>
                <div class="tw-mb-2">
                    <a href="<?php echo admin_url('otmain/purchase_order/purchase_order'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('otmain_new_purchase_order'); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        render_datatable([
                            _l('otmain_purchase_order') . ' #',
                            _l('date'),
                            _l('client'),
                            _l('total'),
                            _l('otmain_supplier_quote_ref'),
                        ], 'otmain-purchase-orders', [], ['id' => 'otmain-purchase-orders']);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    initDataTable('.table-otmain-purchase-orders', admin_url + 'otmain/purchase_order/table', undefined, undefined, {}, [[1, 'desc'], [0, 'desc']]);
});
</script>
</body>
</html>
