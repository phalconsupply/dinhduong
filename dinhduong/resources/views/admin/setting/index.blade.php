@extends('admin.layouts.app-full')
@section('title')
    Cấu hình
@endsection
@section('body_class', 'home')
@section('content')
    <section class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 d-lg-block d-none">
                    @include('admin.setting.sidebar')
                </div><!--end col-->

                <div class=" col-lg-8 col-12">

                    <div class="card border-bottom pb-4">
                        <div class="card-body">
                            <h5>Cấu hình hệ thống</h5>
                            <form method="POST" action="{{route('admin.setting.update')}}">
                                {{csrf_field()}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Logo light</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="logo-light" value="{{$setting['logo-light']}}" onclick="selectFileWithCKFinder('logo-light')" id="logo-light" type="text" class="form-control ps-5" placeholder="Mật khẩu hiện tại :">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tên công ty</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="name"  value="{{$setting['name']}}" id="name"  type="text" class="form-control ps-5" placeholder="Nhập tên công ty">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Số điện thoại</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-phone fea icon-sm icons"></i>
                                                <input name="phone" value="{{$setting['phone']}}" id="phone" type="text" class="form-control ps-5" placeholder="Nhập số điện thoại">
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Số hotline</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-phone fea icon-sm icons"></i>
                                                <input name="hotline" value="{{$setting['hotline']}}" id="hotline" type="text" class="form-control ps-5" placeholder="Nhập hotline">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Địa chỉ công ty</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-map fea icon-sm icons"></i>
                                                <input name="address" value="{{$setting['address']}}" id="address" type="text" class="form-control ps-5" placeholder="Nhập địa chỉ công ty">
                                            </div>
                                        </div>
                                    </div><!--end col-->


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Tiêu đề website (meta title)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-hexagon fea icon-sm icons"></i>
                                                <input name="site-title" value="{{$setting['site-title']}}" id="site-title" type="text" class="form-control ps-5" placeholder="Nhập tiêu đề website">
                                            </div>
                                        </div>
                                    </div><!--end col-->

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Mô tả website (meta description)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                <textarea name="site-description" class="form-control ps-5">{!! $setting['site-description'] !!}</textarea>
                                            </div>
                                        </div>
                                    </div><!--end col-->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Từ khóa website (meta keywords)</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                <textarea name="site-keywords" class="form-control ps-5">{!! $setting['site-keywords'] !!}</textarea>
                                            </div>
                                        </div>
                                    </div><!--end col-->

                                </div><!--end row-->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="submit" id="submit" name="send" class="btn btn-primary" value="Cập nhật">
                                    </div><!--end col-->
                                </div><!--end row-->
                            </form><!--end form-->
                        </div><!--end col-->
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section>
@endsection
