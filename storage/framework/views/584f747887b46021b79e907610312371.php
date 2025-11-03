
<?php if(\Session::has('success')): ?>
    <div class="alert-top alert alert-success alert-dismissible show" role="alert">
        <?php echo \Session::get('success'); ?>

    </div>
<?php endif; ?>
<?php if(\Session::has('warning')): ?>
    <div class="alert-top alert alert-warning alert-dismissible show" role="alert">
        <?php echo \Session::get('warning'); ?>

    </div>
<?php endif; ?>
<?php if($errors->any()): ?>
    <div class="alert-top alert alert-danger alert-dismissible show" role="alert">
        <?php echo implode('', $errors->all(':message<br>')); ?>

    </div>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/layouts/alert.blade.php ENDPATH**/ ?>