<ul class="nav nav-pills flex-column flex-sm-row rounded" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link rounded  @if($tab == 'history') active @endif" href="{{route('admin.users.show_history',$user)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-history icons icon-sm"></i> Lịch sử</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'roles') active @endif" href="{{route('admin.users.show_roles',$user)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-shield icons icon-sm"></i> Vai trò</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'update_password') active @endif"  href="{{route('admin.users.update_password',$user)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-lock-access icons icon-sm"></i> Đổi mật khẩu</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'detail') active @endif" href="{{route('admin.users.show',$user)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Thông tin chi tiết</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
</ul><!--end nav pills-->
