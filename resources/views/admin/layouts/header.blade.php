@php
    // Mục menu đang mở: so theo tên route để giữ trạng thái cả ở trang con (sửa, chi tiết…)
    $dangMo = fn (...$mau) => request()->routeIs(...$mau);
    $mucMenu = fn (...$mau) => $dangMo(...$mau) ? ' active' : '';
    $hienTai = fn (...$mau) => $dangMo(...$mau) ? 'aria-current=page' : '';
@endphp
{{-- Menu quản trị nằm ngang trên header (thay sidebar trái) để nội dung dùng hết chiều rộng màn hình --}}
<div class="top-header">
    <nav class="navbar navbar-expand-xl header-bar admin-topnav" aria-label="Menu quản trị">
        <a href="{{ route('admin.dashboard.index') }}" class="navbar-brand d-flex align-items-center me-3" aria-label="Về trang tổng quan">
            <img src="{{ $setting['logo-light'] }}" alt="">
            <span class="ten-he-thong d-none d-xxl-inline">{{ $setting['name'] }}</span>
        </a>

        {{-- Nhóm bên phải luôn hiện, kể cả khi menu thu gọn trên màn hình hẹp --}}
        <div class="d-flex align-items-center ms-auto order-xl-3">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-icon btn-soft-light" title="Xem trang người dùng" aria-label="Xem trang người dùng (mở tab mới)">
                <i class="ti ti-eye"></i>
            </a>
            <div class="dropdown dropdown-primary ms-1">
                <button type="button" class="btn btn-soft-light dropdown-toggle p-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Tài khoản của tôi">
                    <img src="{{ url('assets/images/client/05.jpg') }}" class="avatar avatar-ex-small rounded" alt="">
                </button>
                <div class="dropdown-menu dd-menu dropdown-menu-end shadow border-0 mt-3 py-3" style="min-width: 220px;">
                    <a class="dropdown-item d-flex align-items-center text-dark pb-3" href="{{ route('admin.profile.index') }}">
                        <img src="{{ asset('admin-assets/images/client/05.jpg') }}" class="avatar avatar-md-sm rounded-circle border shadow" alt="">
                        <div class="flex-1 ms-2">
                            <span class="d-block">{{ Auth::user()->name }}</span>
                            @if(!is_admin())<span class="d-block small">Đơn vị: {{ Auth::user()->unit?->name }}</span>@endif
                            <span class="d-block small">Chức vụ: {{ v('role.' . Auth::user()->role) }}</span>
                        </div>
                    </a>
                    <a class="dropdown-item text-dark" href="{{ route('admin.profile.index') }}"><i class="ti ti-user-circle me-1"></i> Tài khoản của tôi</a>
                    <a class="dropdown-item text-dark" href="{{ route('admin.profile.changepassword') }}"><i class="ti ti-lock me-1"></i> Đổi mật khẩu</a>
                    <a class="dropdown-item text-dark" href="#" data-bs-toggle="modal" data-bs-target="#aboutModal"><i class="ti ti-info-circle me-1"></i> Giới thiệu</a>
                    <div class="dropdown-divider border-top"></div>
                    <a class="dropdown-item text-dark" href="{{ route('admin.auth.logout') }}"><i class="ti ti-logout me-1"></i> Đăng xuất</a>
                </div>
            </div>
            {{-- d-xl-none: .btn-icon của theme đặt display và đè quy tắc ẩn toggler của Bootstrap --}}
            <button class="navbar-toggler btn btn-icon btn-soft-light ms-1 d-xl-none" type="button" data-bs-toggle="collapse" data-bs-target="#menu-quan-tri"
                    aria-controls="menu-quan-tri" aria-expanded="false" aria-label="Mở menu">
                <i class="ti ti-menu-2"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse order-last order-xl-1" id="menu-quan-tri">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link{{ $mucMenu('admin.dashboard.index') }}" {{ $hienTai('admin.dashboard.index') }} href="{{ route('admin.dashboard.index') }}"><i class="ti ti-home me-1"></i>Thống kê</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ $mucMenu('admin.dashboard.statistics') }}" {{ $hienTai('admin.dashboard.statistics') }} href="{{ route('admin.dashboard.statistics') }}"><i class="ti ti-chart-bar me-1"></i>Thống kê chi tiết</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ $mucMenu('admin.history.*') }}" {{ $hienTai('admin.history.*') }} href="{{ route('admin.history.index') }}"><i class="ti ti-history me-1"></i>Khảo sát</a>
                </li>

                @if(is_admin() || is_super_admin_province())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ $mucMenu('admin.units.*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-shield me-1"></i>Đơn vị</a>
                        <ul class="dropdown-menu shadow border-0">
                            @if(!is_admin())
                                <li><a class="dropdown-item" href="{{ route('admin.units.show', ['unit' => Auth::user()->unit_id]) }}">Đơn vị của tôi</a></li>
                            @endif
                            <li><a class="dropdown-item{{ $mucMenu('admin.units.index') }}" href="{{ route('admin.units.index') }}">Danh sách đơn vị</a></li>
                            <li><a class="dropdown-item{{ $mucMenu('admin.units.create') }}" href="{{ route('admin.units.create') }}">Thêm đơn vị</a></li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link{{ $mucMenu('admin.units.*') }}" {{ $hienTai('admin.units.*') }} href="{{ route('admin.units.show', ['unit' => Auth::user()->unit_id]) }}"><i class="ti ti-shield me-1"></i>Đơn vị của tôi</a>
                    </li>
                @endif

                @if(is_roles(['admin', 'manager']))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ $mucMenu('admin.users.*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-users me-1"></i>Nhân sự</a>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item{{ $mucMenu('admin.users.index') }}" href="{{ route('admin.users.index') }}">Tất cả nhân sự</a></li>
                            <li><a class="dropdown-item{{ $mucMenu('admin.users.create') }}" href="{{ route('admin.users.create') }}">Thêm tài khoản</a></li>
                        </ul>
                    </li>
                @endif

                @if(is_admin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle{{ $mucMenu('admin.setting.*', 'admin.type.*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-settings me-1"></i>Cấu hình</a>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item{{ $mucMenu('admin.setting.index') }}" href="{{ route('admin.setting.index') }}">Tổng quan</a></li>
                            <li><a class="dropdown-item{{ $mucMenu('admin.type.*') }}" href="{{ route('admin.type.index', ['tab' => 'weight-for-age']) }}">Đối tượng</a></li>
                            <li><a class="dropdown-item{{ $mucMenu('admin.setting.advices') }}" href="{{ route('admin.setting.advices') }}">Lời khuyên</a></li>
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link{{ $mucMenu('admin.media.*') }}" {{ $hienTai('admin.media.*') }} href="{{ route('admin.media.index') }}"><i class="ti ti-file me-1"></i>Đa phương tiện</a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<!-- Top Header -->
