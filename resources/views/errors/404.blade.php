@php
    $seg = Request::segment(1);
    $prefix = 'web';
    if($seg == 'admin'){
        $prefix = 'admin';
        $layout= 'admin.layouts.app-full';
    }else{
        $layout= 'layouts.app';
    }
@endphp
@extends($layout)
@section('title')
    404
@endsection
@section('body_class', 'home')
@section('content')
    @if($prefix == 'admin')
        <div class="container-fluid">
            <div class="layout-specing">
                <section class=" d-flex align-items-center">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 col-md-12 text-center">
                                <img src="{{asset('assets/images/404.svg')}}" class="img-fluid" style="max-width: 400px" alt="">
                                <div class="text-uppercase mt-4 display-3">Oh ! no</div>
                                <div class="text-capitalize text-dark mb-4 error-page">Page Not Found</div>
                            </div><!--end col-->
                        </div><!--end row-->

                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{url()->previous() }}" class="btn btn-outline-primary mt-4">Về trang trước</a>
                                <a href="{{url('/')}}" class="btn btn-primary mt-4 ms-2">Về trang chủ</a>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end container-->
                </section><!--end section-->
            </div>
        </div>

    @else
        <section id="nuti-nutritional-status" style="padding-top: 20px;">
            <div class="container">
                <div class="row">
                    {{-- Removed sidebar, now full width --}}
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="tab">
                            <div id="tab-1" class="nutrition-menu-content text-center">
                                <h3 class="heading">Không tìm thấy nội dung</h3>
                            </div>
                            <div class="col-md-12 text-center">
                                <a href="{{url()->previous() }}" class="btn btn-outline-primary mt-4">Về trang trước</a>
                                <a href="{{url('/')}}" class="btn btn-primary mt-4 ms-2">Về trang chủ</a>
                            </div><!--end col-->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

@endsection
