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
                        <div class="table-responsive bg-white shadow rounded">
                            <table class="table table-hover mb-0 table-center">
                                <tbody>
                                <tr>
                                    <th scope="row">Tên</th>
                                    <td>{{$user->name}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Tên tài khoản</th>
                                    <td>{{$user->username}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Số căn cước</th>
                                    <td>{{$user->id_number}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Số điện thoại</th>
                                    <td>{{$user->phone}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Thư điện tử</th>
                                    <td>{{$user->email}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Giới tính</th>
                                    <td>{{v('gender.'.$user->gender)}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Ngày sinh</th>
                                    <td>{{$user->birthday_f() ?? '#'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Địa chỉ</th>
                                    <td>{{$user->address}}, <span class="small">{{$user->ward->full_name ?? '#'}}, {{$user->district->full_name ?? '#'}}, {{$user->province->full_name ?? '#'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Được tạo bởi</th>
                                    <td>{{$user->creator->name ?? '#'}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Ghi chú</th>
                                    <td>{{$user->note}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Ngày tạo</th>
                                    <td>{{$user->created_at->format('d-m-Y')}}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Ngày cập nhật gần đây</th>
                                    <td>{{$user->updated_at->format('d-m-Y')}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>    </div>
                </div>
            </div>
        </div>


@endsection

@push('foot')

@endpush


