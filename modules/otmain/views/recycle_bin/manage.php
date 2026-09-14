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
                            <table class="table table-bordered table-hover dt-table" data-order-col="3" data-order-type="desc">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('otmain_file_name'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_related'); ?></th>
                                        <th><?php echo _l('otmain_file_type'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_deleted_at'); ?></th>
                                        <th><?php echo _l('otmain_recycle_bin_deleted_by'); ?></th>
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
                                        foreach ($items as $item) { ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($item['attachment_key'])) { ?>
                                            <a href="<?php echo site_url('download/file/sales_attachment/' . $item['attachment_key']); ?>" target="_blank">
                                                <?php echo e($item['file_name']); ?>
                                            </a>
                                            <?php } else {
                                                echo e($item['file_name']);
                                            } ?>
                                        </td>
                                        <td><?php echo e($item['related_label']); ?></td>
                                        <td><?php echo e($item['rel_type']); ?></td>
                                        <td data-order="<?php echo e($item['deleted_at']); ?>">
                                            <?php echo e(_dt($item['deleted_at'])); ?>
                                        </td>
                                        <td><?php echo e($item['deleted_by_name'] ?: '-'); ?></td>
                                        <td>
                                            <?php
                                            $days = (int) $item['days_remaining'];
                                            $class = $days <= 3 ? 'text-danger' : ($days <= 7 ? 'text-warning' : '');
                                            echo '<span class="' . $class . '">' . $days . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                                <a href="<?php echo admin_url('otmain/recycle_bin/restore/' . $item['id']); ?>"
                                                   class="tw-text-success"
                                                   title="<?php echo _l('otmain_recycle_bin_restore'); ?>">
                                                    <i class="fa fa-undo"></i>
                                                </a>
                                                <a href="<?php echo admin_url('otmain/recycle_bin/permanently_delete/' . $item['id']); ?>"
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
</body>
</html>
