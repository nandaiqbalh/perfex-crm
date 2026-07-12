<?php defined('BASEPATH') or exit('No direct script access allowed');
$title = $title ?? '';
$id    = $id ?? '';
?>
<div class="otmain-form-section"<?php echo $id !== '' ? ' id="' . e($id) . '"' : ''; ?>>
    <h5 class="otmain-form-section-title"><?php echo e($title); ?></h5>
    <div class="otmain-form-section-body">
