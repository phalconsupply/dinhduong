@extends('admin.units.show')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('show-content')
    <div class="table-responsive bg-white shadow rounded">
        <table class="table table-hover mb-0 table-center">
        <tbody>
        <tr>
            <th scope="row">Tên đơn vị</th>
            <td>{{$unit->name}}</td>
        </tr>
        <tr>
            <th scope="row">Số điện thoại</th>
            <td>{{$unit->phone}}</td>
        </tr>
        <tr>
            <th scope="row">Thư điện tử</th>
            <td>{{$unit->email}}</td>
        </tr>
        <tr>
            <th scope="row">Địa chỉ</th>
            <td>{{$unit->address}}, <span class="small">{{$unit->ward->full_name ?? '#'}}, {{$unit->district->full_name ?? '#'}}, {{$unit->province->full_name ?? '#'}}</td>
        </tr>
        <tr>
            <th scope="row">Được tạo bởi</th>
            <td>{{$unit->creator->name ?? '#'}}</td>
        </tr>
        <tr>
            <th scope="row">Ghi chú</th>
            <td>{{$unit->note}}</td>
        </tr>
        <tr>
            <th scope="row">Ngày tạo</th>
            <td>{{$unit->created_at->format('d-m-Y')}}</td>
        </tr>
        <tr>
            <th scope="row">Ngày cập nhật gần đây</th>
            <td>{{$unit->updated_at->format('d-m-Y')}}</td>
        </tr>
        </tbody>
        </table>
    </div>
@endsection

@push('foot')

@endpush


