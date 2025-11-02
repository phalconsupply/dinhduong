<!DOCTYPE html>
<html>

<head lang="vi">
    <title><?php echo e($setting['site-title']); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="cleartype" content="on">
    <link href="<?php echo e(asset($setting['logo-light'])); ?>" rel="shortcut icon" type="image/x-icon">
    <link href="<?php echo e(asset('/web/frontend/css/all.min.css')); ?>" rel="stylesheet">
    <!--load all styles -->
    <link href="<?php echo e(asset('/web/frontend/plugins/datatimepickerbootstrap/bootstrap-datetimepicker.css')); ?>" rel="stylesheet">
    <!-- CSS Styles - NEW FLEXBOX GRID SYSTEM (replaces old Bootstrap float-based grid) -->
    <link rel="stylesheet" href="<?php echo e(asset('/web/css/flexbox-grid.css')); ?>?v=2.2" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome for Modern Form Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS for Wizard Form -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons for Modern UI -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Modern Layout CSS (WHO Statistics Style) -->
    <link rel="stylesheet" href="<?php echo e(asset('/web/css/modern-layout.css')); ?>?v=2.2" />
    <!-- Clean Form Design CSS - NEW SIMPLIFIED VERSION -->
    <link rel="stylesheet" href="<?php echo e(asset('/web/css/form-clean.css')); ?>?v=2.2" />
    <!-- Tailwind Wizard Form CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('/web/css/form-tailwind.css')); ?>" />
    <style>
        /* Force clear cache and test grid */
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
        }
        
        /* Test col-md-4 and col-md-8 */
        @media (min-width: 768px) {
            .col-md-4 {
                flex: 0 0 33.333333% !important;
                max-width: 33.333333% !important;
            }
            .col-md-8 {
                flex: 0 0 66.666667% !important;
                max-width: 66.666667% !important;
            }
        }
        
        .chosen-container-multi .chosen-choices {
            border-radius: 5px;
            min-height: 50px;
        }
    </style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>

<body>

<!-- New Header with Login and Horizontal Menu -->
<header class="main-header">
    <div class="header-top">
        <div class="container">
            <div class="header-info">
                <div class="logo-section">
                    <img src="<?php echo e(asset($setting['logo-light'])); ?>" alt="Logo">
                    <div class="logo-text">
                        <h1>Phần mềm đánh giá dinh dưỡng</h1>
                        <p><i class="fas fa-phone"></i> Hotline: <a href="tel:<?php echo e($setting['phone']); ?>" style="color: white;"><?php echo e($setting['phone']); ?></a></p>
                    </div>
                </div>
                
                <div class="header-user-section">
                    <?php if(auth()->check()): ?>
                        <div class="user-info">
                            <img src="<?php echo e(auth()->user()->thumb); ?>" alt="User Avatar">
                            <span class="user-name"><?php echo e(auth()->user()->name); ?></span>
                        </div>
                        <div class="user-actions">
                            <a href="<?php echo e(url('/admin')); ?>"><i class="fas fa-cog"></i> Quản trị</a>
                            <a href="<?php echo e(url('/auth/logout')); ?>"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    <?php else: ?>
                        <form action="<?php echo e(route('auth.login')); ?>" method="POST" class="login-form">
                            <?php echo csrf_field(); ?>
                            <input type="text" name="username" value="<?php echo e(old('username')); ?>" placeholder="Tên đăng nhập" required>
                            <input type="password" name="password" placeholder="Mật khẩu" required>
                            <input type="submit" value="Đăng nhập">
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="horizontal-menu">
        <div class="container">
            <ul class="nav-menu">
                <?php $slug = $slug ?? 'tu-0-5-tuoi'; ?>
                <li class="<?php if($slug == 'tu-0-5-tuoi'): ?> current <?php endif; ?>">
                    <a href="/tu-0-5-tuoi">
                        <i class="fas fa-baby"></i> Từ 0-5 tuổi
                    </a>
                </li>
                <li class="<?php if($slug == 'tu-5-19-tuoi'): ?> current <?php endif; ?>">
                    <a href="/tu-5-19-tuoi">
                        <i class="fas fa-child"></i> Từ 5-19 tuổi
                    </a>
                </li>
                <li class="<?php if($slug == 'tu-19-tuoi'): ?> current <?php endif; ?> disabled">
                    <a href="/tu-19-tuoi">
                        <i class="fas fa-user"></i> Trên 19 tuổi
                    </a>
                </li>
                <li class="<?php if($slug == 'who-statistics'): ?> current <?php endif; ?>">
                    <a href="/who-statistics.php">
                        <i class="fas fa-book-medical"></i> Chỉ dẫn phân loại
                    </a>
                </li>
                <li class="<?php if($slug == 'kythuatcando'): ?> current <?php endif; ?>">
                    <a href="/kythuatcando.php">
                        <i class="fas fa-ruler-combined"></i> Kỹ thuật cân đo
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/layouts/header.blade.php ENDPATH**/ ?>