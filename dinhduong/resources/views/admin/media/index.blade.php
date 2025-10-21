@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <!-- Main content -->
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 d-lg-block d-none">
                    <iframe src="/admin-assets/libs/ckfinder/ckfinder.html" width="100%" style="min-height:500px"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
