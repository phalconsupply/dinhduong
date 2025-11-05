<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="mb-0">Khảo sát mới</h5>
    <form action="<?php echo e(route('admin.history.export')); ?>" method="POST" class="mb-0">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-primary text-white">
            <i class="ti ti-download icons icon-sm"></i> Xuất khảo sát
        </button>
    </form>
</div>

<div class="table-responsive shadow rounded mt-2">
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
        <?php $__currentLoopData = $new_survey; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/dashboards/sections/khao-sat-moi.blade.php ENDPATH**/ ?>