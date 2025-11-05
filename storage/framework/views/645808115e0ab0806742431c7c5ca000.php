<?php $__env->startSection('title'); ?>
    <?php echo e($data['title']); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'home'); ?>
<?php $__env->startSection('content'); ?>
    <section class="section bg-50 bg-light d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mx-auto">
                    <div class="card  login-page bg-white shadow rounded border-0">
                        <div class="card-body">
                            <img src="<?php echo e($setting['logo-light']); ?>" width="150" class="img-fluid d-block mx-auto" alt="">
                            <h4 class="card-title text-center">Đăng nhập</h4>
                            <form action="<?php echo e(route('admin.auth.login')); ?>" method="POST" class="login-form mt-4">
                                <?php echo e(csrf_field()); ?>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Username <span class="text-danger">*</span></label>
                                            <div class="form-icon position-relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user fea icon-sm icons"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                <input required name="username" type="text" class="form-control ps-5" placeholder="Username" value="<?php echo e(old('username')); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                            <div class="form-icon position-relative">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-key fea icon-sm icons"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path></svg>
                                                <input required name="password" type="password" class="form-control ps-5" placeholder="Mật khẩu" value="<?php echo e(old('password')); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary">Đăng nhập</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app-single', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/auth/login.blade.php ENDPATH**/ ?>