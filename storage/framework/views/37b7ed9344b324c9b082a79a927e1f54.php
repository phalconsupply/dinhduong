<div class="top-header">
    <div class="header-bar d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <a href="#" class="logo-icon me-3">
                <img src=" <?php echo e($setting['logo-light']); ?>" height="30" class="small" alt="">
            </a>
            <a id="close-sidebar" class="btn btn-icon btn-soft-light" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
            </a>

        </div>
        <ul class="list-unstyled mb-0">
            <li class="list-inline-item mb-0">
                <a href="<?php echo e(url('/')); ?>" target="_blank">
                    <div class="btn btn-icon btn-soft-light"><i class="ti ti-eye"></i></div>
                </a>
            </li>
            <li class="list-inline-item mb-0 ms-1">
                <div class="dropdown dropdown-primary">
                    <button type="button" class="btn btn-soft-light dropdown-toggle p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php echo e(url('assets/images/client/05.jpg')); ?>" class="avatar avatar-ex-small rounded" alt=""></button>
                    <div class="dropdown-menu dd-menu dropdown-menu-end shadow border-0 mt-3 py-3" style="min-width: 200px;">
                        <a class="dropdown-item d-flex align-items-center text-dark pb-3" href="<?php echo e(route('admin.profile.index')); ?>">
                            <img src="<?php echo e(asset('admin-assets/images/client/05.jpg')); ?>" class="avatar avatar-md-sm rounded-circle border shadow" alt="">
                            <div class="flex-1 ms-2">
                                <span class="d-block"><?php echo e(Auth::user()->name); ?></span>
                                <?php if(!is_admin()): ?><span class="d-block small">Đơn vị: <?php echo e(Auth::user()->unit->name); ?></span><?php endif; ?>
                                <span class="d-block small">Chức vụ: <?php echo e(v('role.'.Auth::user()->role)); ?></span>
                            </div>
                        </a>
                        <a class="dropdown-item text-dark" href="<?php echo e(route('admin.dashboard.index')); ?>"><span class="mb-0 d-inline-block me-1"><i class="ti ti-home"></i></span> Tổng quát</a>
                        <a class="dropdown-item text-dark" href="<?php echo e(route('admin.dashboard.statistics')); ?>"><span class="mb-0 d-inline-block me-1"><i class="ti ti-chart-bar"></i></span> Thống kê chi tiết</a>
                        <a class="dropdown-item text-dark" href="<?php echo e(route('admin.profile.index')); ?>"><span class="mb-0 d-inline-block me-1"><i class="ti ti-user-circle"></i></span> Tài khoản</a>
                        <div class="dropdown-divider border-top"></div>
                        <a class="dropdown-item text-dark" href="<?php echo e(route('admin.auth.logout')); ?>"><span class="mb-0 d-inline-block me-1"><i class="ti ti-logout"></i></span> Đăng xuất</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- Top Header -->

<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/layouts/header.blade.php ENDPATH**/ ?>