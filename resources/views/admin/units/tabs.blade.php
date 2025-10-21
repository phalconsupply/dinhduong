<ul class="nav nav-pills flex-column flex-sm-row rounded" id="pills-tab" role="tablist">

    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'history') active @endif" href="{{route('admin.units.show_history',$unit)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-history icons icon-sm"></i> Lịch sử</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
    @if(is_roles(['admin', 'manager']))
    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'users') active @endif" href="{{route('admin.units.show_users',$unit)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-user-circle icons icon-sm"></i> Nhân sự</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
    @endif
    <li class="nav-item">
        <a class="nav-link rounded @if($tab == 'detail') active @endif" href="{{route('admin.units.show',$unit)}}">
            <div class="text-center py-2">
                <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Thông tin chi tiết</h6>
            </div>
        </a><!--end nav link-->
    </li><!--end nav item-->
</ul><!--end nav pills-->
