<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
    <ul class="" style="margin-bottom: 15px; display: block; height: 100px;">
{{--        <div class="col-2 col-md-2 ">--}}
{{--        </div>--}}
        <div class="">
            <img src="{{$setting['logo-light']}}" class="img-thumbnail" width="40" style="display: block; float: left; margin-right: 15px;" />
            <p class="mb-0" style="font-weight: bold">Phần mền đánh giá dinh dưỡng</p>
            <p class="small mb-0">Hotline: <a href="tel:{{$setting['phone']}}">{{$setting['phone']}}</a> </p>
{{--            <p class="small mb-0">Version: {{$setting['app_version']}}</p>--}}
        </div>
    </ul>
    <ul class="age-menu">
        <li class="heading">Đối tượng</li>
        <?php $slug = $slug ?? 'tu-0-5-tuoi'; ?>
        <li class="@if($slug == 'tu-0-5-tuoi') current @endif"><a href="/tu-0-5-tuoi">Từ 0-5 tuổi</a></li>
        <li class="@if($slug == 'tu-5-19-tuoi') current @endif"><a href="/tu-5-19-tuoi">Từ 5-19 tuổi</a></li>
        <li class="@if($slug == 'tu-19-tuoi') current @endif disabled"><a href="/tu-19-tuoi">Trên 19 tuổi</a></li>
    </ul>
    <ul class="age-menu" style="margin-top:10px">

        @if(auth()->check())
            <img src="{{auth()->user()->thumb}}" width="100px" class="img-thumbnail img-responsive">
            <li>
                <a href=#">{{auth()->user()->name}}</a>
            </li>
            <li><a href="{{url('/admin')}}">Quản trị</a></li>
            <li><a href="{{url('/auth/logout')}}">Đăng xuất</a></li>
        @else

            <li class="heading">Tài khoản</li>
            <form action="{{route('auth.login')}}" method="POST">
                @csrf
            <li class="p-5"><input type="text" name="username" value="{{old('username')}}" class="form-control" id="username" placeholder="Tên đăng nhập" required></li>
            <li class="p-5"><input type="password" name="password"  class="form-control" id="password" placeholder="Mật khẩu" required></li>
            <li class="p-5"><input type="submit" name="submit" value="Đăng nhập"  class="form-control btn-success"></li>
            </form>
        @endif
    </ul>
    <div class="kid-img">
        <figure>
            <img class="img-responsive" src="/web/frontend/images/kid-1.jpg">
        </figure>
    </div>
</div>
