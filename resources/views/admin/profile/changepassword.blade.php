@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="d-md-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cập nhật mật khẩu</h5>

                <nav aria-label="breadcrumb" class="d-inline-block">
                    <ul class="breadcrumb bg-transparent rounded mb-0 p-0">
                        <li class="breadcrumb-item text-capitalize"><a href="{{url('/')}}">Trang chủ</a></li>
                        <li class="breadcrumb-item text-capitalize"><a href="#">Tài khoản</a></li>
                        <li class="breadcrumb-item text-capitalize active" aria-current="page">Chi tiết</li>
                    </ul>
                </nav>
            </div>
            <div class="row mt-3">
                <div class="col-lg-12">
                    <ul class="nav nav-pills nav-tabs flex-column flex-sm-row rounded" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link rounded " id="info-tab" href="{{route('admin.profile.index')}}" >
                                <div class="text-center py-2">
                                    <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Tổng quan</h6>
                                </div>
                            </a><!--end nav link-->
                        </li><!--end nav item-->

                        <li class="nav-item">
                            <a class="nav-link rounded active" id="info-tab"  href="{{route('admin.profile.changepassword')}}" role="tab" >
                                <div class="text-center py-2">
                                    <h6 class="mb-0"><i class="ti ti-lock icons icon-sm"></i> Đổi mật khẩu</h6>
                                </div>
                            </a><!--end nav link-->
                        </li><!--end nav item-->

                    </ul><!--end nav pills-->
                </div><!--end col-->
            </div><!--end row-->

            <div class="row">
                <div class="col-12">
                    <form method="POST" action="{{route('admin.profile.update_password')}}">
                        {{csrf_field()}}
                        <div class="row ">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Mật khẩu</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-lock fea icon-sm icons"></i>
                                        <input name="password" value="{{ old('password') }}" id="password" type="password" class="form-control ps-5" placeholder="Nhập mật khẩu">                                            </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Xác nhận mật khẩu</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-lock fea icon-sm icons"></i>
                                        <input name="password_confirmation" value="{{ old('password_confirmation') }}" id="password_confirmation" type="password" class="form-control ps-5" placeholder="Xác nhận mật khẩu">                                            </div>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" id="submit" name="send" class="btn btn-primary" value="Thiết lập mật khẩu">
                            </div><!--end col-->
                        </div><!--end row-->
                    </form><!--end form-->
                </div><!--end col-->
            </div><!--end row-->

        </div>
    </div>

@endsection


