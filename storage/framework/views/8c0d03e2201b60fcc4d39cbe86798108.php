
<?php if(\Session::has('success')): ?>
    <div class="alert-top alert alert-success alert-dismissible fade show" role="alert">
        <?php echo \Session::get('success'); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
<?php endif; ?>
<?php if(\Session::has('warning')): ?>
    <div class="alert-top alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo \Session::get('warning'); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert-top alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo implode('', $errors->all(':message<br>')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/layouts/alert.blade.php ENDPATH**/ ?>