<!DOCTYPE html>
<html>

<head lang="vi">
    <title>{{$setting['site-title']}}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <!-- Tailwind CSS for Wizard Form -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons for Modern UI -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Modern Layout CSS (WHO Statistics Style) -->
    <link rel="stylesheet" href="{{asset('/web/css/modern-layout.css')}}?v=2.2" />
    <!-- Clean Form Design CSS - NEW SIMPLIFIED VERSION -->
    <link rel="stylesheet" href="{{asset('/web/css/form-clean.css')}}?v=2.2" />
    <!-- Tailwind Wizard Form CSS -->
    <link rel="stylesheet" href="{{asset('/web/css/form-tailwind.css')}}" />
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

        .dropdown:hover .dropdown-content {
            display: block;
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
                    <a href="#">
                        <i class="fas fa-book"></i> Documents
                    </a>
                    <div class="dropdown-content">
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
        </div>
    </div>
</header>
