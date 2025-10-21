<div class="card sticky-bar p-4 rounded shadow">

    <div class="widget mt-4">
        <ul class="list-unstyled mb-0" id="navmenu-nav">
            <li class="navbar-item account-menu px-0 mt-2 @if($page=='index') active @endif">
                <a href="{{route('admin.setting.index')}}" class="navbar-link d-flex rounded shadow align-items-center py-2 px-4 ">
                    <span class="h4 mb-0"><i class="uil uil-dashboard"></i></span>
                    <h6 class="mb-0 ms-2">Tổng quan</h6>
                </a>
            </li>
            <li class="navbar-item account-menu px-0 mt-2 @if($page=='advices') active @endif">
                <a href="{{route('admin.setting.advices')}}" class="navbar-link d-flex rounded shadow align-items-center py-2 px-4 ">
                    <span class="h4 mb-0"><i class="uil uil-dashboard"></i></span>
                    <h6 class="mb-0 ms-2">Lời khuyên</h6>
                </a>
            </li>


        </ul>
    </div>

</div>
