@extends('admin.layouts.app-full') @section('title') Tổng quan @endsection @section('body_class', 'home') @section('content') <div class="container-fluid">
    <div class="layout-specing">
        <h5 class="mb-0">Thống kê</h5>
        {{-- Bộ lọc: lưới responsive — mobile mỗi trường một hàng, desktop dàn ngang --}}
        <form action="" method="GET" class="mt-2">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="small" for="loc-from-date">Từ ngày:</label>
                    <input name="from_date" id="loc-from-date" class="form-control" value="{{request()->get('from_date','')}}" type="date">
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="small" for="loc-to-date">Đến ngày:</label>
                    <input name="to_date" id="loc-to-date" class="form-control" value="{{request()->get('to_date','')}}" type="date">
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="small" for="province_code">Tỉnh/TP:</label>
                    <select name="province_code" id="province_code" data-wards-url="{{ route('admin.ajax_get_ward_by_province') }}" class="form-select">
                        <option value="">Tỉnh/thành phố</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->code }}" @if(request()->get('province_code') == $province->code) selected @endif>{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="small" for="ward_code">Phường xã:</label>
                    <select name="ward_code" id="ward_code" data-placeholder="Phường/xã" class="form-select">
                        <option value="">Phường/xã</option>
                        @foreach($wards as $ward)
                            <option value="{{ $ward->code }}" @if($ward->code == request()->get('ward_code')) selected @endif>{{ $ward->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-xl-2">
                    <label class="small" for="ethnic_id">Dân tộc:</label>
                    <select name="ethnic_id" id="ethnic_id" class="form-select">
                        <option value="all" @if(request()->get('ethnic_id') == 'all') selected @endif>Tất cả</option>
                        <option value="ethnic_minority" @if(request()->get('ethnic_id') == 'ethnic_minority') selected @endif>Tất cả dân tộc thiểu số</option>
                        @foreach($ethnics as $ethnic)
                            <option value="{{ $ethnic->id }}" @if($ethnic->id == request()->get('ethnic_id')) selected @endif>{{ $ethnic->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-xl-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary text-white flex-fill">Lọc</button>
                    <button type="reset" class="btn btn-warning text-white flex-fill" onclick="resetForm()">Làm mới</button>
                </div>
            </div>
        </form>
        @include('admin.dashboards.sections.count')
        <!--end row-->
        @include('admin.dashboards.sections.bieu-do-theo-nam')
        <!--end row-->
        <div class="row">
            <!--end col-->
            <div class="col-xl-12 mt-4">
                @include('admin.dashboards.sections.khao-sat-moi')
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
</div>
@endsection

@push('foot')
    <script src="{{ asset('web/js/dia-ban-2026.js') }}"></script>
    <script>
        function resetForm() {
            window.location.href = "{{ route('admin.dashboard.index') }}";
        }
    </script>
@endpush
