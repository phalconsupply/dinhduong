<?php $__env->startSection('content'); ?>
    <!-- Main Content Wrapper with Modern Style -->
    <div class="main-content-wrapper">
        <div class="content-body">
            <section id="nuti-medical">
                <div class="container-fluid">
                    <div class="row">
                        
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <?php echo $__env->make('sections.form-heading', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            
                            <!-- Progress Steps -->
                            <div class="form-progress-wrapper">
                                <div class="form-steps">
                                    <div class="step active" data-step="1">
                                        <div class="step-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="step-label">Thông tin cá nhân</div>
                                        <div class="step-connector"></div>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="step-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="step-label">Địa chỉ</div>
                                        <div class="step-connector"></div>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="step-icon">
                                            <i class="fas fa-weight"></i>
                                        </div>
                                        <div class="step-label">Chỉ số sức khỏe</div>
                                    </div>
                                </div>
                            </div>
                    
                    <div class="">
                        <div id="tab-2" class="profile-detail-menu-content" style="">
                            <?php echo $__env->make('layouts.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <form class="pro5-form" action="<?php echo e(route('form.post', ['slug' => $slug])); ?>" method="POST" enctype="multipart/form-data">
                                
                                <!-- BLOCK 1: Avatar (1/3) + Personal Information (2/3) -->
                                <div class="row">
                                    <!-- Avatar Section - 1/3 width -->
                                    <div class="col-xs-12 col-md-4">
                                        <?php echo $__env->make('sections.form-avatar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    
                                    <!-- Personal Information Section - 2/3 width -->
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-section-card">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                                <h3 class="card-title">Thông tin cá nhân</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="pro5-input">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group">
                                                    <label for="last-name">Họ và tên <span class="required">*</span></label>
                                                    <input type="text" name="fullname" value="<?php echo e(old('fullname', $item->fullname)); ?>" class="form-control" id="last-name" placeholder="Nhập họ và tên" required>
                                                    <div class="input-icon">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group">
                                                    <label for="id_number">Mã định danh (CCCD)</label>
                                                    <input type="text" name="id_number" value="<?php echo e(old('id_number', $item->id_number)); ?>" class="form-control" id="id_number" placeholder="Nhập số CCCD">
                                                    <div class="input-icon">
                                                        <i class="fas fa-id-card"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group">
                                                    <label for="phone">Số điện thoại</label>
                                                    <input type="number" minlength="10" maxlength="11" name="phone" value="<?php echo e(old('phone', $item->phone)); ?>" class="form-control" id="phone" placeholder="Nhập số điện thoại">
                                                    <div class="input-icon">
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group">
                                                    <label for="gender">Giới tính <span class="required">*</span></label>
                                                    <select name="gender" id="gender" class="form-control" style="width: 100%;">
                                                        <option value="1" <?php if(old('gender', $item->gender) == 1): ?> selected <?php endif; ?>>Nam</option>
                                                        <option value="0" <?php if(old('gender', $item->gender) == 0): ?> selected <?php endif; ?>>Nữ</option>
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-venus-mars"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group">
                                                    <label for="ethnic_id">Dân tộc <span class="required">*</span></label>
                                                    <select name="ethnic_id" id="ethnic_id" class="form-control" required="">
                                                        <?php $__currentLoopData = $ethnics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ethnic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($ethnic->id); ?>" <?php if(old('ethnic_id') && old('ethnic_id', $item->ethnic_id) == $ethnic->id): ?> selected <?php endif; ?>><?php echo e($ethnic->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-globe-asia"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-floating-group calendar-group-modern">
                                                    <label for="cal-date">Ngày cân đo <span class="required">*</span></label>
                                                    <input type="text" name="cal_date" value="<?php echo e(old('cal_date', $item?->cal_date?->format('d/m/YYYY'))); ?>" class="form-control" id="cal-date" placeholder="Chọn ngày cân đo" required>
                                                    <div class="input-icon">
                                                        <i class="fas fa-calendar-day"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <?php if($category != 3): ?>
                                                <div class="form-floating-group calendar-group-modern">
                                                    <label for="calendar-birth">Ngày sinh <span class="required">*</span></label>
                                                    <input type="text" name="birthday" value="<?php echo e(old('birthday', $item?->birthday?->format('d/m/YYYY'))); ?>" class="form-control" id="calendar-birth" placeholder="Chọn ngày sinh" required>
                                                    <div class="input-icon">
                                                        <i class="fas fa-birthday-cake"></i>
                                                    </div>
                                                    <input id="over19" type="hidden" name="over19" value="<?php echo e(old('over19', $item->over19)); ?>" />
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 1 -->
                                
                                <!-- BLOCK 2: Address (Full Width) -->
                                <div class="form-section-card">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <h3 class="card-title">Địa chỉ</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="pro5-input">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating-group">
                                                        <label for="address">Địa chỉ <span class="required">*</span></label>
                                                        <input type="text" name="address" value="<?php echo e(old('address', $item->address)); ?>" class="form-control" id="address" placeholder="Nhập địa chỉ" required>
                                                        <div class="input-icon">
                                                            <i class="fas fa-home"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="clearfix"></div>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-floating-group">
                                                        <label for="province_code">Tỉnh/Thành phố <span class="required">*</span></label>
                                                        <select name="province_code" id="province_code" class="form-control" data-placeholder="Tỉnh/Thành phố" style="width: 100%;" required>
                                                            <option value="">Chọn Tỉnh/thành phố</option>
                                                            <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($province->code); ?>" <?php if(old('province_code', $item->province_code) == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                        <div class="input-icon">
                                                            <i class="fas fa-map"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-floating-group">
                                                        <label for="district_code">Quận / Huyện <span class="required">*</span></label>
                                                        <select name="district_code" id="district_code" class="form-control" aria-label="Default select example" required="">
                                                            <option value="">Chọn Quận/huyện</option>
                                                            <?php $__currentLoopData = session('districts', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($district->code); ?>" <?php if(old('district_code', $item->district_code) == $district->code): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                        <div class="input-icon">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <div class="form-floating-group">
                                                        <label for="ward_code">Phường / Xã <span class="required">*</span></label>
                                                        <select name="ward_code" id="ward_code" class="form-control" aria-label="Default select example" required="">
                                                            <option value="">Chọn Phường/Xã</option>
                                                            <?php $__currentLoopData = session('wards', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ward): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($ward->code); ?>" <?php if(old('ward_code', $item->ward_code) == $ward->code): ?> selected <?php endif; ?>><?php echo e($ward->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                        <div class="input-icon">
                                                            <i class="fas fa-map-pin"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 2 -->
                                    
                                <!-- BLOCK 3: Birth Information (left) + Health Measurements (right) - Equal Width -->
                                <div class="row">
                                    <!-- Birth Information Section (LEFT 50%) -->
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-section-card">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-baby"></i>
                                                </div>
                                                <h3 class="card-title">Thông tin lúc sinh</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-floating-group">
                                                    <label for="birth-weight">Cân nặng lúc sinh</label>
                                                    <input id="birth-weight" min="0" type="number" step="1" name="birth_weight" value="<?php echo e(old('birth_weight', $item->birth_weight)); ?>" class="form-control" placeholder="Nhập cân nặng (gram)">
                                                    <div class="input-icon">
                                                        <i class="fas fa-weight"></i>
                                                    </div>
                                                    <small class="text-muted" style="display: block; margin-top: 5px;">Đơn vị: gram</small>
                                                </div>
                                                
                                                <div class="form-floating-group">
                                                    <label for="gestational-age">Tuổi thai lúc sinh</label>
                                                    <select name="gestational_age" id="gestational-age" class="form-control">
                                                        <option value="">Chọn tuổi thai</option>
                                                        <option value="Đủ tháng" <?php echo e(old('gestational_age', $item->gestational_age) == 'Đủ tháng' ? 'selected' : ''); ?>>Đủ tháng</option>
                                                        <option value="Thiếu tháng" <?php echo e(old('gestational_age', $item->gestational_age) == 'Thiếu tháng' ? 'selected' : ''); ?>>Thiếu tháng</option>
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-floating-group">
                                                    <label for="birth-weight-category">Phân loại cân nặng</label>
                                                    <input id="birth-weight-category" type="text" name="birth_weight_category_display" value="<?php echo e(old('birth_weight_category', $item->birth_weight_category)); ?>" class="form-control" placeholder="Tự động tính" readonly style="background-color: #f8f9fa; font-weight: 600;">
                                                    <input type="hidden" name="birth_weight_category" id="birth-weight-category-hidden" value="<?php echo e(old('birth_weight_category', $item->birth_weight_category)); ?>">
                                                    <div class="input-icon">
                                                        <i class="fas fa-info-circle"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Health Measurements Section (RIGHT 50%) -->
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-section-card">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-heartbeat"></i>
                                                </div>
                                                <h3 class="card-title">Chỉ số sức khỏe</h3>
                                            </div>
                                            <div class="card-body">
                                                <!-- Measurement Cards Grid -->
                                                <div class="measurement-grid">
                                                    <!-- Weight Card -->
                                                    <div class="measurement-card weight">
                                                        <div class="measurement-icon">⚖️</div>
                                                        <div class="measurement-value">
                                                            <input id="weight-user-profile" min="0" type="number" step="0.1" required name="weight" value="<?php echo e(old('weight', $item->weight)); ?>" placeholder="0.0">
                                                            <span class="unit">kg</span>
                                                        </div>
                                                        <div class="measurement-label">Cân nặng</div>
                                                    </div>
                                                    
                                                    <!-- Height Card -->
                                                    <div class="measurement-card height">
                                                        <div class="measurement-icon">📏</div>
                                                        <div class="measurement-value">
                                                            <input id="length-user-profile" type="number" step="0.1" min="0" required name="height" value="<?php echo e(old('height', $item->height)); ?>" placeholder="0.0">
                                                            <span class="unit">cm</span>
                                                        </div>
                                                        <div class="measurement-label">Chiều cao</div>
                                                    </div>
                                                    
                                                    <!-- Age Card -->
                                                    <div class="measurement-card age">
                                                        <div class="measurement-icon">🎂</div>
                                                        <div class="measurement-value">
                                                            <input name="age_show" value="<?php echo e(old('age_show', $item->age_show)); ?>" id="age_show" type="text" readonly placeholder="--">
                                                            <span class="unit">tuổi</span>
                                                        </div>
                                                        <div class="measurement-label">Tuổi</div>
                                                        <input name="age" value="<?php echo e(old('age',  $item->age)); ?>" id="age" type="hidden" readonly>
                                                        <input type="hidden" name="realAge" id="real-age" value="0">
                                                    </div>
                                                    
                                                    <!-- BMI Card -->
                                                    <div class="measurement-card bmi" id="bmi-card">
                                                        <div class="measurement-icon">📊</div>
                                                        <div class="measurement-value">
                                                            <input id="bmi-user-profile" type="text" name="bmi" value="<?php echo e(old('bmi', $item->bmi)); ?>" readonly="" placeholder="--">
                                                            <span class="unit">BMI</span>
                                                        </div>
                                                        <div class="measurement-label">Chỉ số BMI</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Classification Info Panel -->
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-section-card classification-info-panel">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <h3 class="card-title">Phân loại & Bảng chuẩn WHO</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="classification-display" id="classification-info">
                                                    <div class="info-card age-group-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-child"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Nhóm tuổi</h6>
                                                            <p class="info-value" id="age-group-info">Chưa xác định</p>
                                                            <small class="info-detail" id="age-group-detail">Nhập ngày sinh để xác định</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card standard-table-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-table"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Bảng chuẩn sử dụng</h6>
                                                            <p class="info-value" id="standard-table-info">--</p>
                                                            <small class="info-detail" id="standard-table-detail">Tự động chọn theo tuổi</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card measurement-method-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-ruler"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Phương pháp đo</h6>
                                                            <p class="info-value" id="measurement-method-info">--</p>
                                                            <small class="info-detail" id="measurement-method-detail">Phụ thuộc vào tuổi</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card calculation-method-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-calculator"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Phương pháp tính toán</h6>
                                                            <p class="info-value">WHO LMS 2006</p>
                                                            <small class="info-detail">Lambda-Mu-Sigma Method</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Link to Guide -->
                                                <div class="guide-link-wrapper">
                                                    <a href="/huong-dan-danh-gia-dinh-duong.html" target="_blank" class="guide-link">
                                                        <i class="fas fa-book-open"></i>
                                                        Xem hướng dẫn chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 3 -->
                                
                                <!-- Submit Button -->
                                <div class="submit-button-wrapper" style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
                                        <?php echo csrf_field(); ?>
                                        <input id="category-user-profile" type="hidden" name="category" value="<?php echo e($category); ?>">
                                        <input name="slug" value="<?php echo e($slug); ?>" type="hidden">
                                        <?php if($item->id): ?>
                                            <input name="id" value="<?php echo e($item->id); ?>" type="hidden">
                                            <input name="uid" value="<?php echo e($item->uid); ?>" type="hidden">
                                        <?php endif; ?>
                                        <button class="btn-submit-form" type="submit">
                                            <i class="fas fa-search"></i> Xem kết quả
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Validate start -->
        <div class="modal nuti-modal common-modal fade modal400" id='amz_common_error_modal' tabindex="-1" role="dialog"
             aria-labelledby="nutiModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="icon close-icon"></i>
                        </button>
                        <h4 class="modal-title" id="nutiModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="redirect_url" value="" />
                        <strong></strong>

                    </div>
                </div>
            </div>
        </div>
        <!-- Validate end -->
                    </div>
                </div>
            </section>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('foot'); ?>
    <!-- controler monthAction 550 -->
    <script type="text/javascript">
        $(document).ready(function() {
            // Khi có thay đổi trong select province
            $('#province_code').change(function () {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '<?php echo e(route('web.ajax_get_district_by_province')); ?>', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: {province_code: province_code}, // Truyền province id qua request
                    success: function (response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var districtSelect = $('#district_code'); // Select element cho district
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();
                        wardSelect.find('option').remove();
                        wardSelect.append('<option value="">Chọn phường xã</option>');

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="">Chọn quận huyện</option>');
                        $.each(response.districts, function (key, value) {
                            districtSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });

                    },
                    error: function (xhr, status, error) {
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

        $(window).load(function() {
            document.getElementById("age").addEventListener("change", age19);

            function age19() {
                var a = document.getElementById("age").value;
                if (a < 19) {
                    alert('Bé nhỏ hơn 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                    $("#age").val('');
                }
            }

            var getMonthUrl = "<?php echo e(url('/ajax/tinh-ngay-sinh')); ?>";
            var gMonth;

            function getMonthAjax(birthdate, date) {
                $.ajax({
                    url: getMonthUrl,
                    data: {
                        'birthday': birthdate,
                        'date': date
                    },
                    success: function(response) {
                        var months = response;
                        var age = Math.floor(months / 12);
                        $("#addon3").text('tuổi');
                        if (category == 1) {
                            if (months < 61) { //0 - 60 tháng == 0- 5 tuổi
                                $("#age_show").val(months + ' tháng');
                                $("#age").val(months);
                            } else {
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé lớn hơn 5 tuổi hoặc > 61 tháng. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        } else if (category == 2) {
                            if (months >= 61 && months < 72) {
                                $("#age_show").val(months + ' tháng');
                                $("#age").val(months);
                            } else if (months >= 72 && months < 229) {
                                console.log(getAge(birthdate, date));
                                $("#addon3").text('');
                                $('#age').val(getAge(birthdate, date));
                            } else {
                                console.log(getAge(birthdate, date));
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé nhỏ hơn 5 tuổi hoặc > 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        } else if (category == 3) {
                            if (months >= 229) {
                                $('#age').val(age);
                            } else {
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé nhỏ hơn 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        }

                        $('#real-age').val(months / 12);
                        
                        // Cập nhật thông tin phân loại và bảng chuẩn
                        updateClassificationInfo(months);
                    },
                    error: function(jqXHR, textStatus) {
                        if (jqXHR.status == 401) {
                            $('#age').val('');
                            alert(jqXHR.responseText);
                        } else {
                            // alert('Không thể kiểm tra tuổi của đối tượng, có thể xảy ra lỗi kết nối đến hệ thống. Xin vui lòng kiểm tra lại');
                        }

                    }
                })
            }

            function getAge(dateString, date) {
                console.log(date);
                console.log(dateString);
                // var today = new Date(now.getYear(),now.getMonth(),now.getDate());
                var now = new Date(date.substring(6, 10),
                    date.substring(3, 5) - 1,
                    date.substring(0, 2)
                );
                var yearNow = now.getYear();
                var monthNow = now.getMonth();
                var dateNow = now.getDate();

                var dob = new Date(dateString.substring(6, 10),
                    dateString.substring(3, 5) - 1,
                    dateString.substring(0, 2)
                );
                console.log(now);
                console.log(dob);
                var yearDob = dob.getYear();
                var monthDob = dob.getMonth();
                var dateDob = dob.getDate();
                var age = {};
                var ageString = "";
                var yearString = "";
                var monthString = "";
                var dayString = "";

                yearAge = yearNow - yearDob;

                if (monthNow >= monthDob)
                    var monthAge = monthNow - monthDob;
                else {
                    yearAge--;
                    var monthAge = 12 + monthNow - monthDob;
                }

                if (dateNow >= dateDob)
                    var dateAge = dateNow - dateDob;
                else {
                    monthAge--;
                    var dateAge = 31 + dateNow - dateDob;

                    if (monthAge < 0) {
                        monthAge = 11;
                        yearAge--;
                    }
                }

                age = {
                    years: yearAge,
                    months: monthAge,
                    days: dateAge
                };

                if (age.years > 1) yearString = " tuổi";
                else yearString = " tuổi";
                if (age.months > 1) monthString = " tháng";
                else monthString = " tháng";
                if (age.days > 1) dayString = " ngày";
                else dayString = " ngày";

                if ((age.years > 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.years + yearString + ", " + age.months + monthString;
                else if ((age.years == 0) && (age.months == 0) && (age.days > 0))
                    ageString = "Chỉ " + age.days + dayString + " tuổi!";
                else if ((age.years > 0) && (age.months == 0) && (age.days == 0))
                    ageString = age.years + yearString + " 0 tháng";
                else if ((age.years > 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.years + yearString + " " + age.months + monthString + ".";
                else if ((age.years == 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.months + monthString;
                else if ((age.years > 0) && (age.months == 0) && (age.days > 0))
                    ageString = age.years + yearString + " " + age.months + monthString;
                else if ((age.years == 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.months + monthString + ".";
                else ageString = "Oops! Could not calculate age!";

                return ageString;
            }

            function monthDiff(d1, d2) {
                var d1Y = d1.getFullYear();
                var d2Y = d2.getFullYear();
                var d1M = d1.getMonth();
                var d2M = d2.getMonth();

                return (d1M + 12 * d1Y) - (d2M + 12 * d2Y);
            }

            var category = <?php echo e($category); ?>; // Get category from server-side blade variable

            $("#cal-date").datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: new Date(),
                maxDate: new Date()
            }).on('dp.change', function(e) {
                var decrementDay = moment(new Date(e.date));
                // decrementDay.subtract(1, 'days');
                $('#calendar-birth').data('DateTimePicker').maxDate(decrementDay);
                $(this).data("DateTimePicker").hide();
                
                // Chỉ gọi AJAX nếu cả 2 trường đã có giá trị
                if ($("#calendar-birth").val() && $("#cal-date").val()) {
                    getMonthAjax($("#calendar-birth").val(), $("#cal-date").val());
                }
            });

            $("#calendar-birth").datetimepicker({
                <?php if(old('birthday')): ?>
                defaultDate: moment('<?php echo e(old('birthday')); ?>', 'DD/MM/YYYY').toDate(),
                <?php endif; ?>
                format: 'DD/MM/YYYY',
                maxDate: new Date()
            }).on('dp.change', function(e) {
                var incrementDay = moment(new Date(e.date));
                // incrementDay.add(1, 'days');
                $('#cal-date').data('DateTimePicker').minDate(incrementDay);
                $(this).data("DateTimePicker").hide();
                
                // Chỉ gọi AJAX nếu cả 2 trường đã có giá trị
                if ($("#calendar-birth").val() && $("#cal-date").val()) {
                    getMonthAjax($("#calendar-birth").val(), $("#cal-date").val());
                }
            });

            $("#last-name").focus();
            // $("#calendar-birth").val("");
            var availableCities = [
                "AN GIANG",
                "BÀ RỊA     - VŨNG TÀU",
                "BẮC GIANG",
                "BẮC KẠN",
                "BẠC LIÊU",
                "BẮC NINH",
                "BẾN TRE",
                "BÌNH ĐỊNH",
                "BÌNH DƯƠNG",
                "BÌNH PHƯỚC",
                "BÌNH THUẬN",
                "CÀ MAU",
                "CẦN THƠ",
                "CAO BẰNG",
                "ĐÀ NẴNG",
                "ĐẮK LẮK",
                "ĐẮK NÔNG",
                "ĐIỆN BIÊN",
                "ĐỒNG NAI",
                "ĐỒNG THÁP",
                "GIA LAI",
                "HÀ GIANG",
                "HÀ NAM",
                "HÀ NỘI",
                "HÀ TĨNH",
                "HẢI DƯƠNG",
                "HẢI PHÒNG",
                "HẬU GIANG",
                "HỒ CHÍ MINH",
                "HÒA BÌNH",
                "HƯNG YÊN",
                "KHÁNH HÒA",
                "KIÊN GIANG",
                "KON TUM",
                "LAI CHÂU",
                "LÂM ĐỒNG",
                "LẠNG SƠN",
                "LÀO CAI",
                "LONG AN",
                "NAM ĐỊNH",
                "NGHỆ AN",
                "NINH BÌNH",
                "NINH THUẬN",
                "PHÚ THỌ",
                "PHÚ YÊN",
                "QUẢNG BÌNH",
                "QUẢNG NAM",
                "QUẢNG NGÃI",
                "QUẢNG NINH",
                "QUẢNG TRỊ",
                "SÓC TRĂNG",
                "SƠN LA",
                "TÂY NINH",
                "THÁI BÌNH",
                "THÁI NGUYÊN",
                "THANH HÓA",
                "THỪA THIÊN HUẾ",
                "TIỀN GIANG",
                "TRÀ VINH",
                "TUYÊN QUANG",
                "VĨNH LONG",
                "VĨNH PHÚC",
                "YÊN BÁI",
            ];
            $("#address").autocomplete({
                source: function(request, response) {
                    var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                    response($.grep(availableCities, function(item) {
                        var result = matcher.test(item);
                        return result
                    }));
                },
            });

            if (category === 3) {
                $("#age").change(function() {
                    var date = new Date();
                    var age = $("#age").val()
                    var year = date.getFullYear() - parseInt(age);
                    // $('#real-age').val(age);
                    $('#real-age').attr('value', age);
                    // $("#calendar-birth").val('01/01/' + year);
                    $('#calendar-birth').attr('value', '01/01/' + year);
                    // $("#over19").val("1");
                    $('#over19').attr('value', '1');
                });
            }

            function checkValidateBeforeSubmitForm() {
                var isValid = true;
                var invalidCounter = 0;

                function isValidDate(d) {
                    return d instanceof Date && !isNaN(d);
                }

                var ngaySinhVal = $('#calendar-birth').val();
                //regex convert 20/09/2018 to 09/20/2018
                var ngaySinhCheck = new Date(ngaySinhVal.replace(/(\d{2})\/(\d{2})\/(\d{4})/, "$2/$1/$3"));
                console.log('ngaySinhVal', ngaySinhVal);
                //console.log('ngaySinhCheck', ngaySinhCheck, isValidDate(ngaySinhCheck));
                //co loi thi tang bien dem them 1;
                if (isValidDate(ngaySinhCheck) === false) {
                    invalidCounter++;
                }

                //TODO check valid other properties
                console.log('invalidCounter', invalidCounter);
                //has invalid return false
                return (invalidCounter > 0) ? false : true;
            }

            $(".pro5-form").submit(function(event) {
                if (checkValidateBeforeSubmitForm() !== true) {
                    event.preventDefault();
                };

                if ($('#real-age').val() == "") {
                    alert("VUI LÒNG KIỂM TRA LẠI ĐƯỜNG TRUYỀN VÀ NHẬP LẠI NGÀY THÁNG NĂM SINH CỦA ĐỐI TƯỢNG");
                    return false;
                }
            });


            function alert(message, title) {
                if (title == undefined) {
                    title = "Thông báo";
                }
                $("#amz_common_error_modal h4").html(title);
                $("#amz_common_error_modal .modal-body strong").html(message);
                $("#amz_common_error_modal").modal('show');
            }
        });

        document.getElementById('avatar-wapper').addEventListener('click', function() {
            document.getElementById('avatar-input').click();
        });

        document.getElementById('avatar-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                    document.getElementById('title-name').style.display = 'none'; // ẩn title
                }
                reader.readAsDataURL(file);
            }
        });

        // Logic phân loại cân nặng lúc sinh
        document.getElementById('birth-weight').addEventListener('input', function() {
            classifyBirthWeight();
        });

        function classifyBirthWeight() {
            const birthWeight = parseFloat(document.getElementById('birth-weight').value);
            const categoryDisplay = document.getElementById('birth-weight-category');
            const categoryHidden = document.getElementById('birth-weight-category-hidden');
            
            if (isNaN(birthWeight) || birthWeight <= 0) {
                categoryDisplay.value = '';
                categoryHidden.value = '';
                categoryDisplay.style.backgroundColor = '#f5f5f5';
                categoryDisplay.style.color = '#333';
                return;
            }

            let category = '';
            let bgColor = '#f5f5f5';
            let textColor = '#333';

            if (birthWeight < 2500) {
                category = 'Nhẹ cân';
                bgColor = '#fff3cd'; // Vàng nhạt
                textColor = '#856404';
            } else if (birthWeight >= 2500 && birthWeight <= 4000) {
                category = 'Đủ cân';
                bgColor = '#d4edda'; // Xanh nhạt
                textColor = '#155724';
            } else if (birthWeight > 4000) {
                category = 'Thừa cân';
                bgColor = '#f8d7da'; // Đỏ nhạt
                textColor = '#721c24';
            }

            categoryDisplay.value = category;
            categoryHidden.value = category;
            categoryDisplay.style.backgroundColor = bgColor;
            categoryDisplay.style.color = textColor;
            categoryDisplay.style.fontWeight = 'bold';
        }

        // Chạy phân loại khi load trang nếu đã có giá trị
        window.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('birth-weight').value) {
                classifyBirthWeight();
            }
        });

        /**
         * Cập nhật thông tin phân loại và bảng chuẩn WHO LMS dựa trên tuổi
         */
        function updateClassificationInfo(ageInMonths) {
            const ageInWeeks = ageInMonths * 4.33;
            
            // Xác định nhóm tuổi
            let ageGroup = '';
            let ageGroupDetail = '';
            let standardTable = '';
            let standardTableDetail = '';
            let measurementMethod = '';
            let measurementMethodDetail = '';
            
            if (ageInWeeks <= 13) {
                ageGroup = 'Trẻ sơ sinh (0-13 tuần)';
                ageGroupDetail = 'Giai đoạn tăng trưởng cực nhanh';
                standardTable = 'Bảng 0_13w (0-13 tuần)';
                standardTableDetail = 'Dữ liệu theo tuần, độ chính xác cao';
                measurementMethod = 'Chiều dài nằm';
                measurementMethodDetail = 'WFL - Weight for Length';
            } else if (ageInMonths <= 24) {
                ageGroup = 'Trẻ nhỏ (0-2 tuổi)';
                ageGroupDetail = 'Giai đoạn tăng trưởng nhanh';
                standardTable = 'Bảng 0_2y (0-24 tháng)';
                standardTableDetail = 'Dữ liệu theo tháng, ưu tiên cho trẻ nhỏ';
                measurementMethod = 'Chiều dài nằm';
                measurementMethodDetail = 'WFL - Weight for Length';
            } else if (ageInMonths <= 60) {
                ageGroup = 'Trẻ lớn (2-5 tuổi)';
                ageGroupDetail = 'Giai đoạn ổn định tăng trưởng';
                standardTable = 'Bảng 0_5y (0-60 tháng)';
                standardTableDetail = 'Phạm vi rộng nhất, ưu tiên cao';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'WFH - Weight for Height';
            } else {
                ageGroup = 'Trên 5 tuổi';
                ageGroupDetail = 'Ngoài phạm vi đánh giá dinh dưỡng trẻ em';
                standardTable = 'Không áp dụng';
                standardTableDetail = 'Cần sử dụng bảng chuẩn khác';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'BMI for Age';
            }
            
            // Cập nhật giao diện
            document.getElementById('age-group-info').textContent = ageGroup;
            document.getElementById('age-group-detail').textContent = ageGroupDetail;
            document.getElementById('standard-table-info').textContent = standardTable;
            document.getElementById('standard-table-detail').textContent = standardTableDetail;
            document.getElementById('measurement-method-info').textContent = measurementMethod;
            document.getElementById('measurement-method-detail').textContent = measurementMethodDetail;
            
            // Cập nhật màu sắc theo nhóm tuổi
            const ageGroupCard = document.querySelector('.age-group-card .info-icon');
            if (ageInWeeks <= 13) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #ff6b6b, #ff8e53)';
            } else if (ageInMonths <= 24) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #4ecdc4, #44a08d)';
            } else if (ageInMonths <= 60) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #667eea, #764ba2)';
            } else {
                ageGroupCard.style.background = 'linear-gradient(45deg, #f093fb, #f5576c)';
            }
        }
    </script>
    
    <!-- CSS cho Classification Panel -->
    <style>
        .classification-info-panel .card-body {
            padding: 1.5rem;
        }
        
        .classification-display {
            display: grid;
            gap: 1rem;
        }
        
        .info-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 1.1em;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #495057;
            font-size: 0.9em;
        }
        
        .info-value {
            font-weight: bold;
            margin-bottom: 0.25rem;
            color: #212529;
            font-size: 1em;
        }
        
        .info-detail {
            color: #6c757d;
            font-size: 0.8em;
            line-height: 1.3;
        }
        
        .guide-link-wrapper {
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .guide-link {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .guide-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .guide-link i {
            margin-right: 0.5rem;
        }
        
        /* Age Group Specific Colors */
        .age-group-card .info-icon {
            background: linear-gradient(45deg, #6c757d, #495057);
        }
        
        .standard-table-card .info-icon {
            background: linear-gradient(45deg, #17a2b8, #138496);
        }
        
        .measurement-method-card .info-icon {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }
        
        .calculation-method-card .info-icon {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
    </style>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/form.blade.php ENDPATH**/ ?>