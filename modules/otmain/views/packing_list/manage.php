<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (staff_can('create', 'otmain_packing_list')) { ?>
                <div class="tw-mb-2">
                    <a href="<?php echo admin_url('otmain/packing_list/packing_list'); ?>" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('otmain_new_packing_list'); ?>
                    </a>
                </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        render_datatable([
                            _l('otmain_packing_list') . ' #',
                            _l('date'),
                            _l('client'),
                            _l('otmain_vessel'),
                            _l('otmain_quote_reference'),
                        ], 'otmain-packing-lists', [], ['id' => 'otmain-packing-lists']);
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
    initDataTable('.table-otmain-packing-lists', admin_url + 'otmain/packing_list/table', undefined, undefined, {}, [[1, 'desc'], [0, 'desc']]);
});
</script>
</body>
</html>
