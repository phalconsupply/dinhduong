<!DOCTYPE html>
<html lang="vi">

<head>
    <title>{{$setting['site-title']}}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="cleartype" content="on">
    <link href="{{asset($setting['logo-light'])}}" rel="shortcut icon" type="image/x-icon">
    <link href="{{asset('/web/frontend/css/all.min.css')}}" rel="stylesheet">
    <!--load all styles -->
    <link href="{{asset('/web/frontend/plugins/datatimepickerbootstrap/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <!-- CSS Styles - NEW FLEXBOX GRID SYSTEM (replaces old Bootstrap float-based grid) -->
    <link rel="stylesheet" href="{{asset('/web/css/flexbox-grid.css')}}?v=2.2" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome for Modern Form Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Modern Layout CSS (WHO Statistics Style) -->
    <link rel="stylesheet" href="{{asset('/web/css/modern-layout.css')}}?v=2.2" />
    <!-- Clean Form Design CSS - NEW SIMPLIFIED VERSION -->
    <link rel="stylesheet" href="{{asset('/web/css/form-clean.css')}}?v=2.6" />
    <style>
        .chosen-container-multi .chosen-choices {
            border-radius: 5px;
            min-height: 50px;
        }

        /* Dropdown Menu Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 220px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1001;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            top: 100%;
            left: 0;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-weight: 500;
            border-bottom: none !important;
            transition: all 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f8f9ff;
            color: #667eea;
        }

        .dropdown-content a.active {
            background-color: #f8f9ff;
            color: #667eea;
            font-weight: 600;
        }

        /* Mở bằng click/bàn phím (lớp .mo do script bên dưới gắn); hover chỉ trên thiết bị có chuột */
        .dropdown.mo .dropdown-content {
            display: block;
        }
        @media (hover: hover) {
            .dropdown:hover .dropdown-content {
                display: block;
            }
        }
        .dropdown > a:focus-visible,
        .dropdown-content a:focus-visible {
            outline: 3px solid #667eea;
            outline-offset: 2px;
        }

        .dropdown > a:after {
            content: ' ▼';
            font-size: 0.8em;
            margin-left: 5px;
        }

        /* Update container-header to container */
        .header-top .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .horizontal-menu .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
    </style>
    @stack('head')
    <!-- Preflight Tailwind bản tĩnh: nạp cuối cùng để giữ thứ tự cascade như khi còn dùng Play CDN -->
    <link rel="stylesheet" href="{{asset('/web/css/tailwind-preflight.css')}}?v=1" />
</head>

<body>

<!-- New Header with Login and Horizontal Menu -->
<header class="main-header">
    <div class="header-top">
        <div class="container">
            <div class="header-info">
                <div class="logo-section">
                    <a href="/"><img src="{{asset($setting['logo-light'])}}" alt="Logo" onerror="this.style.display='none'"></a>
                    <div class="logo-text">
                        <h1><a href="/" style="color: white; text-decoration: none;">Phần mềm đánh giá dinh dưỡng</a></h1>
                        <p><i class="fas fa-phone"></i> Hotline: <a href="tel:{{$setting['phone']}}" style="color: white;">{{$setting['phone']}}</a></p>
                    </div>
                </div>
                
                <div class="header-user-section">
                    @if(auth()->check())
                        <div class="user-info">
                            <img src="{{auth()->user()->thumb}}" alt="User Avatar">
                            <span class="user-name">{{auth()->user()->name}}</span>
                        </div>
                        <div class="user-actions">
                            <a href="{{url('/admin')}}"><i class="fas fa-cog"></i> Quản trị</a>
                            <a href="{{url('/auth/logout')}}"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        </div>
                    @else
                        <form action="{{route('auth.login')}}" method="POST" class="login-form">
                            @csrf
                            <input type="text" name="username" value="{{old('username')}}" placeholder="Tên đăng nhập" required>
                            <input type="password" name="password" placeholder="Mật khẩu" required>
                            <input type="submit" value="Đăng nhập">
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="horizontal-menu">
        <div class="container">
            <ul class="nav-menu">
                <?php $slug = $slug ?? 'tu-0-5-tuoi'; ?>
                <li class="@if($slug == 'tu-0-5-tuoi') current @endif">
                    <a href="/">
                        <i class="fas fa-baby"></i> Từ 0-5 tuổi
                    </a>
                </li>
                <li class="@if($slug == 'tu-5-19-tuoi') current @endif">
                    <a href="/tu-5-19-tuoi">
                        <i class="fas fa-child"></i> Từ 5-19 tuổi
                    </a>
                </li>
                <li class="@if($slug == 'tu-19-tuoi') current @endif">
                    <a href="/tu-19-tuoi">
                        <i class="fas fa-user"></i> Trên 19 tuổi
                    </a>
                </li>
                <li class="dropdown @if(in_array($slug, ['who-statistics', 'kythuatcando', 'huong-dan'])) current @endif">
                    <a href="#menu-tai-lieu" id="nut-tai-lieu" role="button" aria-haspopup="true" aria-expanded="false" aria-controls="menu-tai-lieu">
                        <i class="fas fa-book" aria-hidden="true"></i> Documents
                    </a>
                    <div class="dropdown-content" id="menu-tai-lieu" aria-labelledby="nut-tai-lieu">
                        <a href="/who-statistics.php" @if($slug == 'who-statistics') class="active" @endif>
                            <i class="fas fa-book-medical"></i> Chỉ dẫn phân loại WHO
                        </a>
                        <a href="/kythuatcando.php" @if($slug == 'kythuatcando') class="active" @endif>
                            <i class="fas fa-ruler-combined"></i> Kỹ thuật cân đo
                        </a>
                        <a href="/huong-dan-danh-gia-dinh-duong.html" @if($slug == 'huong-dan') class="active" @endif>
                            <i class="fas fa-chart-line"></i> Hướng dẫn đánh giá dinh dưỡng
                        </a>
                    </div>
                </li>
            </ul>
            <script>
                // Menu "Documents": mở bằng click / Enter / Space, đóng bằng Escape hoặc click ra ngoài.
                // Trên màn hình hẹp, thanh menu cuộn ngang sẽ cắt menu con nên dùng định vị fixed.
                (function () {
                    var nut = document.getElementById('nut-tai-lieu');
                    if (!nut) return;
                    var muc = nut.parentElement, menu = document.getElementById('menu-tai-lieu');
                    function datMo(mo) {
                        muc.classList.toggle('mo', mo);
                        nut.setAttribute('aria-expanded', mo ? 'true' : 'false');
                        if (mo && window.innerWidth < 768) {
                            var r = nut.getBoundingClientRect();
                            menu.style.position = 'fixed';
                            menu.style.top = r.bottom + 'px';
                            menu.style.left = Math.max(8, Math.min(r.left, window.innerWidth - 228)) + 'px';
                        } else {
                            menu.style.position = menu.style.top = menu.style.left = '';
                        }
                    }
                    nut.addEventListener('click', function (e) { e.preventDefault(); datMo(!muc.classList.contains('mo')); });
                    nut.addEventListener('keydown', function (e) {
                        if (e.key === ' ') { e.preventDefault(); datMo(!muc.classList.contains('mo')); }
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && muc.classList.contains('mo')) { datMo(false); nut.focus(); }
                    });
                    document.addEventListener('click', function (e) { if (!muc.contains(e.target)) datMo(false); });
                    window.addEventListener('scroll', function () { if (window.innerWidth < 768) datMo(false); }, {passive: true});
                })();
            </script>
        </div>
    </div>
</header>
