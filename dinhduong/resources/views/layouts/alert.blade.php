
@if (\Session::has('success'))
    <div class="alert-top alert alert-success alert-dismissible show" role="alert">
        {!! \Session::get('success') !!}
    </div>
@endif
@if (\Session::has('warning'))
    <div class="alert-top alert alert-warning alert-dismissible show" role="alert">
        {!! \Session::get('warning') !!}
    </div>
@endif
@if($errors->any())
    <div class="alert-top alert alert-danger alert-dismissible show" role="alert">
        {!! implode('', $errors->all(':message<br>')) !!}
    </div>
@endif
