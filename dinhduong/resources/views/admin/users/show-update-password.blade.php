@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class=" col-lg-12 col-md-12 col-12">
                    <div class="row">
                        <div class="col-md-12">
                            @include('admin.users.table-detail')
                        </div>
                        <div class="col-md-12 mt-4">
                            @include('admin.users.tabs')
                        </div>

                </div>
                <div class="card border-bottom pb-4">
                    <div class="card-body">
                        <div class="card-title"><h5>Đổi mật khẩu</h5></div>
                        <form action="{{route('admin.users.update_password', $user)}}" method="POST">
                            @csrf
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                        <div class="form-icon position-relative">
                                            <i data-feather="lock" class="fea icon-sm icons"></i>
                                            <input name="password" id="password" type="password" class="form-control ps-5" placeholder="Mật khẩu" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div  class="col-md-12">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="form-icon position-relative">
                                        <i data-feather="lock" class="fea icon-sm icons"></i>
                                        <input name="password_confirmation" id="password_confirmation" type="password" class="form-control ps-5" placeholder="Xác nhận mật khẩu" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6 ta-r">
                                    <input type="submit" id="submit" name="send" class="btn btn-sm btn-primary" value="Cập nhật mật khẩu">
                                </div><!--end col-->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('foot')

@endpush


