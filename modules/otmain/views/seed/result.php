<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php
$summary    = isset($result['summary']) && is_array($result['summary']) ? $result['summary'] : null;
$categories = is_array($summary) ? ($summary['categories'] ?? []) : [];
$repairStats = isset($result['stats']) && is_array($result['stats']) ? $result['stats'] : null;
?>
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

                        <?php if ($repairStats) { ?>
                        <div class="alert alert-warning">
                            Repair detail:
                            customers <?php echo (int) ($repairStats['customers_upserted'] ?? 0); ?>,
                            proposals <?php echo (int) ($repairStats['proposals_synced'] ?? 0); ?>,
                            trackers resynced <?php echo (int) ($repairStats['trackers_synced'] ?? 0); ?>,
                            packing updated <?php echo (int) ($repairStats['packing_updated'] ?? 0); ?>,
                            invoice updated <?php echo (int) ($repairStats['invoice_updated'] ?? 0); ?>,
                            PO updated <?php echo (int) ($repairStats['po_updated'] ?? 0); ?>
                            <?php if (!empty($repairStats['missing_proposals'])) { ?>
                            <br />Missing proposals:
                            <?php echo e(implode(', ', $repairStats['missing_proposals'])); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <?php if ($categories !== []) { ?>
                        <h5 class="bold mtop20">Hasil seed vs data non-seed</h5>
                        <p class="text-muted">
                            Marker kode: <code><?php echo e($summary['marker'] ?? ''); ?></code>
                            · Marker DB: <code><?php echo e($summary['marker_db'] ?? ''); ?></code>
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th class="text-right">Seed</th>
                                        <th class="text-right">Non-seed</th>
                                        <th class="text-right">Total DB</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat) { ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($cat['link'])) { ?>
                                            <a href="<?php echo $cat['link']; ?>"><?php echo e($cat['label']); ?></a>
                                            <?php } else { ?>
                                            <?php echo e($cat['label']); ?>
                                            <?php } ?>
                                        </td>
                                        <td class="text-right"><strong><?php echo (int) ($cat['seed'] ?? 0); ?></strong></td>
                                        <td class="text-right"><?php echo (int) ($cat['non_seed'] ?? 0); ?></td>
                                        <td class="text-right"><?php echo (int) ($cat['total'] ?? 0); ?></td>
                                        <td class="text-muted"><?php echo e($cat['note'] ?? ''); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted mtop10">
                            <strong>Seed</strong> = dokumen ter-track seed / company di catalog.
                            <strong>Non-seed</strong> = data lain di database (manual atau luar catalog) — tidak dihapus oleh seed.
                        </p>
                        <?php } ?>

                        <h5 class="bold mtop25">Link data production seed</h5>
                        <ul class="list-unstyled tw-space-y-2">
                            <li><a href="<?php echo $result['links']['clients']; ?>">Customers / Clients</a></li>
                            <li><a href="<?php echo $result['links']['proposals']; ?>">Quotations (Proposals) — list</a></li>
                            <li><a href="<?php echo $result['links']['proposal']; ?>">Quotation — sample (TP Suction Hose / last seed)</a></li>
                            <li><a href="<?php echo $result['links']['invoices']; ?>">Invoices — list</a></li>
                            <li><a href="<?php echo $result['links']['invoice']; ?>">Invoice — sample (last seed)</a></li>
                            <li><a href="<?php echo $result['links']['packing']; ?>">Packing Lists</a></li>
                            <li><a href="<?php echo $result['links']['purchase_orders']; ?>">Purchase Orders</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker']; ?>">Item Tracker list</a></li>
                            <li><a href="<?php echo $result['links']['item_tracker_detail']; ?>">Item Tracker — sample quotation</a></li>
                        </ul>

                        <hr />
                        <p class="text-muted">
                            Seed <strong>tidak pernah</strong> menghapus semua data di database. Customers di-upsert.
                            Proposal/manual aman: seed hanya menghapus dokumen bertanda seed
                            (<code>source_quote_number</code> / subject catalog seed / ID tracked).
                        </p>
                        <p class="text-muted">
                            Endpoint: <code><?php echo admin_url('otmain/seed'); ?></code>
                        </p>
                        <p class="text-muted">
                            <code>?force=1</code> — recreate seed docs + bersihkan orphan
                            (<a href="<?php echo admin_url('otmain/seed?force=1'); ?>">jalankan</a>).
                            <br />
                            <code>?repair=1</code> — upsert customers + re-link packing/invoice/PO → proposal + <strong>resync all item trackers</strong> (seed &amp; non-seed)
                            (<a href="<?php echo admin_url('otmain/seed?repair=1'); ?>">jalankan</a>).
                            <br />
                            <code>?customers=1</code> — upsert catalog <code>customers.php</code> saja
                            (<a href="<?php echo admin_url('otmain/seed?customers=1'); ?>">jalankan</a>).
                            <br />
                            <code>?resync_tracker=1</code> — resync Item Tracker dari line items proposal (semua proposal yang punya tracker, termasuk non-seed)
                            (<a href="<?php echo admin_url('otmain/seed?resync_tracker=1'); ?>">jalankan</a>).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
