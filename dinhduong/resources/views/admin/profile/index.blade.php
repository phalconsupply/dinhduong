@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="d-md-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin tài khoản</h5>

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
                            <a class="nav-link rounded active" id="info-tab" href="{{route('admin.profile.index')}}" >
                                <div class="text-center py-2">
                                    <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Tổng quan</h6>
                                </div>
                            </a><!--end nav link-->
                        </li><!--end nav item-->

                        <li class="nav-item">
                            <a class="nav-link rounded " id="info-tab"  href="{{route('admin.profile.changepassword')}}" role="tab" >
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
                    <form method="POST" action="{{route('admin.profile.update')}}">
                        @csrf
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Tên</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-user fea icon-sm icons"></i>
                                        <input name="name" id="name" value="{{ old('name', $user->name) }}" type="text" {{$is_disable}} class="form-control ps-5" placeholder="Nhập tên">
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-link fea icon-sm icons"></i>
                                        <input name="username" value="{{ $user->username }}" id="username" type="text" {{$is_disable}} class="form-control ps-5" disabled>
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-mail fea icon-sm icons"></i>
                                        <input name="email" value="{{ old('email', $user->email) }}" id="email" type="text" {{$is_disable}} class="form-control ps-5" placeholder="Nhập email">
                                    </div>
                                </div>
                            </div><!--end col-->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <div class="form-icon position-relative">
                                        <i class="ti ti-phone fea icon-sm icons"></i>
                                        <input name="phone" value="{{ old('phone', $user->phone) }}" id="phone" type="text" {{$is_disable}} class="form-control ps-5" placeholder="Nhập số điện thoại">                                            </div>
                                </div>
                            </div><!--end col-->

                            <div class="col-md-12">
                                <div class="form-check form-switch">
                                    <input name="is_active" class="form-check-input" type="checkbox" id="is_active" @if($user->is_active==1) checked @endif disabled >
                                    <label class="form-check-label" for="is_active">Trạng thái</label>
                                </div>
                            </div>

                        </div><!--end row-->
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" id="submit" name="send" class="btn btn-primary" value="Cập nhật thông tin">
                            </div><!--end col-->
                        </div><!--end row-->
                    </form><!--end form-->
                </div><!--end col-->
            </div><!--end row-->

        </div>
    </div>

@endsection


