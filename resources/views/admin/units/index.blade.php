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
                    <div class="d-md-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><a href="{{route('admin.units.index')}}"><i class="ti ti-shield icons icon-sm"></i> Danh sách đơn vị</a></h5>
                        <nav aria-label="breadcrumb" class="d-inline-block">
                            <ul class="breadcrumb bg-transparent rounded mb-0 p-0">
                                <li class="breadcrumb-item text-capitalize">
                                    <a href="{{route('admin.units.create')}}" class="btn btn-sm btn-success text-white"  >
                                        <i class="ti ti-plus icons icon-sm"></i>Thêm đơn vị</a></li>
                            </ul>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-4">
                            <div class="col-6">
                                <form action="" method="GET">
                                    <div class="row mb-3">
                                        <div class="col-10">
                                            <div class="form-icon position-relative">
                                                <i data-feather="search" class="fea icon-sm icons"></i>
                                                <input name="keyword" id="keyword" type="text" class="form-control ps-5" value="{{app()->request->get('keyword')}}" placeholder="Từ khóa">

                                            </div>
                                        </div>
                                        <div class="col-2 row">
                                            <div class="">
                                                <button type="submit" class="btn btn-sm form-control btn-success text-white d-inline"><i data-feather="search" class="fea icon-sm icons"></i> Tìm</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-6 ta-r">
                            </div>
                            @include('admin.units.table', ['units'=> $units])
                        </div><!--end row-->
                    </div>
                </div>
            </div>
        </div>

        @endsection

@push('foot')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush

