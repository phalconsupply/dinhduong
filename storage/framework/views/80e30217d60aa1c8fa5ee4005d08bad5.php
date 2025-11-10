
<!doctype html>
<html lang="en" dir="ltr">

<head>
    <?php echo $__env->make('admin.layouts.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>

<body>
<!-- Loader -->
<!-- <div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
</div> -->
<!-- Loader -->
<?php echo $__env->make('admin.layouts.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('content'); ?>
<!-- javascript -->
<!-- JAVASCRIPT -->
<script src="<?php echo e(asset('admin-assets/js/jquery-1.12.4.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/feather-icons/feather.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/simplebar/simplebar.min.js')); ?>"></script>
<!-- Main Js -->
<script src="<?php echo e(asset('admin-assets/js/plugins.init.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('foot'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/layouts/app-single.blade.php ENDPATH**/ ?>