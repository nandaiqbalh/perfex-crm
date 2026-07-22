<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!class_exists('Invoices_model', false)) {
    get_instance()->load->model('invoices_model');
}

$statuses = get_instance()->invoices_model->get_statuses();
$locked   = !empty($invoice->status_locked);
$btnLabel = _l('otmain_invoice_status');
if ($locked) {
    $btnLabel .= ' · ' . _l('otmain_invoice_status_manual');
}
?>
<div class="btn-group mright5">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false"
        title="<?= e(_l('otmain_invoice_status_override_help')); ?>">
        <?= e($btnLabel); ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-right">
        <?php foreach ($statuses as $statusId) { ?>
        <li<?= ((int) $invoice->status === (int) $statusId) ? ' class="active"' : ''; ?>>
            <a href="<?= admin_url('otmain/otmain/set_invoice_status/' . (int) $invoice->id . '?status=' . (int) $statusId); ?>"
                <?= ((int) $invoice->status === (int) $statusId && $locked) ? 'onclick="return false;"' : ''; ?>>
                <?= e(format_invoice_status($statusId, '', false)); ?>
                <?php if ((int) $invoice->status === (int) $statusId) { ?>
                <i class="fa fa-check text-success tw-ml-1"></i>
                <?php } ?>
            </a>
        </li>
        <?php } ?>
        <li role="separator" class="divider"></li>
        <li<?= !$locked ? ' class="active"' : ''; ?>>
            <a href="<?= admin_url('otmain/otmain/set_invoice_status/' . (int) $invoice->id . '?automatic=1'); ?>">
                <?= e(_l('otmain_invoice_status_automatic')); ?>
                <?php if (!$locked) { ?>
                <i class="fa fa-check text-success tw-ml-1"></i>
                <?php } ?>
            </a>
        </li>
    </ul>
</div>
