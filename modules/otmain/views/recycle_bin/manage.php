<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-bold tw-text-lg">
                            <?php echo e($title); ?>
                        </h4>
                        <p class="text-muted">
                            <?php echo sprintf(_l('otmain_recycle_bin_help'), (int) $purge_days); ?>
                        </p>

                        <div class="table-responsive mtop15">
                            <table class="table table-bordered table-hover dt-table" data-order-col="4" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('otmain_recycle_bin_item_name'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_item_type'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_related'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_deleted_by'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_deleted_at'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_days_remaining'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($items)) { ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <?php echo _l('otmain_recycle_bin_empty'); ?>
                                        </td>
                                    </tr>
                                    <?php } else {
                                        foreach ($items as $item) {
                                            $binType = $item['bin_type'] ?? 'file';
                                            $docType = $item['doc_type'] ?? '';
                                            $itemId  = (int) ($item['id'] ?? 0);
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if ($binType === 'file' && !empty($item['attachment_key'])) { ?>
                                            <a href="<?php echo site_url('download/file/sales_attachment/' . $item['attachment_key']); ?>" target="_blank">
                                                <i class="fa fa-paperclip"></i> <?php echo e($item['display_name']); ?>
                                            </a>
                                            <?php } elseif ($binType === 'document') { ?>
                                                <i class="fa fa-file-text-o"></i> <?php echo e($item['display_name']); ?>
                                            <?php } else {
                                                echo e($item['display_name']);
                                            } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $labelClass = 'label-default';
                                            $dtype = $item['doc_type'] ?? ($item['rel_type'] ?? '');
                                            if (in_array($dtype, ['proposal', 'estimate'])) {
                                                $labelClass = 'label-info';
                                            } elseif ($dtype === 'invoice') {
                                                $labelClass = 'label-success';
                                            } elseif ($dtype === 'credit_note') {
                                                $labelClass = 'label-warning';
                                            } elseif (in_array($dtype, ['packing_list', 'purchase_order'])) {
                                                $labelClass = 'label-primary';
                                            }
                                            ?>
                                            <span class="label <?php echo $labelClass; ?>"><?php echo e($item['type_label']); ?></span>
                                        </td>
                                        <td><?php echo e($item['related_label']); ?></td>
                                        <td><?php echo e($item['deleted_by_name'] ?: '-'); ?></td>
                                        <td data-order="<?php echo e($item['deleted_at']); ?>">
                                            <?php echo e(_dt($item['deleted_at'])); ?>
                                        </td>
                                        <td>
                                            <?php
                                            $days = (int) $item['days_remaining'];
                                            $class = $days <= 3 ? 'text-danger' : ($days <= 7 ? 'text-warning' : '');
                                            echo '<span class="' . $class . '">' . $days . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                                <?php
                                                $restoreUrl = admin_url('otmain/recycle_bin/restore/' . $itemId . '?type=' . urlencode($binType) . '&doc_type=' . urlencode($docType));
                                                $deleteUrl  = admin_url('otmain/recycle_bin/permanently_delete/' . $itemId . '?type=' . urlencode($binType) . '&doc_type=' . urlencode($docType));
                                                ?>
                                                <a href="<?php echo $restoreUrl; ?>"
                                                   class="tw-text-success"
                                                   title="<?php echo _l('otmain_recycle_bin_restore'); ?>">
                                                    <i class="fa fa-undo"></i>
                                                </a>
                                                <a href="<?php echo $deleteUrl; ?>"
                                                   class="tw-text-danger _delete"
                                                   title="<?php echo _l('otmain_recycle_bin_permanent_delete'); ?>">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
    // Force Sales sidebar to stay expanded on Recycle Bin page
    var $binLink = $('#side-menu').find('li.sub-menu-item-otmain-recycle-bin > a');
    if ($binLink.length) {
        $binLink.parents('li').addClass('active');
        $binLink.parents('ul.nav-second-level').addClass('in').prop('aria-expanded', true);
        $binLink.parents('li').find('> a:first').prop('aria-expanded', true);
    }
});
</script>
</body>
</html>
