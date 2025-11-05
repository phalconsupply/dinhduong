<?php $__env->startSection('title'); ?>
    <?php echo getSetting('site_name') ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'home'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 d-lg-block d-none">
                    <div class="card border-bottom pb-4">

                        <div class="card-body">
                            <div class="card-title"><h5>Chỉnh sửa đơn vị</h5></div>
                            <form action="<?php echo e(route('admin.units.update', $unit)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tên đơn vị <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="name" id="name" type="text" class="form-control ps-5" placeholder="Tên đơn vị" value="<?php echo e(old('name', $unit->name)); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="phone" id="phone" type="text" class="form-control ps-5"  value="<?php echo e(old('phone', $unit->phone)); ?>" placeholder="Số điện thoại đơn vị" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Thư điện tử</label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="email" id="email" type="email" class="form-control ps-5" placeholder="Thư điện tử đơn vị" value="<?php echo e(old('email', $unit->email)); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Cấp đơn vị <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <select name="type_id" id="type_id" class="form-select form-control pl-45px" aria-label="Default select example" required>
                                                        <option value="">Chọn cấp bậc đơn vị</option>
                                                        <?php $__currentLoopData = old('unit_types', $unit_types) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($type->id); ?>" <?php if($type->id == old('type_id', $unit->type_id)): ?> selected <?php endif; ?>><?php echo e($type->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Địa chỉ<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <input name="address" id="address" type="text" class="form-control ps-5" value="<?php echo e(old('address', $unit->address)); ?>" placeholder="Địa chỉ" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tỉnh/TP<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <select name="province_code" id="province_code" class="pl-45px form-select form-control " aria-label="Default select example" required>
                                                        <option>Tỉnh/thành phố</option>
                                                        <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($province->code); ?>" <?php if(old('province_code', $unit->province_code) == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Quận/Huyện<span id="required_district_code" class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="district_code" id="district_code" class="pl-45px form-select form-control " aria-label="Default select example" required>
                                                        <option value="">Quận/huyện</option>
                                                        <?php $__currentLoopData = old('districts', $districts) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($district->code); ?>" <?php if($district->code == old('district_code', $unit->district_code)): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Phường/Xã<span id="required_ward_code" class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="ward_code" id="ward_code" class="pl-45px form-select form-control " aria-label="Default select example" required>
                                                        <option value="">Phường/xã</option>
                                                        <?php $__currentLoopData = old('wards', $wards) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($ward->code); ?>" <?php if($ward->code == old('ward_code', $unit->ward_code)): ?> selected <?php endif; ?>><?php echo e($ward->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <div class="form-check form-switch">
                                                <input name="is_active" class="form-check-input" type="checkbox" id="is_active" <?php if(old('is_active', $unit->is_active) == 'on' || old('is_active', $unit->is_active) == 1): ?> checked <?php endif; ?>>
                                                <label class="form-check-label" for="is_active">Trạng thái</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Ảnh đại diện</label>
                                                <div class="form-icon position-relative">
                                                    <i class="ti ti-upload fea icon-sm icons"></i>
                                                    <input name="thumb" id="thumb" value="<?php echo e(old('thumb',  $unit->thumb)); ?>" type="text" onclick="selectFileWithCKFinder('thumb')" class="form-control ps-5" placeholder="Nhập link ảnh">
                                                </div>
                                            </div>
                                        </div><!--end col-->

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Ghi chú</label>
                                            <textarea name="note" class="form-control" placeholder="Ghi chú"><?php echo e(old('note', $unit->note)); ?></textarea>
                                        </div>


                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-8 ta-r">
                                        <input type="submit" id="submit" name="send" class="btn btn-sm btn-primary" value="Cập nhật đơn vị">
                                    </div><!--end col-->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('foot'); ?>
    <script>
        $(document).ready(function() {
            

            
            

            

            
            
            
            

            
            // $('#level').change(function() {
            //     var level = $(this).val();
            //     if(level == 'province'){
            //         $("#required_district_code").hide();
            //         $("#required_ward_code").hide();
            //         $('#district_code').attr('required', false);
            //         $('#ward_code').attr('required', false);
            //     }
            //     if(level == 'district'){
            //         $("#required_district_code").show();
            //         $('#district_code').attr('required', true);
            //     }
            //     if(level == 'ward'){
            //         $("#required_district_code").show();
            //         $("#required_ward_code").show();
            //         $('#district_code').attr('required', true);
            //         $('#ward_code').attr('required', true);
            //     }
            // });
            // Khi có thay đổi trong select province
            $('#province_code').change(function() {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '<?php echo e(route('web.ajax_get_district_by_province')); ?>', // Đường dẫn tới route xử lý lấy danh sách district
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
                    url: '<?php echo e(route('web.ajax_get_ward_by_district')); ?>', // Đường dẫn tới route xử lý lấy danh sách district
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


<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/units/edit.blade.php ENDPATH**/ ?>