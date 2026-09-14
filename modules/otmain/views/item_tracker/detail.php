<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
/* Item Tracker Detail — responsive table with horizontal scroll */
.item-tracker-table-wrap {
    overflow-x: auto !important;
    overflow-y: visible;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    margin-top: 20px;
    width: 100%;
}
.item-tracker-table-wrap::-webkit-scrollbar {
    height: 12px;
    background: #f1f5f9;
}
.item-tracker-table-wrap::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 6px;
    border: 2px solid #f1f5f9;
}
.item-tracker-table-wrap::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}
.item-tracker-table-wrap .table {
    margin-bottom: 0;
    width: 2100px;
}
.item-tracker-table-wrap .table th,
.item-tracker-table-wrap .table td {
    vertical-align: middle;
}
/* Prevent selectpicker dropdowns from being clipped */
.item-tracker-table-wrap .bootstrap-select .dropdown-menu {
    z-index: 1050;
}
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2">
                    <a href="<?php echo admin_url('otmain/item_tracker'); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left tw-mr-1"></i> <?php echo _l('otmain_item_tracker'); ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body" style="overflow-x: visible;">
                        <h4 class="tw-mt-0 tw-font-bold tw-text-lg">
                            <?php echo e($title); ?>
                        </h4>

                        <div class="row mtop15">
                            <div class="col-md-6">
                                <p><strong><?php echo _l('client'); ?>:</strong>
                                    <?php
                                    if ($proposal->rel_type === 'customer') {
                                        echo e(get_company_name($proposal->rel_id));
                                    } else {
                                        echo e($proposal->proposal_to ?: '-');
                                    }
                                    ?>
                                </p>
                                <p><strong><?php echo _l('date'); ?>:</strong> <?php echo e(_d($proposal->date)); ?></p>
                                <p><strong><?php echo _l('proposal_total'); ?>:</strong>
                                    <?php echo e(app_format_money($proposal->total, $currency)); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($invoice)) { ?>
                                <p><strong><?php echo _l('otmain_related_invoice'); ?>:</strong>
                                    <a href="<?php echo admin_url('invoices/list_invoices/' . $invoice->id); ?>">
                                        <?php echo e(format_invoice_number($invoice->id)); ?>
                                    </a>
                                </p>
                                <?php } ?>
                                <?php if (empty($no_tracker)) { ?>
                                <p><strong><?php echo _l('otmain_progress'); ?>:</strong>
                                    <?php echo e(($progress['received'] ?? 0) . '/' . ($progress['total'] ?? 0) . ' ' . _l('otmain_status_received')); ?>
                                </p>
                                <?php } ?>
                            </div>
                        </div>

                        <?php if (!empty($no_tracker)) { ?>
                            <div class="alert alert-warning mtop20">
                                <?php echo _l('otmain_tracker_no_items'); ?>
                                <?php if (staff_can('edit', 'otmain_item_tracker') && (int) $proposal->status === 3) { ?>
                                    <a href="<?php echo admin_url('otmain/item_tracker/backfill/' . $proposal->id); ?>" class="btn btn-primary btn-sm tw-ml-2">
                                        <?php echo _l('otmain_generate_tracker'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } else { ?>

                        <?php echo form_open(admin_url('otmain/item_tracker/detail/' . $proposal->id)); ?>

                        <div class="row mtop15">
                            <div class="col-md-4">
                                <label for="quotation_status"><?php echo _l('otmain_quotation_status'); ?></label>
                                <select name="quotation_status" id="quotation_status" class="selectpicker" data-width="100%"
                                    <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                    <?php
                                    $currentQStatus = $proposal->quotation_status ?: 'pending';
                                    foreach ($quotation_statuses as $key => $label) { ?>
                                    <option value="<?php echo e($key); ?>" <?php echo $currentQStatus === $key ? 'selected' : ''; ?>>
                                        <?php echo e($label); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                                <div class="mtop10">
                                    <?php echo otmain_format_quotation_status($currentQStatus); ?>
                                </div>
                            </div>
                            <div class="col-md-8 text-right">
                                <?php if (staff_can('edit', 'otmain_item_tracker')) { ?>
                                <button type="submit" class="btn btn-primary mtop25">
                                    <i class="fa fa-check tw-mr-1"></i> <?php echo _l('otmain_save_all'); ?>
                                </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="item-tracker-table-wrap">
                            <table class="table table-bordered items">
                                <thead>
                                    <tr>
                                        <th style="width: 40px; text-align: center;">#</th>
                                        <th style="min-width: 200px;"><?php echo _l('estimate_table_item_heading'); ?></th>
                                        <th style="width: 65px; text-align: center;"><?php echo _l('estimate_table_quantity_heading'); ?></th>
                                        <th style="width: 60px; text-align: center;"><?php echo _l('unit'); ?></th>
                                        <th style="width: 90px; text-align: right;"><?php echo _l('estimate_table_rate_heading'); ?></th>
                                        <th style="min-width: 130px;"><?php echo _l('otmain_item_status'); ?></th>
                                        <th style="min-width: 180px;"><?php echo _l('otmain_vendor'); ?></th>
                                        <th style="min-width: 140px;"><?php echo _l('otmain_vendor_invoice_number'); ?></th>
                                        <th style="min-width: 140px;"><?php echo _l('otmain_vendor_payment_status'); ?></th>
                                        <th style="min-width: 140px;"><?php echo _l('otmain_eta_date'); ?></th>
                                        <th style="min-width: 210px;"><?php echo _l('otmain_notes'); ?></th>
                                        <th style="min-width: 210px;"><?php echo _l('otmain_admin_notes'); ?></th>
                                        <th style="min-width: 130px;"><?php echo _l('otmain_last_updated'); ?></th>
                                        <?php if (staff_can('delete', 'otmain_item_tracker')) { ?>
                                        <th style="width: 45px; text-align: center;"></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($items as $item) {
                                        $itemId = (int) $item['id'];
                                        ?>
                                    <tr>
                                        <td class="text-center" style="width: 40px;"><?php echo $i++; ?></td>
                                        <td style="min-width: 200px;">
                                            <strong><?php echo e($item['description']); ?></strong>
                                            <?php if (!empty($item['long_description'])) { ?>
                                            <br><span class="text-muted"><?php echo clear_textarea_breaks($item['long_description']); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center" style="width: 65px;"><?php echo e(floatval($item['qty'])); ?></td>
                                        <td class="text-center" style="width: 60px;"><?php echo e($item['unit']); ?></td>
                                        <td class="text-right" style="width: 90px; white-space: nowrap;"><?php echo e(app_format_money($item['rate'], $currency)); ?></td>
                                        <td style="min-width: 130px;">
                                            <select name="items[<?php echo $itemId; ?>][item_status]"
                                                    class="selectpicker otmain-item-status"
                                                    data-width="100%"
                                                    data-item-id="<?php echo $itemId; ?>"
                                                    <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                                <?php foreach ($item_statuses as $key => $label) { ?>
                                                <option value="<?php echo e($key); ?>" <?php echo $item['item_status'] === $key ? 'selected' : ''; ?>>
                                                    <?php echo e($label); ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td style="min-width: 180px;">
                                            <select name="items[<?php echo $itemId; ?>][supplier_id]"
                                                    id="supplier_id_<?php echo $itemId; ?>"
                                                    class="ajax-search otmain-item-supplier"
                                                    data-width="100%"
                                                    data-live-search="true"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                    <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                                <?php if (!empty($item['supplier_id'])) { ?>
                                                <option value="<?php echo (int) $item['supplier_id']; ?>" selected>
                                                    <?php echo e(get_company_name($item['supplier_id'])); ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <input type="text"
                                                   name="items[<?php echo $itemId; ?>][vendor_invoice_number]"
                                                   class="form-control"
                                                   value="<?php echo e($item['vendor_invoice_number'] ?? ''); ?>"
                                                   <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <select name="items[<?php echo $itemId; ?>][vendor_payment_status]"
                                                    class="selectpicker"
                                                    data-width="100%"
                                                    <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                                <?php
                                                $currentVp = $item['vendor_payment_status'] ?? 'unpaid';
                                                foreach ($vendor_payment_statuses as $vpKey => $vpLabel) { ?>
                                                <option value="<?php echo e($vpKey); ?>" <?php echo $currentVp === $vpKey ? 'selected' : ''; ?>>
                                                    <?php echo e($vpLabel); ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td style="min-width: 140px;">
                                            <div class="input-group date">
                                                <input type="text"
                                                       name="items[<?php echo $itemId; ?>][eta_date]"
                                                       class="form-control datepicker otmain-eta-date"
                                                       value="<?php echo !empty($item['eta_date']) ? e(_d($item['eta_date'])) : ''; ?>"
                                                       <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>>
                                                <div class="input-group-addon">
                                                    <i class="fa-regular fa-calendar calendar-icon"></i>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="min-width: 210px;">
                                            <textarea name="items[<?php echo $itemId; ?>][notes]"
                                                      class="form-control"
                                                      rows="2"
                                                      <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>><?php echo e($item['notes'] ?? ''); ?></textarea>
                                        </td>
                                        <td style="min-width: 210px;">
                                            <textarea name="items[<?php echo $itemId; ?>][admin_notes]"
                                                      class="form-control"
                                                      rows="2"
                                                      <?php echo staff_cant('edit', 'otmain_item_tracker') ? 'disabled' : ''; ?>><?php echo e($item['admin_notes'] ?? ''); ?></textarea>
                                        </td>
                                        <td style="min-width: 130px; white-space: nowrap;">
                                            <?php if (!empty($item['dateupdated'])) { ?>
                                                <?php echo e($item['updated_by_name'] ?: '-'); ?><br>
                                                <small class="text-muted"><?php echo e(_dt($item['dateupdated'])); ?></small>
                                            <?php } else { ?>
                                                -
                                            <?php } ?>
                                        </td>
                                        <?php if (staff_can('delete', 'otmain_item_tracker')) { ?>
                                        <td class="text-center" style="width: 45px;">
                                            <a href="<?php echo admin_url('otmain/item_tracker/delete_item/' . $itemId); ?>"
                                               class="text-danger _delete"
                                               title="<?php echo _l('delete'); ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>

                        <?php echo form_close(); ?>

                        <div class="mtop30">
                            <h4 class="bold tw-mb-3"><?php echo _l('otmain_attachments'); ?></h4>
                            <p class="text-muted"><?php echo _l('otmain_attach_documents_help'); ?></p>

                            <?php if (staff_can('edit', 'otmain_item_tracker')) { ?>
                            <?php echo form_open_multipart(
                                admin_url('otmain/item_tracker/upload_attachment/' . $proposal->id),
                                ['id' => 'otmain-item-tracker-upload', 'class' => 'dropzone mbot15']
                            ); ?>
                            <input type="file" name="file" multiple />
                            <?php echo form_close(); ?>
                            <?php } ?>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="otmain-item-tracker-attachments">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('otmain_file_name'); ?></th>
                                            <th><?php echo _l('otmain_file_type'); ?></th>
                                            <th><?php echo _l('otmain_file_date'); ?></th>
                                            <th style="width: 80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($attachments)) { ?>
                                        <tr class="otmain-no-attachments-row">
                                            <td colspan="4" class="text-center text-muted"><?php echo _l('otmain_no_attachments'); ?></td>
                                        </tr>
                                        <?php } else {
                                            foreach ($attachments as $attachment) { ?>
                                        <tr data-attachment-id="<?php echo (int) $attachment['id']; ?>">
                                            <td>
                                                <a href="<?php echo site_url('download/file/sales_attachment/' . $attachment['attachment_key']); ?>" target="_blank">
                                                    <?php echo e($attachment['file_name']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo e($attachment['filetype']); ?></td>
                                            <td><?php echo e(_dt($attachment['dateadded'])); ?></td>
                                            <td class="text-center">
                                                <?php if (staff_can('delete', 'otmain_item_tracker') || staff_can('edit', 'otmain_item_tracker')) { ?>
                                                <a href="<?php echo admin_url('otmain/item_tracker/delete_attachment/' . $attachment['id']); ?>"
                                                   class="text-danger _delete"
                                                   title="<?php echo _l('delete'); ?>">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    init_ajax_search('customer', '.otmain-item-supplier.ajax-search');

    if (typeof Dropzone !== 'undefined' && $('#otmain-item-tracker-upload').length) {
        Dropzone.autoDiscover = false;
        new Dropzone('#otmain-item-tracker-upload', appCreateDropzoneOptions({
            paramName: 'file',
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    function validateEtaRows() {
        var ok = true;
        $('.otmain-item-status').each(function() {
            var $status = $(this);
            var $row = $status.closest('tr');
            var $eta = $row.find('.otmain-eta-date');
            if ($status.val() === 'eta' && !$eta.val()) {
                ok = false;
                $eta.addClass('has-error');
            } else {
                $eta.removeClass('has-error');
            }
        });
        return ok;
    }

    $('form').not('#otmain-item-tracker-upload').on('submit', function(e) {
        if (!validateEtaRows()) {
            e.preventDefault();
            alert_float('danger', '<?php echo _l('otmain_eta_date_required'); ?>');
            return false;
        }
    });
});
</script>
</body>
</html>
