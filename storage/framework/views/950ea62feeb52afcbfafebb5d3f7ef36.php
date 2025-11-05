
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

<div class="page-wrapper toggled">
    <!-- sidebar-wrapper -->
    <?php echo $__env->make('admin.layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- sidebar-wrapper  -->

    <!-- Start Page Content -->
    <main class="page-content bg-light">
        <!-- Top Header -->
        <?php echo $__env->make('admin.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- Top Header -->
        <?php echo $__env->make('admin.layouts.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
        <!-- Footer -->
        <?php echo $__env->make('admin.layouts.modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('admin.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- Footer -->
    </main>
    <!--End page-content" -->
</div>

<!-- javascript -->
<!-- JAVASCRIPT -->
<script src="<?php echo e(asset('admin-assets/js/jquery-1.12.4.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/feather-icons/feather.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/simplebar/simplebar.min.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/tiny-slider/min/tiny-slider.js')); ?>"></script>
<script src="<?php echo e(asset('admin-assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Main Js -->
<script src="<?php echo e(asset('admin-assets/js/plugins.init.js')); ?>"></script>
<script>
    function showToast(type = 'success', message = 'Thao tác thành công') {
        $.toast({
            heading: type === 'success' ? 'Thành công' : 'Lỗi',
            text: message,
            icon: type,
            position: 'top-right',
            loaderBg: type === 'success' ? '#51d28c' : '#f2a654'
        });
    }

    // Laravel FileManager - Thay thế CKFinder
    function selectFileWithCKFinder(elementId) {
        // Giữ tên function cũ để không phải sửa nhiều chỗ
        selectFileWithLFM(elementId, 'image');
    }

    function selectFileWithLFM(id, type) {
        var route_prefix = '/laravel-filemanager';
        var target_input = $('#' + id);
        var target_preview = $('#preview-' + id);
        
        window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
        
        window.SetUrl = function (items) {
            var file_path = items.map(function (item) {
                return item.url;
            }).join(',');
            
            // Set value to input
            target_input.val(file_path).trigger('change');
            
            // Show image preview if element exists
            if (target_preview.length) {
                target_preview.attr('src', file_path);
            }
        };
    }

</script>
<script src="<?php echo e(asset('admin-assets/js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('foot'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/layouts/app-full.blade.php ENDPATH**/ ?>