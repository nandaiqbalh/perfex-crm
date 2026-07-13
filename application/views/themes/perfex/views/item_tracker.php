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
<h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700 section-heading">
    <?= _l('otmain_item_tracker'); ?>
</h4>
<div class="panel_s">
    <div class="panel-body">
        <?php if (empty($trackers)) { ?>
        <p class="text-muted"><?= _l('otmain_tracker_no_items'); ?></p>
        <?php } else { ?>
        <div class="table-responsive">
            <table class="table dt-table table-item-trackers" data-order-col="1" data-order-type="desc">
                <thead>
                    <tr>
                        <th><?= _l('proposal') . ' #'; ?></th>
                        <th><?= _l('date'); ?></th>
                        <th><?= _l('otmain_quotation_status'); ?></th>
                        <th><?= _l('otmain_progress'); ?></th>
                        <th><?= _l('invoice'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trackers as $tracker) {
                        $detailUrl = site_url('otmain/item_tracker_client/detail/' . $tracker['id']);
                        ?>
                    <tr>
                        <td>
                            <a href="<?= $detailUrl; ?>" class="tw-font-medium">
                                <?= e(format_proposal_number($tracker['id'])); ?>
                            </a>
                            <?php if (!empty($tracker['subject'])) { ?>
                            <br><span class="text-muted"><?= e($tracker['subject']); ?></span>
                            <?php } ?>
                        </td>
                        <td data-order="<?= e($tracker['date']); ?>">
                            <?= e(_d($tracker['date'])); ?>
                        </td>
                        <td>
                            <?= otmain_format_quotation_status($tracker['quotation_status'] ?: 'pending'); ?>
                        </td>
                        <td>
                            <?= e((int) $tracker['item_received'] . '/' . (int) $tracker['item_total'] . ' ' . _l('otmain_status_received')); ?>
                        </td>
                        <td>
                            <?php if (!empty($tracker['invoice_id'])) { ?>
                                <?= e(format_invoice_number($tracker['invoice_id'])); ?>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>
