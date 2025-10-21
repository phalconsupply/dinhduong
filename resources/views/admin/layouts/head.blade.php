<meta charset="utf-8" />
<title>{{$setting['app_title']}}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{$setting['site-description']}}" />
<meta name="keywords" content="{{$setting['site-keywords']}}" />
<meta name="author" content="{{$setting['site-domain']}}" />
<meta name="email" content="{{$setting['email']}}" />
<meta name="website" content="{{url('/')}}" />
<meta name="Version" content="{{Config::get('app.version')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- favicon -->
<link rel="shortcut icon" href="{{ asset($setting['logo-light']) }}" />
<!-- Css -->
<link href="{{ asset('admin-assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">
<link href="{{asset('admin-assets/libs/tiny-slider/tiny-slider.css')}}" rel="stylesheet">
<!-- Bootstrap Css -->
<link href="{{ asset('admin-assets/css/bootstrap.min.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ asset('admin-assets/libs/@mdi/font/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('admin-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin-assets/libs/@iconscout/unicons/css/line.css') }}" type="text/css" rel="stylesheet" />
<!-- Style Css-->
<link href="{{ asset('admin-assets/css/style.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
<link href="{{ asset('admin-assets/css/admin.css') }}" class="theme-opt" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.css" integrity="sha512-8D+M+7Y6jVsEa7RD6Kv/Z7EImSpNpQllgaEIQAtqHcI0H6F4iZknRj0Nx1DCdB+TwBaS+702BGWYC0Ze2hpExQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
