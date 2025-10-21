@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class=" col-lg-12 col-md-12 col-12">
                    <div class="row">
                        <div class="col-md-12">
                            @include('admin.users.table-detail')
                        </div>
                        <div class="col-md-12 mt-4">
                            @include('admin.users.tabs')
                        </div>
                        @yield('show-content')
                    </div>
                </div>
            </div>
        </div>


@endsection

@push('foot')

@endpush


