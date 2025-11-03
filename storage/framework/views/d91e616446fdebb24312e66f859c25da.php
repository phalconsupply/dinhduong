<?php $__env->startSection('title'); ?>
    <?php echo getSetting('site_name') ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'home'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class=" col-lg-12 col-md-12 col-12">
                    <h5 class="mb-0"><a href="<?php echo e(route('admin.history.index')); ?>"><i class="ti ti-history icons icon-sm"></i> Lịch sử tra cứu</a></h5>
                    <div class="row">
                        <div class="col-12 mt-4 mb-2">
                            <div class="d-flex">
                                <form action="" method="GET">
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <div class="form-group position-relative">
                                            <label>Từ khóa</label>
                                            <input
                                                name="keyword"
                                                id="keyword"
                                                type="text"
                                                class="form-control ps-3"
                                                value="<?php echo e(app()->request->get('keyword')); ?>"
                                                placeholder="Nhập tên, sđt, cc..."
                                            >
                                        </div>


                                        <div class="form-group">
                                            <label class="small">Từ ngày:</label>
                                            <input name="from_date" class="form-control" value="<?php echo e(request()->get('from_date','')); ?>" type="date">
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Đến ngày:</label>
                                            <input name="to_date" class="form-control" value="<?php echo e(request()->get('to_date','')); ?>" type="date">
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Tỉnh/TP:</label>
                                            <select name="province_code" id="province_code" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="">Tỉnh/thành phố</option>
                                                <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($province->code); ?>" <?php if(request()->get('province_code') == $province->code): ?> selected <?php endif; ?>><?php echo e($province->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Quận huyện:</label>
                                            <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="">Quận/huyện</option>
                                                <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($district->code); ?>" <?php if($district->code == request()->get('district_code')): ?> selected <?php endif; ?>><?php echo e($district->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Phường xã:</label>
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
                                                <button type="submit" class="btn btn-sm btn-primary text-white">Lọc</button>
                                                <button type="reset" class="btn btn-sm btn-warning text-white" onclick="resetForm()">Làm mới</button>
                                            </div>
                                        </div>
                                    </div> <!-- ✅ Thêm dòng này để đóng div.d-flex -->
                                </form>
                                <div class="d-flex align-items-end justify-content-end mb-1 ms-2">
                                    <div class="form-group">
                                        <form action="<?php echo e(route('admin.history.export')); ?>" method="POST" class="mb-0">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-primary text-white">
                                                <i class="ti ti-download icons icon-sm"></i> Xuất khảo sát
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive shadow rounded mt-4">
                                <table id="history-table" class="table table-center bg-white mb-0">
                                    <thead>
                                    <tr>
                                        <th class="border-bottom p-2">ID</th>
                                        <th class="border-bottom p-2">Ảnh</th>
                                        <th class="border-bottom p-2">Tên<br />Điện thoại<br />CCCD</th>
                                        <th class="border-bottom p-2" >Chỉ số</th>
                                        <th class="border-bottom p-2" >Ngày cân<br />Ngày sinh</th>
                                        <th class="border-bottom p-2" >Kết quả</th>
                                        <th class="border-bottom p-2" >Trạng thái</th>
                                        <th class="border-bottom p-2" >Giới tính<br />Tuổi<br />Dân tộc</th>
                                        <th class="border-bottom p-2" >Địa chỉ</th>
                                        <th class="border-bottom p-2" >Người lập<br />Đơn vị<br />Ngày lập</th>
                                        <th class="border-bottom p-2">Menu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class=""><?php echo e($row->id); ?></td>
                                            <td class="">
                                                <?php if($row->thumb): ?>
                                                    <a href="<?php echo e(route('admin.users.show', $row)); ?>"><img src="<?php echo e($row->thumb); ?>" class="img-thumbnail" width="80px" /></a>
                                                <?php else: ?>
                                                    <a href="<?php echo e(route('admin.users.show', $row)); ?>"><img src="<?php echo e(v('user.avatar')); ?>" class="img-thumbnail" width="80px" /></a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="small"><?php echo e($row->fullname); ?></span><br>
                                                <span class="small"><?php echo $row->phone ?? '&nbsp;'; ?></span><br>
                                                <span class="small"><?php echo $row->id_number ?? '&nbsp;'; ?></span>
                                            </td>
                                            <td>
                                                <span class="small">Chiều cao: <?php echo e($row->height); ?> cm</span><br>
                                                <span class="small">Cân nặng: <?php echo e($row->weight); ?> kg</span><br>
                                                <span class="small">BMI: <?php echo e($row->bmi); ?></span>
                                            </td>
                                            <td>
                                                <span class="small"><?php echo e($row->birthday_f() ?? '#'); ?></span><br>
                                                <span class="small"><?php echo e($row->cal_date_f() ?? '#'); ?></span>
                                            </td>
                                            <td>
                                                <span class="small" style="background-color: <?php echo e($row->check_weight_for_age()['color']); ?>">Cân nặng theo tuổi:<?php echo e($row->check_weight_for_age()['text']); ?></span><br>
                                                <span class="small" style="background-color: <?php echo e($row->check_height_for_age()['color']); ?>">Chiều cao theo tuổi:<?php echo e($row->check_height_for_age()['text']); ?></span><br>
                                                <span class="small" style="background-color: <?php echo e($row->check_weight_for_height()['color']); ?>">Cân nặng theo chiều cao:<?php echo e($row->check_weight_for_height()['text']); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                    $nutritionStatus = $row->nutrition_status ?? '';
                                                    $isEmpty = in_array($nutritionStatus, ['', null, 'Chưa xác định', 'Chưa có đủ dữ liệu']);
                                                    
                                                    // Kiểm tra nếu có chứa "gầy còm" (case-insensitive)
                                                    $isWasted = !$isEmpty && stripos($nutritionStatus, 'gầy còm') !== false;
                                                ?>

                                                <?php if($isEmpty): ?>
                                                    <span class="badge bg-secondary">Chưa xác định</span>
                                                <?php elseif($isWasted): ?>
                                                    
                                                    <span class="badge bg-danger"><?php echo e($nutritionStatus); ?></span>
                                                <?php else: ?>
                                                    
                                                    <span class="small"><?php echo e($nutritionStatus); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo e(v('gender.color.'.$row->gender)); ?>"><?php echo e(v('gender.'.$row->gender)); ?></span><br>
                                                <span class=""><?php echo e($row->get_age()); ?></span><br>
                                                <span class=""><?php echo e($row->ethnic->name ?? '#'); ?></span>
                                            </td>
                                            <td>
                                                <span class="small"><?php echo e($row->address ?? '#'); ?></span><br>
                                                <span class="small"><?php echo e($row->ward->full_name ?? '#'); ?>, <?php echo e($row->district->full_name ?? '#'); ?></span><br>
                                                <span  class="small"><?php echo e($row->province->full_name ?? '#'); ?></span>
                                            </td>
                                            <td>
                                                <?php $creator = $row->creator; ?>
                                                <span class="small"><?php echo e($creator->name ?? 'Khách vãng lai'); ?></span><br>
                                                <span class="small"><?php echo $creator->unit->name ?? '&nbsp;'; ?></span><br>
                                                <span class="small"><?php echo e($row->created_at->format("d-m-Y")); ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown-primary me-2 mt-2">
                                                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Menu
                                                    </button>
                                                    <div class="dropdown-menu">

                                                        <a href="<?php echo e(route('result',['uid'=>$row->uid])); ?>" class="dropdown-item" target="_blank"><i class="ti ti-eye"></i> Xem kết quả</a>
                                                        <a href="<?php echo e(route('print',['uid'=>$row->uid])); ?>" class="dropdown-item" target="_blank"><i class="ti ti-printer"></i> In</a>
                                                        <div class="dropdown-divider"></div>
                                                        <form class="dropdown-item" action="<?php echo e(route('admin.history.destroy', ['history'=>$row])); ?>" method="POST" onsubmit="return confirm('Xác nhận xóa khoả sát này?');">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                            <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa lịch sử</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <div class="mt-2 d-flex">
                                    <div class="mx-auto">
                                        <?php echo e($history->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4")); ?>

                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('foot'); ?>
    <script>
        function resetForm() {
            window.location.href = "<?php echo e(route('admin.history.index')); ?>";
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

<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/history/index.blade.php ENDPATH**/ ?>