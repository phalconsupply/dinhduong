@extends('admin.units.show')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('show-content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-6">
                <form action="" method="GET">
                    <div class="row mb-3">
                        <div class="col-10">
                            <div class="form-icon position-relative">
                                <i data-feather="search" class="fea icon-sm icons"></i>
                                <input name="keyword" id="keyword" type="text" class="form-control ps-5" value="{{app()->request->get('keyword')}}" placeholder="Từ khóa">

                            </div>
                        </div>
                        <div class="col-2 row">
                            <div class="">
                                <button type="submit" class="btn btn-sm form-control btn-success text-white d-inline"><i data-feather="search" class="fea icon-sm icons"></i> Tìm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6 ta-r">

            </div>
        </div>
        <table id="history-table" class="table table-center bg-white mb-0">
            <thead>
            <tr>
                <th class="border-bottom ">ID</th>
                <th class="border-bottom ">Ảnh</th>
                <th class="border-bottom ">Tên/Tài khoản/CCCD</th>
                <th class="border-bottom " >Điện thoại/Thư điện tử</th>
                <th class="border-bottom " >Giới tính/Ngày sinh</th>
                <th class="border-bottom " >Địa chỉ</th>
                <th class="border-bottom " >Bộ phận/Chức vụ</th>
                <th class="border-bottom ">Menu</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="text-center">{{$user->id}}</td>
                    <td class="text-center">

                        @if($user->thumb)
                            <a href="{{route('admin.users.show', $user)}}"><img src="{{$user->thumb}}" class="img-thumbnail" width="80px" /></a>
                        @else
                            <a href="{{route('admin.users.show', $user)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
                        @endif
                    </td>
                    <td>
                        <span class="small">{{$user->name}}</span><br>
                        <span class="small">{{$user->username}}</span><br>
                        <span class="small">{{$user->id_number}}</span>
                    </td>
                    <td>
                        <span class="small">{{$user->phone}}</span><br>
                        <span class="small">{{$user->email}}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{v('gender.color.'.$user->gender)}}">{{v('gender.'.$user->gender)}}</span><br>
                        <span class="small">{{$user->birthday_f() ?? '#'}}</span>
                    </td>
                    <td><span class="small">{{$user->address ?? '#'}}</span><br>
                        <span class="small">{{$user->ward->full_name ?? '#'}}, {{$user->district->full_name ?? '#'}}</span><br>
                        <span class="small">{{$user->province->full_name ?? '#'}}</span>
                    </td>
                    <td>
                        <span class="">{{$user->department}}</span><br>
                        <span class="badge bg-{{v('role.color.'.$user->role)}}">{{v('role.'.$user->role)}}</span>
                    </td>
                    <td>
                        <div class="btn-group dropdown-primary me-2 mt-2">
                            <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Menu
                            </button>
                            <div class="dropdown-menu">

                                <a href="{{route('admin.users.edit',['user'=>$user])}}" class="dropdown-item"><i class="ti ti-eye"></i> Xem lịch sử</a>
                                <a href="{{route('admin.users.show',$user)}}" class="dropdown-item"><i class="ti ti-eye"></i> Xem chi tiết</a>
                                <a href="{{route('admin.users.edit',['user'=>$user])}}" class="dropdown-item"><i class="ti ti-edit"></i> Chỉnh sửa</a>
                                <div class="dropdown-divider"></div>
                                @if($user->role === 'employee' && (is_admin() || is_manager()))
                                    @if($user->is_active)
                                        <form class="dropdown-item" action="{{route('admin.users.toggle_active',['user'=>$user])}}" method="POST" onsubmit="return confirm('Xác nhận khóa tài khoản người dùng này?');">
                                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                                            <button class="none-btn" type="submit"><i class="ti ti-lock"></i> Khoá tài khoản</button>
                                        </form>
                                    @else
                                        <form class="dropdown-item" action="{{route('admin.users.toggle_active',['user'=>$user])}}" method="POST" onsubmit="return confirm('Xác nhận mở tài khoản người dùng này?');">
                                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                                            <button class="none-btn" type="submit"><i class="ti ti-lock-open"></i> Mở tài khoản</button>
                                        </form>
                                    @endif
                                @endif
                                @if($user->role === 'employee' && (is_admin() || is_manager()))
                                <form class="dropdown-item" action="{{route('admin.users.destroy', ['user'=>$user])}}" method="POST" onsubmit="return confirm('Xác nhận xóa tài khoản này?');">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa tài khoản</button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('foot')

@endpush


