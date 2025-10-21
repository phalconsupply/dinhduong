<div class="card border-0">
    <div class="d-flex justify-content-between p-4 shadow rounded-top">
        <h6 class="fw-bold mb-0">Nhân sự</h6>
    </div>
    <div class="table-responsive shadow rounded-bottom" data-simplebar="init" style="height: 545px;">
        <div class="simplebar-wrapper" style="margin: 0px;">
            <div class="simplebar-height-auto-observer-wrapper">
                <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: scroll;">
                        <div class="simplebar-content" style="padding: 0px;">
                            <table class="table table-center bg-white mb-0">
                                <thead>
                                <tr>
                                    <th class="border-bottom ">ID</th>
                                    <th class="border-bottom ">Ảnh</th>
                                    <th class="border-bottom ">Tên/Tài khoản/CCCD</th>
                                    <th class="border-bottom " >Điện thoại/Thư điện tử</th>
                                    <th class="border-bottom " >Giới tính/Ngày sinh</th>
                                    <th class="border-bottom " >Đơn vị/Vai trò</th>
                                    <th class="border-bottom " >Trạng thái</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Start -->
                                @foreach($members as $user)
                                    <tr>
                                        <td class="text-center">{{$user->id}}</td>
                                        <td class="text-center">
                                            @if($user->thumb)
                                                <a href="{{route('admin.users.show_history', $user)}}"><img src="{{$user->thumb}}" class="img-thumbnail" width="80px" /></a>
                                            @else
                                                <a href="{{route('admin.users.show_history', $user)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
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
                                            <span class=""><a href="{{route('admin.units.show_history', $user->unit_id ?? 0)}}">{{$user->unit->name ?? '#'}}</a></span><br>
                                            <span>{{$user->department}}</span><br>
                                            <span class="badge bg-{{v('role.color.'.$user->role)}}">{{v('role.'.$user->role)}}</span>
                                        </td>
                                        <td>
                                            @if($user->is_active == 1)
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Chưa kích hoạt</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- End -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-placeholder" style="width: auto; height: 747px;"></div>
        </div>
        <div class="simplebar-track simplebar-horizontal" style="visibility: visible;">
            <div class="simplebar-scrollbar" style="width: 666px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
        </div>
        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
            <div class="simplebar-scrollbar" style="height: 397px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
        </div>
    </div>
</div>
