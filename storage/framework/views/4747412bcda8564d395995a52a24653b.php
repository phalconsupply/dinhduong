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
                            <div class="card-title"><h5>Chỉnh sử thông tin người dùng</h5></div>
                            <form action="<?php echo e(route('admin.users.update', $user)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="name" id="name" type="text" class="form-control ps-5" placeholder="Họ và tên" value="<?php echo e(old('name', $user->name)); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tên tài khoản <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="username" id="username" type="text" class="form-control ps-5"  value="<?php echo e(old('username', $user->username)); ?>" placeholder="Tên tài khoản" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Giới tính<span class="text-danger">*</span></label>
                                                <select name="gender" class="form-select form-control" required aria-label="Default select example">
                                                    <option value="1" <?php if(old('gender', $user->gender) == '1'): ?> selected <?php endif; ?>>Nam</option>
                                                    <option value="0" <?php if(old('gender', $user->gender) == '0'): ?> selected <?php endif; ?>>Nữ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Số căn cước<span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="user" class="fea icon-sm icons"></i>
                                                        <input name="id_number" id="id_number" type="text" class="form-control ps-5" placeholder="Số căn cước" value="<?php echo e(old('id_number', $user->id_number)); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Ngày sinh</label>
                                                    <div class="form-icon position-relative">
                                                        <i class="ti ti-calendar icons"></i>
                                                        <input name="birthday" id="birthday" type="date" class="form-control ps-5" placeholder="Ngày sinh" value="<?php echo e(old('birthday', $user->birthday)); ?>">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Số điện thoại<span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="user" class="fea icon-sm icons"></i>
                                                        <input name="phone" id="phone" type="text" class="form-control ps-5" placeholder="Số điện thoại" value="<?php echo e(old('phone', $user->phone)); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Thư điện tử</label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="mail" class="fea icon-sm icons"></i>
                                                        <input name="email" id="email" type="email" class="form-control ps-5" placeholder="Thư điện tử" value="<?php echo e(old('email', $user->email)); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Địa chỉ<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <input name="address" id="address" type="text" class="form-control ps-5" value="<?php echo e(old('address', $user->address)); ?>" placeholder="Địa chỉ" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tỉnh/TP<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <select name="province_code" id="province_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option>Tỉnh/thành phố</option>
                                                        <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($province->code); ?>" <?php if(old('province_code', $user->province_code) == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Quận/Huyện<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option value="">Quận/huyện</option>
                                                        <?php $__currentLoopData = old('districts', $districts) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($district->code); ?>" <?php if($district->code == old('district_code', $user->district_code)): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Phường/Xã<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="ward_code" id="ward_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option value="">Phường/xã</option>
                                                        <?php $__currentLoopData = old('wards', $wards) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($ward->code); ?>" <?php if($ward->code == old('ward_code', $user->ward_code)): ?> selected <?php endif; ?>><?php echo e($ward->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-check form-switch">
                                                <input name="is_active" class="form-check-input" type="checkbox" id="is_active" <?php if(old('is_active', $user->is_active) == 'on' || old('is_active', $user->is_active) == 1): ?> checked <?php endif; ?>>
                                                <label class="form-check-label" for="is_active">Trạng thái</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">

                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Ảnh hồ sơ</label>
                                                <div class="form-icon position-relative">
                                                    <i class="ti ti-upload fea icon-sm icons"></i>
                                                    <input name="thumb" id="thumb" value="<?php echo e(old('thumb',  $user->thumb)); ?>" type="text" onclick="selectFileWithCKFinder('thumb')" class="form-control ps-5" placeholder="Nhập link ảnh">
                                                </div>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Thuộc đơn vị <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="shield" class="fea icon-sm icons"> </i>
                                                    <select name="unit_id" id="unit_id" class="form-select form-control pl-45px" aria-label="Default select example" required>
                                                        <option value="">Chọn đơn vị</option>
                                                        <?php $__currentLoopData = $units ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($unit->id); ?>" <?php if($unit->id == old('unit_id', $user->unit_id)): ?> selected <?php endif; ?>><?php echo e($unit->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Thuộc bộ phân</label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="users" class="fea icon-sm icons"> </i>
                                                    <input name="department" id="department" type="text" class="form-control ps-5" value="<?php echo e(old('department')); ?>" placeholder="Bộ phận">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Chức danh</label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="users" class="fea icon-sm icons"> </i>
                                                    <input name="role_title" id="role_title" type="text" class="form-control ps-5" value="<?php echo e(old('role_title', $user->role_title)); ?>" placeholder="Chức danh">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="star" class="fea icon-sm icons"> </i>
                                                    <select name="role" id="role" class="form-select form-control pl-45px" aria-label="Default select example" required>
                                                        <option value="">Chọn chức vụ</option>
                                                        <option value="manager" <?php if('manager' == old('role', $user->role)): ?> selected <?php endif; ?>>Quản lý</option>
                                                        <option value="employee" <?php if('employee' == old('role', $user->role)): ?> selected <?php endif; ?>>Nhân viên</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Ghi chú</label>
                                            <textarea name="note" class="form-control" placeholder="Ghi chú"><?php echo e(old('note',  $user->note)); ?></textarea>
                                        </div>


                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-8 ta-r">
                                        <input type="submit" id="submit" name="send" class="btn btn-sm btn-primary" value="Cập nhật tài khoản">
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
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();
                        wardSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="0">Chọn quận huyện</option>');
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
                        wardSelect.append('<option value="0">Chọn phường xã</option>');
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


<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>