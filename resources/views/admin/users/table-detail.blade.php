<div class=" table-responsive shadow rounded">
    <table id="history-table" class="table table-center bg-white mb-0">
        <thead class="">
        <tr>
            <th class="border-bottom ">Ảnh</th>
            <th class="border-bottom ">Tên/Tài khoản/CCCD</th>
            <th class="border-bottom " >Điện thoại/Thư điện tử</th>
            <th class="border-bottom " >Giới tính/Ngày sinh</th>
            <th class="border-bottom " >Đơn vị/Vai trò</th>
            <th class="border-bottom " >Địa chỉ</th>
            <th class="border-bottom " >Trạng thái</th>
            <th class="border-bottom ">Menu</th>
        </tr>
        </thead>
        <tbody>
        <tr>
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
            <td>
                <span class=""><a href="{{route('admin.units.show_history', $user->unit_id)}}">{{$user->unit->name}}</a></span><br>
                <span class="badge bg-{{v('role.color.'.$user->role)}}">{{v('role.'.$user->role)}}</span>
            </td>
            <td><span class="small">{{$user->address ?? '#'}}</span><br>
                <span class="small">{{$user->ward->full_name ?? '#'}}, {{$user->district->full_name ?? '#'}}</span><br>
                <span class="small">{{$user->province->full_name ?? '#'}}</span>
            </td>
            <td>
                @if($user->is_active == 1)
                    <span class="badge bg-success">Hoạt động</span>
                @else
                    <span class="badge bg-danger">Chưa kích hoạt</span>
                @endif
            </td>
            <td>
                <div class="btn-group dropdown-primary me-2 mt-2">
                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Menu
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{route('admin.users.edit',['user'=>$user])}}" class="dropdown-item"><i class="ti ti-edit"></i> Chỉnh sửa</a>
                        <div class="dropdown-divider"></div>
                        @if($user->username != 'admin')
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
        </tbody>
    </table>
</div>
