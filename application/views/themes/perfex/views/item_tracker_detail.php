<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.item-status-pending,.quote-status-pending{background:#fbbf24;color:#000;}
.item-status-ordered,.quote-status-in_progress{background:#3b82f6;color:#fff;}
.item-status-eta{background:#a855f7;color:#fff;}
.item-status-quality_check{background:#9ca3af;color:#fff;}
.item-status-received,.quote-status-ready_for_shipment{background:#22c55e;color:#fff;}
.quote-status-shipped{background:#6b7280;color:#fff;}
.otmain-status-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;line-height:1.4;}
</style>
<div class="tw-mb-3">
    <a href="<?= site_url('otmain/item_tracker_client'); ?>" class="btn btn-default btn-sm">
        <i class="fa fa-arrow-left"></i> <?= _l('otmain_item_tracker'); ?>
    </a>
</div>

<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-heading">
    <?= e(format_proposal_number($proposal->id)); ?>
    <?php if (!empty($proposal->subject)) { ?>
        <span class="tw-text-neutral-500 tw-font-normal">— <?= e($proposal->subject); ?></span>
    <?php } ?>
</h4>

<div class="panel_s">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-8">
                <p class="tw-mb-1">
                    <strong><?= _l('date'); ?>:</strong> <?= e(_d($proposal->date)); ?>
                </p>
                <?php if (!empty($invoice)) { ?>
                <p class="tw-mb-1">
                    <strong><?= _l('otmain_related_invoice'); ?>:</strong>
                    <?= e(format_invoice_number($invoice->id)); ?>
                </p>
                <?php } ?>
                <p class="tw-mb-1">
                    <strong><?= _l('otmain_progress'); ?>:</strong>
                    <?= e(($progress['received'] ?? 0) . '/' . ($progress['total'] ?? 0) . ' ' . _l('otmain_status_received')); ?>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <?= otmain_format_quotation_status($proposal->quotation_status ?: 'pending'); ?>
            </div>
        </div>

        <hr>

        <?php if (empty($items)) { ?>
        <p class="text-muted"><?= _l('otmain_tracker_no_items'); ?></p>
        <?php } else { ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th><?= _l('estimate_table_item_heading'); ?></th>
                        <th width="10%"><?= _l('estimate_table_quantity_heading'); ?></th>
                        <th width="18%"><?= _l('otmain_item_status'); ?></th>
                        <th width="14%"><?= _l('otmain_eta_date'); ?></th>
                        <th width="22%"><?= _l('otmain_notes'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($items as $item) { ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td>
                            <strong><?= e($item['description']); ?></strong>
                            <?php if (!empty($item['long_description'])) { ?>
                            <br><span class="text-muted"><?= clear_textarea_breaks($item['long_description']); ?></span>
                            <?php } ?>
                        </td>
                        <td>
                            <?= e(floatval($item['qty'])); ?>
                            <?php if (!empty($item['unit'])) { ?>
                                <?= e($item['unit']); ?>
                            <?php } ?>
                        </td>
                        <td><?= otmain_format_item_status($item['item_status']); ?></td>
                        <td>
                            <?php if ($item['item_status'] === 'eta' || !empty($item['eta_date'])) { ?>
                                <?= !empty($item['eta_date']) ? e(_d($item['eta_date'])) : '-'; ?>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                        <td><?= !empty($item['notes']) ? nl2br(e($item['notes'])) : '-'; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>
