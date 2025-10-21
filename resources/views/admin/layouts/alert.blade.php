
@if (\Session::has('success'))
    <div class="alert-top alert alert-success alert-dismissible fade show" role="alert">
        {!! \Session::get('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
@endif
@if (\Session::has('warning'))
    <div class="alert-top alert alert-warning alert-dismissible fade show" role="alert">
        {!! \Session::get('warning') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
@endif
@if($errors->any())
    <div class="alert-top alert alert-danger alert-dismissible fade show" role="alert">
    {!! implode('', $errors->all(':message<br>')) !!}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> </button>
    </div>
@endif
