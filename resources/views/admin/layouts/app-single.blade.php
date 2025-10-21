
<!doctype html>
<html lang="en" dir="ltr">

<head>
    @include('admin.layouts.head')
    @stack('head')
</head>

<body>
<!-- Loader -->
<!-- <div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
</div> -->
<!-- Loader -->
@include('admin.layouts.alert')
@yield('content')
<!-- javascript -->
<!-- JAVASCRIPT -->
<script src="{{ asset('admin-assets/js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/simplebar/simplebar.min.js') }}"></script>
<!-- Main Js -->
<script src="{{ asset('admin-assets/js/plugins.init.js') }}"></script>
<script src="{{ asset('admin-assets/js/app.js') }}"></script>
@stack('foot')
</body>

</html>
