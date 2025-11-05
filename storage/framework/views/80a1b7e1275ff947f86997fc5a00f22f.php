 <?php $__env->startSection('title'); ?> Tổng quan <?php $__env->stopSection(); ?> <?php $__env->startSection('body_class', 'home'); ?> <?php $__env->startSection('content'); ?> <div class="container-fluid">
    <div class="layout-specing">
        <h5 class="mb-0">Thống kê</h5>
        <form action="" method="GET">
        <div class="d-flex align-items-center gap-2 mt-2">
            <div class="form-group">
                <label class="small">Từ ngày:</label>
                <input name="from_date" class="form-control" value="<?php echo e(request()->get('from_date','')); ?>" type="date">
            </div>
            <div class="form-group">
                <label  class="small">Đến ngày:</label>
                <input name="to_date" class="form-control" value="<?php echo e(request()->get('to_date','')); ?>" type="date">
            </div>
            <div class="form-group">
                <label  class="small">Tỉnh/TP:</label>
                <select name="province_code" id="province_code" class="form-select form-control text-end" aria-label="Default select example">
                    <option value="">Tỉnh/thành phố</option>
                    <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($province->code); ?>" <?php if(request()->get('province_code') == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label  class="small">Quận huyện:</label>
                <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example">
                    <option value="">Quận/huyện</option>
                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($district->code); ?>" <?php if($district->code == request()->get('district_code')): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label  class="small">Phường xã:</label>
                <select name="ward_code" id="ward_code" class="form-select form-control text-end" aria-label="Default select example">
                    <option value="">Phường/xã</option>
                    <?php $__currentLoopData = $wards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ward->code); ?>" <?php if($ward->code == request()->get('ward_code')): ?> selected <?php endif; ?>><?php echo e($ward->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label  class="small">Dân tộc:</label>
                <select name="ethnic_id" id="ethnic_id" class="form-select form-control text-end" aria-label="Default select example">
                    <option value="all" <?php if(request()->get('ethnic_id') == 'all'): ?> selected <?php endif; ?>>Tất cả</option>
                    <option value="ethnic_minority" <?php if(request()->get('ethnic_id') == 'ethnic_minority'): ?> selected <?php endif; ?>>Tất cả dân tộc thiểu số</option>
                    <?php $__currentLoopData = $ethnics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ethnic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ethnic->id); ?>" <?php if($ethnic->id == request()->get('ethnic_id')): ?> selected <?php endif; ?>><?php echo e($ethnic->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary text-white">Lọc</button>
                    <button type="reset" class="btn btn-warning text-white" onclick="resetForm()">Làm mới</button>
                </div>
            </div>

        </form>
        </div>
        <?php echo $__env->make('admin.dashboards.sections.count', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--end row-->
        <?php echo $__env->make('admin.dashboards.sections.bieu-do-theo-nam', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--end row-->
        <div class="row">
            <!--end col-->
            <div class="col-xl-12 mt-4">
                <?php echo $__env->make('admin.dashboards.sections.khao-sat-moi', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('foot'); ?>
    <script>
        function resetForm() {
            window.location.href = "<?php echo e(route('admin.dashboard.index')); ?>";
        }
        $(document).ready(function() {

            // Khi có thay đổi trong select province
            $('#province_code').change(function() {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '<?php echo e(route('admin.ajax_get_district_by_province')); ?>', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { province_code: province_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var districtSelect = $('#district_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="">Chọn quận huyện</option>');
                        $.each(response.districts, function(key, value) {
                            districtSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });

                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi xảy ra trong yêu cầu Ajax
                        console.log(error);
                    }
                });
            });

            $(document).on('change','#district_code',function() {
                var district_code = $(this).val(); // Lấy giá trị province id được chọn
                console.log(district_code)
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '<?php echo e(route('admin.ajax_get_ward_by_district')); ?>', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { district_code: district_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        wardSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        wardSelect.append('<option value="">Chọn phường xã</option>');
                        $.each(response.wards, function(key, value) {
                            wardSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi xảy ra trong yêu cầu Ajax
                        console.log(error);
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/dashboards/index-admin.blade.php ENDPATH**/ ?>