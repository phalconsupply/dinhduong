<div class="table-responsive shadow rounded">
    <table id="history-table" class="table table-center bg-white mb-0">
        <thead>
        <tr>
            <th class="border-bottom ">ID</th>
            <th class="border-bottom ">Ảnh</th>
            <th class="border-bottom ">Tên đơn vị</th>
            <th class="border-bottom ">Số điện thoại/Email</th>
            <th class="border-bottom " >Địa chỉ</th>
            <th class="border-bottom " >Cấp bậc</th>
            <th class="border-bottom " >Trạng thái</th>
            <th class="border-bottom " >Ngày tạo/Người tạo</th>
            <th class="border-bottom ">Menu</th>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class=""><?php echo e($unit->id); ?></td>
                <td class="">
                    <?php if($unit->thumb): ?>
                        <a href="<?php echo e(route('admin.units.show_history', $unit)); ?>"><img src="<?php echo e($unit->thumb); ?>" class="img-thumbnail" width="80px" /></a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.units.show_history', $unit)); ?>"><img src="<?php echo e(v('user.avatar')); ?>" class="img-thumbnail" width="80px" /></a>
                    <?php endif; ?>
                </td>
                <td class="">
                    <a href="<?php echo e(route('admin.units.show', $unit)); ?>"><?php echo e($unit->name); ?></a><br/>
                    
                </td>
                <td>
                    <span class="small"><?php echo e($unit->phone); ?></span><br>
                    <span class="small"><?php echo e($unit->email); ?></span>
                </td>

                <td><span class="small"><?php echo e($unit->address ?? '#'); ?></span><br>
                    <span class="small"><?php echo e($unit->ward->full_name ?? '#'); ?>, <?php echo e($unit->district->full_name ?? '#'); ?></span><br>
                    <span class="small"><?php echo e($unit->province->full_name ?? '#'); ?></span>
                </td>
                <td>
                    <span class="badge bg-success"><?php echo e($unit->unit_type->name); ?></span>
                </td>
                <td>
                    <?php if($unit->is_active == 1): ?>
                        <span class="badge bg-success">Hoạt động</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Chưa kích hoạt</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="small"><?php echo e($unit->created_at->format('d-m-Y')); ?></span><br>
                    <span class="small"><?php echo e($unit->creator->username); ?></span>
                </td>
                <td>
                    <div class="btn-group dropdown-primary me-2 mt-2">
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu">

                            <a href="<?php echo e(route('admin.units.show_history',['unit'=>$unit])); ?>" class="dropdown-item"><i class="ti ti-eye"></i> Xem lịch sử</a>
                            <a href="<?php echo e(route('admin.units.show',$unit)); ?>" class="dropdown-item"><i class="ti ti-eye"></i> Xem chi tiết</a>
                            <?php if(is_roles(['admin', 'manager'])): ?>
                                <a href="<?php echo e(route('admin.units.edit',['unit'=>$unit])); ?>" class="dropdown-item"><i class="ti ti-edit"></i> Chỉnh sửa</a>
                                <div class="dropdown-divider"></div>
                                <?php if(is_admin() || $unit->created_by == Auth::id()): ?>
                                <form class="dropdown-item" action="<?php echo e(route('admin.units.destroy', ['unit'=>$unit])); ?>" method="POST" onsubmit="return confirm('Xác nhận xóa đơn vị này?');">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                    <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa đơn vị</button>
                                </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php if($units instanceof \Illuminate\Pagination\LengthAwarePaginator): ?>
        <div class="mt-2 d-flex">
            <div class="mx-auto">
                <?php echo e($units->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4")); ?>

            </div>
        </div>
    <?php endif; ?>

</div>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/units/table.blade.php ENDPATH**/ ?>