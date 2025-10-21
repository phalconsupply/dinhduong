@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <form action="" method="POST">
                @csrf

            @include('admin.type.tabs')
            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body p-0">
                            <table  id="transactions-table"  class="table bg-white mb-0" >
                                <thead>
                                <tr>
                                    @foreach($columns as $col)
                                        <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;">{{$col}}</th>
                                    @endforeach
                                    <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;">Giới tính</th>
                                    <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;">#</th>
                                </tr>
                                </thead>
                                <tbody id="dataBody">
                                @foreach($results as $index => $row)
                                    <tr class="existing-row" data-id="{{$row['id']}}" data-index="{{$index}}">
                                        @foreach($columns as $col)
                                        <td><input name="{{$col}}[]" data-id="{{$row['id']}}"  data-key="{{$col}}" type="text" value="{{ $row[$col] }}" class="form-control form-control-sm" required></td>
                                        @endforeach
                                        <td >
                                            <select name="gender[]" data-id="{{$row['id']}}" class="form-control form-control-sm">
                                                <option value="">Chọn giới tính</option>
                                                <option value="1" @if($row['gender'] == 1) selected @endif>Nam</option>
                                                <option value="0" @if($row['gender'] == 0) selected @endif>Nữ</option>
                                            </select>
                                        </td>
                                        <td><button data-id="{{$row['id']}}" type="button" class="btnDel btn-sm btn btn-danger p-0 m-0"><i class="ti ti-trash-x icons icon-sm"></i></button></td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!--end row-->
            @include('admin.type.script')
            </form>
        </div>
    </div>

@endsection
@push('head')
<style>
#dataBody tr{
    padding: 0;
    margin: 0;
}
#dataBody tr td{
    padding: 0;
    margin: 0;
    border-left: 1px solid #f0eeee;
}
#dataBody tr td input{
    border: 0px !important;
}
</style>
@endpush
