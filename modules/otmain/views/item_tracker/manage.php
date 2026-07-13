<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 tw-flex tw-items-center tw-justify-between">
                    <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-mb-0">
                        <?php echo _l('otmain_item_tracker'); ?>
                    </h4>
                    <?php if (staff_can('edit', 'otmain_item_tracker')) { ?>
                    <a href="<?php echo admin_url('otmain/item_tracker/backfill_all'); ?>"
                       class="btn btn-default"
                       onclick="return confirm('<?php echo _l('confirm_action_prompt'); ?>');">
                        <i class="fa fa-sync tw-mr-1"></i>
                        <?php echo _l('otmain_generate_all_trackers'); ?>
                    </a>
                    <?php } ?>
                </div>

                <div class="row tw-mb-3">
                    <div class="col-md-3">
                        <select id="otmain-tracker-status-filter" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('otmain_quotation_status'); ?>">
                            <option value=""><?php echo _l('otmain_quotation_status'); ?> — <?php echo _l('all'); ?></option>
                            <?php foreach ($quotation_statuses as $key => $label) { ?>
                            <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php
                        render_datatable([
                            _l('proposal') . ' #',
                            _l('client'),
                            _l('date'),
                            _l('otmain_quotation_status'),
                            _l('otmain_progress'),
                            _l('invoice'),
                        ], 'otmain-item-trackers', [], ['id' => 'otmain-item-trackers']);
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
    var TrackerServerParams = {
        quotation_status: '[name="quotation_status"], #otmain-tracker-status-filter'
    };
    var table = initDataTable(
        '.table-otmain-item-trackers',
        admin_url + 'otmain/item_tracker/table',
        undefined,
        undefined,
        TrackerServerParams,
        [[2, 'desc'], [0, 'desc']]
    );

    $('#otmain-tracker-status-filter').on('changed.bs.select', function() {
        table.ajax.reload();
    });
});
</script>
</body>
</html>
