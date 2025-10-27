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
    <!-- CSS Styles -->
    <link rel="stylesheet" href="{{asset('/web/css/a585b32.css')}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome for Modern Form Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Modern Form Design CSS -->
    <link rel="stylesheet" href="{{asset('/web/css/form-modern.css')}}" />
    <style>
        .chosen-container-multi .chosen-choices {
            border-radius: 5px;
            min-height: 50px;
        }
        
        /* New Header Styles */
        .main-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-top {
            background: rgba(255,255,255,0.1);
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .header-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }
        
        .logo-section img {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: white;
            padding: 5px;
        }
        
        .logo-text h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: white;
        }
        
        .logo-text p {
            margin: 0;
            font-size: 13px;
            color: rgba(255,255,255,0.9);
        }
        
        .header-user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .user-name {
            font-weight: 500;
        }
        
        .login-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .login-form input {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 150px;
        }
        
        .login-form input[type="submit"] {
            background: #4CAF50;
            color: white;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .login-form input[type="submit"]:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .user-actions a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .user-actions a:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* Horizontal Menu */
        .horizontal-menu {
            background: rgba(255,255,255,0.95);
            padding: 0;
        }
        
        .nav-menu {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        
        .nav-menu li {
            position: relative;
        }
        
        .nav-menu li a {
            display: block;
            padding: 15px 30px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .nav-menu li a:hover,
        .nav-menu li.current a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-color: #4CAF50;
        }
        
        .nav-menu li.disabled a {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f5f5f5;
        }
        
        .nav-menu li a i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .header-info {
                flex-direction: column;
                gap: 15px;
            }
            
            .login-form {
                flex-direction: column;
                width: 100%;
            }
            
            .login-form input[type="text"],
            .login-form input[type="password"] {
                width: 100%;
            }
            
            .nav-menu {
                flex-direction: column;
            }
            
            .nav-menu li a {
                padding: 12px 20px;
            }
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
                    <img src="{{asset($setting['logo-light'])}}" alt="Logo">
                    <div class="logo-text">
                        <h1>Phần mềm đánh giá dinh dưỡng</h1>
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
                    <a href="/tu-0-5-tuoi">
                        <i class="fas fa-baby"></i> Từ 0-5 tuổi
                    </a>
                </li>
                <li class="@if($slug == 'tu-5-19-tuoi') current @endif">
                    <a href="/tu-5-19-tuoi">
                        <i class="fas fa-child"></i> Từ 5-19 tuổi
                    </a>
                </li>
                <li class="@if($slug == 'tu-19-tuoi') current @endif disabled">
                    <a href="/tu-19-tuoi">
                        <i class="fas fa-user"></i> Trên 19 tuổi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
