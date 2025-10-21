
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

<div class="page-wrapper toggled">
    <!-- sidebar-wrapper -->
    @include('admin.layouts.nav')
    <!-- sidebar-wrapper  -->

    <!-- Start Page Content -->
    <main class="page-content bg-light">
        <!-- Top Header -->
        @include('admin.layouts.header')
        <!-- Top Header -->
        @include('admin.layouts.alert')
        @yield('content')
        <!-- Footer -->
        @include('admin.layouts.modal')
        @include('admin.layouts.footer')
        <!-- Footer -->
    </main>
    <!--End page-content" -->
</div>

<!-- javascript -->
<!-- JAVASCRIPT -->
<script src="{{ asset('admin-assets/js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/feather-icons/feather.min.js') }}"></script>
<script src="{{ asset('admin-assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{asset('admin-assets/libs/tiny-slider/min/tiny-slider.js')}}"></script>
<script src="{{ asset('admin-assets/libs/ckfinder/ckfinder.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin-assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js" integrity="sha512-zlWWyZq71UMApAjih4WkaRpikgY9Bz1oXIW5G0fED4vk14JjGlQ1UmkGM392jEULP8jbNMiwLWdM8Z87Hu88Fw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Main Js -->
<script src="{{ asset('admin-assets/js/plugins.init.js') }}"></script>
<script>
    function showToast(type = 'success', message = 'Thao tác thành công') {
        $.toast({
            heading: type === 'success' ? 'Thành công' : 'Lỗi',
            text: message,
            icon: type,
            position: 'top-right',
            loaderBg: type === 'success' ? '#51d28c' : '#f2a654'
        });
    }

    function selectFileWithCKFinder( elementId ) {
        CKFinder.popup( {
            chooseFiles: true,
            width: 800,
            height: 600,
            onInit: function( finder ) {
                finder.on( 'files:choose', function( evt ) {
                    var file = evt.data.files.first();
                    var output = document.getElementById( elementId );
                    output.value = file.getUrl();
                } );

                finder.on( 'file:choose:resizedImage', function( evt ) {
                    var output = document.getElementById( elementId );
                    output.value = evt.data.resizedUrl;
                } );
            }
        } );
    }

</script>
<script src="{{ asset('admin-assets/js/app.js') }}"></script>
@stack('foot')
</body>

</html>
