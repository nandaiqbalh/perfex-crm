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

                        <p>Gunakan link di bawah untuk melihat data demo yang sudah dibuat:</p>
                        <ul class="list-unstyled tw-space-y-2">
                            <li><a href="<?php echo $result['links']['clients']; ?>">Customers / Clients (47 relations)</a></li>
                            <li><a href="<?php echo $result['links']['proposal']; ?>">Quotation / Proposal demo</a></li>
                            <li><a href="<?php echo $result['links']['invoice']; ?>">Invoice demo</a></li>
                            <li><a href="<?php echo $result['links']['packing_edit']; ?>">Packing List demo (Commercial Invoice)</a></li>
                            <li><a href="<?php echo $result['links']['purchase_order_edit']; ?>">Purchase Order demo</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker_detail']; ?>">Item Tracker demo (Kovako M120)</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker_tp']; ?>">Item Tracker — TP Company Limited (Suction Nozzle)</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker']; ?>">Item Tracker list</a></li>
                        </ul>

                        <hr />
                        <p class="text-muted">
                            Seed akan <strong>hapus semua customers + dokumen terkait</strong> lalu insert data baru.
                            Untuk seed ulang: <a href="<?php echo admin_url('otmain/seed?force=1'); ?>">Seed ulang (?force=1)</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
