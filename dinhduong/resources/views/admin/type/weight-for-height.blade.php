@extends('admin.type.index')
@section('show_content')
    <div class="row">
        <div class="col-12 ">
            <div class="card">
                <div class="card-body">
                    <table  id="transactions-table"  class="table bg-white mb-0">
                        <thead>
                        <tr>
{{--                            <th class="border-bottom " style="">ID</th>--}}
                            <th class="border-bottom " style="">cm</th>
                            <th class="border-bottom ">- 3SD</th>
                            <th class="border-bottom ">- 2SD</th>
                            <th class="border-bottom ">- 1SD</th>
                            <th class="border-bottom ">Median</th>
                            <th class="border-bottom ">1SD</th>
                            <th class="border-bottom ">2SD</th>
                            <th class="border-bottom">3SD</th>
                            <th class="border-bottom " style="">Giới tính</th>
                            <th class="border-bottom " style="">#</th>

                        </tr>
                        </thead>
                        <tbody id="dataBody">
                        @foreach($results as $row)
                            <tr class="existing-row"  data-id="{{$row['id']}}">
                                <td><input data-id="{{$row['id']}}" data-key="cm" type="text" value="{{ $row['cm'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="-1SD" type="text" value="{{ $row['-1SD'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="-2SD" type="text" value="{{ $row['-2SD'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="-1SD" type="text" value="{{ $row['-1SD'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="Median" type="text" value="{{ $row['Median'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="1SD" type="text" value="{{ $row['1SD'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="2SD" type="text" value="{{ $row['2SD'] }}" class="form-control form-control-sm"></td>
                                <td><input data-id="{{$row['id']}}" data-key="3SD" type="text" value="{{ $row['3SD'] }}" class="form-control form-control-sm"></td>
                                <td >
                                    <select data-id="{{$row['id']}}" class="form-control form-control-sm">
                                        <option>Chọn giới tính</option>
                                        <option @if($row['gender'] == 1) selected @endif>Nam</option>
                                        <option @if($row['gender'] == 0) selected @endif>Nữ</option>
                                    </select>
                                </td>
                                <td><button data-id="{{$row['id']}}" type="button" class="btnSave btn-sm btn btn-success p-0 m-0"><i class="ti ti-device-floppy icons icon-sm"></i></button></td>
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
@endsection



