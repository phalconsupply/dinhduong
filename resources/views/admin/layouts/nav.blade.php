<nav id="sidebar" class="sidebar-wrapper sidebar-light">
    <div class="sidebar-content" data-simplebar="init" style="height: calc(100% - 60px);">
        <div class="simplebar-wrapper" style="margin: 0px;">
            <div class="simplebar-height-auto-observer-wrapper">
                <div class="simplebar-height-auto-observer"></div>
            </div>
            <div class="simplebar-mask">
                <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                        <div class="simplebar-content" style="padding: 0px;">
                            <div class="sidebar-brand active">
                                <a href="{{url('/')}}">
                                    <img src="{{$setting['logo-light']}}" height="40" class="logo-light-mode" alt="">
                                    <span style="font-size: 13px; font-weight:bold ;">{{$setting['name']}}</span>
                                </a>
                            </div>
                            <ul class="sidebar-menu" style="height: 100%;">
                                <li class=" active">
                                    <a href="{{url('/admin')}}"><i class="ti ti-home me-2"></i>Thống kê</a>
                                </li>

                                <li class="active">
                                    <a href="{{route('admin.history.index')}}"><i class="ti ti-history me-2"></i>Khảo sát</a>
                                </li>
                                @if(is_admin() || is_super_admin_province() )
                                <li class="sidebar-dropdown active">
                                    <a href="{{route('admin.units.index')}}"><i class="ti ti-shield me-2"></i> Đơn vị</a>
                                    <div class="sidebar-submenu d-block">
                                        <ul>
                                            @if(!is_admin())<li><a href="{{route('admin.units.show', ['unit'=>Auth::user()->unit_id])}}">Đơn vị của tôi</a></li>@endif
                                            <li><a href="{{route('admin.units.index')}}">Danh sách đơn vị</a></li>
                                            <li><a href="{{route('admin.units.create')}}">Thêm đơn vị</a></li>
                                        </ul>
                                    </div>
                                </li>
                                @else
                                    <li><a href="{{route('admin.units.show', ['unit'=>Auth::user()->unit_id])}}"><i class="ti ti-shield me-2"></i> Đơn vị của tôi</a></li>
                                @endif
                                @if(is_roles(['admin','manager']))
                                <li class="sidebar-dropdown active">
                                    <a href="{{route('admin.users.index')}}"><i class="ti ti-users me-2"></i>Nhân sự</a>
                                    <div class="sidebar-submenu d-block">
                                        <ul>
                                            <li><a href="{{route('admin.users.index')}}">Tất cả nhân sự</a></li>
                                            <li><a href="{{route('admin.users.create')}}">Thêm tài khoản</a></li>
                                        </ul>
                                    </div>
                                </li>
                                @endif
                                @if(is_admin())
                                <li class="sidebar-dropdown active">
                                    <a href="{{route('admin.setting.index')}}"><i class="ti ti-settings me-2"></i>Cấu hình</a>
                                    <div class="sidebar-submenu d-block">
                                        <ul>
                                            <li><a href="{{route('admin.setting.index')}}">Tổng quan</a></li>
                                            <li><a href="{{route('admin.type.index',  ['tab'=>'weight-for-age'])}}">Đối tượng</a></li>
                                            <li><a href="{{route('admin.setting.advices')}}">Lời khuyên</a></li>
                                        </ul>
                                    </div>
                                </li>
                                @endif
                                <li class="sidebar-dropdown active">
                                    <a href="{{route('admin.profile.index')}}"><i class="ti ti-user me-2"></i>Tài khoản của tôi</a>
                                    <div class="sidebar-submenu d-block">
                                        <ul>
                                            <li><a href="{{route('admin.profile.index')}}">Tổng quan</a></li>
                                            <li><a href="{{route('admin.profile.changepassword')}}">Đổi mật khẩu</a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li  class="active"><a href="{{route('admin.media.index')}}"><i class="ti ti-file me-2"></i>Đa phương tiện</a></li>
                                <li class="active">
                                    <a href="{{route('admin.users.index')}}" data-bs-toggle="modal" data-bs-target="#aboutModal"><i class="ti ti-info-circle me-2"></i>Giới thiệu</a>
                                </li>
                            </ul>
                            <!-- sidebar-menu  -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="simplebar-placeholder" style="width: auto; height: 870px;"></div>
        </div>
        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
            <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
        </div>
        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
            <div class="simplebar-scrollbar" style="height: 816px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
        </div>
    </div>
</nav>
@include('admin.layouts.modal')
