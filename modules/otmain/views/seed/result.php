<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="tw-mt-0 tw-font-bold tw-text-lg"><?php echo e($title); ?></h4>
                        <?php if ($result['status'] === 'success') { ?>
                        <div class="alert alert-success"><?php echo e($result['message']); ?></div>
                        <?php } elseif ($result['status'] === 'skipped') { ?>
                        <div class="alert alert-info"><?php echo e($result['message']); ?></div>
                        <?php } ?>

                        <p>Link data production seed:</p>
                        <ul class="list-unstyled tw-space-y-2">
                            <li><a href="<?php echo $result['links']['clients']; ?>">Customers / Clients</a></li>
                            <li><a href="<?php echo $result['links']['proposal']; ?>">Quotation — TP Suction Hose</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker_detail']; ?>">Item Tracker — TP Suction Hose</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker']; ?>">Item Tracker list</a></li>
                            <li><a href="<?php echo $result['links']['packing']; ?>">Packing Lists</a></li>
                            <li><a href="<?php echo $result['links']['purchase_orders']; ?>">Purchase Orders</a></li>
                        </ul>

                        <hr />
                        <p class="text-muted">
                            <code>?force=1</code> — ganti hanya dokumen yang pernah di-seed (tracked di <code>otmain_seed_document_ids</code>). Customers di-upsert; dokumen manual lain aman.
                        </p>
                        <p class="text-muted">
                            <code>?reset=1</code> — hapus <strong>semua</strong> proposal, packing list, purchase order, dan item tracker dulu (customers tetap), reset nomor PL/PO, lalu seed ulang.
                            URL: <a href="<?php echo admin_url('otmain/seed?reset=1'); ?>"><?php echo admin_url('otmain/seed?reset=1'); ?></a>
                            (atau <code>?force=1&amp;reset=1</code> — sama saja; <code>reset</code> sudah imply force).
                        </p>
                        <p class="text-muted">
                            Seed ulang tracked saja: <a href="<?php echo admin_url('otmain/seed?force=1'); ?>">?force=1</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
