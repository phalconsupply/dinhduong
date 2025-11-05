<?php $__env->startSection('title'); ?>
    <?php echo getSetting('site_name') ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'home'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class=" col-lg-12 col-md-12 col-12">
                    <div class="d-md-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><a href="<?php echo e(route('admin.users.index')); ?>"><i class="ti ti-table icons icon-sm"></i> Danh sách nhân sự</a></h5>
                        <nav aria-label="breadcrumb" class="d-inline-block">
                            <ul class="breadcrumb bg-transparent rounded mb-0 p-0">
                                <li class="breadcrumb-item text-capitalize">
                                    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-sm btn-success text-white"  >
                                        <i class="ti ti-plus icons icon-sm"></i>Thêm nhân sự</a></li>
                            </ul>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-4">
                            <div class="col-6">
                                <form action="" method="GET">
                                    <div class="row mb-3">
                                        <div class="col-10">
                                            <div class="form-icon position-relative">
                                                <i data-feather="search" class="fea icon-sm icons"></i>
                                                <input name="keyword" id="keyword" type="text" class="form-control ps-5" value="<?php echo e(app()->request->get('keyword')); ?>" placeholder="Từ khóa">

                                            </div>
                                        </div>
                                        <div class="col-2 row">
                                            <div class="">
                                                <button type="submit" class="btn btn-sm form-control btn-success text-white d-inline"><i data-feather="search" class="fea icon-sm icons"></i> Tìm</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-6 ta-r">
                            </div>
                            <div class="table-responsive shadow rounded">
                                <table id="history-table" class="table table-center bg-white mb-0">
                                    <thead>
                                    <tr>
                                        <th class="border-bottom ">ID</th>
                                        <th class="border-bottom ">Ảnh</th>
                                        <th class="border-bottom ">Tên/Tài khoản/CCCD</th>
                                        <th class="border-bottom " >Điện thoại/Thư điện tử</th>
                                        <th class="border-bottom " >Giới tính/Ngày sinh</th>
                                        <th class="border-bottom " >Đơn vị/Bộ phận/Chức vụ</th>
                                        <th class="border-bottom " >Địa chỉ</th>
                                        <th class="border-bottom " >Trạng thái</th>
                                        <th class="border-bottom ">Menu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="text-center"><?php echo e($user->id); ?></td>
                                                <td class="text-center">

                                                    <?php if($user->thumb): ?>
                                                        <a href="<?php echo e(route('admin.users.show_history', $user)); ?>"><img src="<?php echo e($user->thumb); ?>" class="img-thumbnail" width="80px" /></a>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('admin.users.show_history', $user)); ?>"><img src="<?php echo e(v('user.avatar')); ?>" class="img-thumbnail" width="80px" /></a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="small"><?php echo e($user->name); ?></span><br>
                                                    <span class="small"><?php echo e($user->username); ?></span><br>
                                                    <span class="small"><?php echo e($user->id_number); ?></span>
                                                </td>
                                                <td>
                                                    <span class="small"><?php echo e($user->phone); ?></span><br>
                                                    <span class="small"><?php echo e($user->email); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo e(v('gender.color.'.$user->gender)); ?>"><?php echo e(v('gender.'.$user->gender)); ?></span><br>
                                                    <span class="small"><?php echo e($user->birthday_f() ?? '#'); ?></span>
                                                </td>
                                                <td>
                                                    <span class=""><a href="<?php echo e(route('admin.units.show_history', $user->unit_id)); ?>"><?php echo e($user->unit->name); ?></a></span><br>
                                                    <span><?php echo e($user->department); ?></span><br>
                                                    <span class="badge bg-<?php echo e(v('role.color.'.$user->role)); ?>"><?php echo e(v('role.'.$user->role)); ?></span>
                                                </td>
                                                <td><span class="small"><?php echo e($user->address ?? '#'); ?></span><br>
                                                    <span class="small"><?php echo e($user->ward->full_name ?? '#'); ?>, <?php echo e($user->district->full_name ?? '#'); ?></span><br>
                                                    <span class="small"><?php echo e($user->province->full_name ?? '#'); ?></span>                                                </td>
                                                <td>
                                                    <?php if($user->is_active == 1): ?>
                                                        <span class="badge bg-success">Hoạt động</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Chưa kích hoạt</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group dropdown-primary me-2 mt-2">
                                                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Menu
                                                        </button>
                                                        <div class="dropdown-menu">

                                                            <a href="<?php echo e(route('admin.users.edit',['user'=>$user])); ?>" class="dropdown-item"><i class="ti ti-eye"></i> Xem lịch sử</a>
                                                            <a href="<?php echo e(route('admin.users.show',$user)); ?>" class="dropdown-item"><i class="ti ti-eye"></i> Xem chi tiết</a>
                                                            <?php if( is_edit_user($user) ): ?>
                                                            <a href="<?php echo e(route('admin.users.edit',['user'=>$user])); ?>" class="dropdown-item"><i class="ti ti-edit"></i> Chỉnh sửa</a>
                                                            <div class="dropdown-divider"></div>

                                                                <?php if($user->is_active): ?>
                                                                    <form class="dropdown-item" action="<?php echo e(route('admin.users.toggle_active',['user'=>$user])); ?>" method="POST" onsubmit="return confirm('Xác nhận khóa tài khoản người dùng này?');">
                                                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                                        <button class="none-btn" type="submit"><i class="ti ti-lock"></i> Khoá tài khoản</button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    <form class="dropdown-item" action="<?php echo e(route('admin.users.toggle_active',['user'=>$user])); ?>" method="POST" onsubmit="return confirm('Xác nhận mở tài khoản người dùng này?');">
                                                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                                        <button class="none-btn" type="submit"><i class="ti ti-lock-open"></i> Mở tài khoản</button>
                                                                    </form>
                                                                <?php endif; ?>


                                                            <?php endif; ?>
                                                            <?php if(is_destroy_user($user)): ?>
                                                            <form class="dropdown-item" action="<?php echo e(route('admin.users.destroy', ['user'=>$user])); ?>" method="POST" onsubmit="return confirm('Xác nhận xóa tài khoản này?');">
                                                                <input type="hidden" name="_method" value="DELETE">
                                                                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                                <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa tài khoản</button>
                                                            </form>
                                                            <?php endif; ?>
                                                        </div>
                                                </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <div class="mt-2 d-flex">
                                    <div class="mx-auto">
                                        <?php echo e($users->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4")); ?>

                                    </div>
                                </div>
                            </div>
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="DetailModal" tabindex="-1" aria-labelledby="DetailModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded shadow border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="LoginForm-title">Thông tin người dùng</h5>
                <button type="button" class="btn btn-icon btn-close" data-bs-dismiss="modal" id="close-modal"><i class="uil uil-times fs-4 text-dark"></i></button>
            </div>
            <div class="modal-body">
                <div class="detail-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-warning" data-dismiss="modal" data-bs-dismiss="modal" >Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('foot'); ?>
<script>
$(document).ready(function() {
    $(".ev-detail").click(function (){
        let id=$(this).data('id');
        let url = "<?php echo e(url('/admin/users')); ?>/"+id
        console.log(url)
        $.get(url, function(data) {
            $('#DetailModal .modal-body').html(data);
            $('#DetailModal').modal('show');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/users/index.blade.php ENDPATH**/ ?>