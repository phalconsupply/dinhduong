<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="mb-0">Khảo sát mới</h5>
    <form action="{{ route('admin.history.export') }}" method="POST" class="mb-0">
        @csrf
        <button type="submit" class="btn btn-sm btn-primary text-white">
            <i class="ti ti-download icons icon-sm"></i> Xuất khảo sát
        </button>
    </form>
</div>

<div class="table-responsive shadow rounded mt-2">
    <table id="history-table" class="table table-center bg-white mb-0">
        <thead>
        <tr>
            <th class="border-bottom p-2">ID</th>
            <th class="border-bottom p-2">Ảnh</th>
            <th class="border-bottom p-2">Tên<br />Điện thoại<br />CCCD</th>
            <th class="border-bottom p-2" >Chỉ số</th>
            <th class="border-bottom p-2" >Ngày cân<br />Ngày sinh</th>
            <th class="border-bottom p-2" >Kết quả</th>
            <th class="border-bottom p-2" >Nguy cơ</th>
            <th class="border-bottom p-2" >Giới tính<br />Tuổi<br />Dân tộc</th>
            <th class="border-bottom p-2" >Địa chỉ</th>
            <th class="border-bottom p-2" >Người lập<br />Đơn vị<br />Ngày lập</th>
            <th class="border-bottom p-2">Menu</th>
        </tr>
        </thead>
        <tbody>
        @foreach($new_survey as $row)
            <tr>
                <td class="">{{$row->id}}</td>
                <td class="">
                    @if($row->thumb)
                        <a href="{{route('admin.users.show', $row)}}"><img src="{{$row->thumb}}" class="img-thumbnail" width="80px" /></a>
                    @else
                        <a href="{{route('admin.users.show', $row)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
                    @endif
                </td>
                <td>
                    <span class="small">{{$row->fullname}}</span><br>
                    <span class="small">{!! $row->phone ?? '&nbsp;' !!}</span><br>
                    <span class="small">{!! $row->id_number ?? '&nbsp;' !!}</span>
                </td>
                <td>
                    <span class="small">Chiều cao: {{$row->height}} cm</span><br>
                    <span class="small">Cân nặng: {{$row->weight}} kg</span><br>
                    <span class="small">BMI: {{$row->bmi}}</span>
                </td>
                <td>
                    <span class="small">{{$row->birthday_f() ?? '#'}}</span><br>
                    <span class="small">{{$row->cal_date_f() ?? '#'}}</span>
                </td>
                <td>
                    <span class="small" style="background-color: {{$row->check_weight_for_age()['color']}}">Cân nặng theo tuổi:{{$row->check_weight_for_age()['text']}}</span><br>
                    <span class="small" style="background-color: {{$row->check_height_for_age()['color']}}">Chiều cao theo tuổi:{{$row->check_height_for_age()['text']}}</span><br>
                    <span class="small" style="background-color: {{$row->check_weight_for_height()['color']}}">Cân nặng theo chiều cao:{{$row->check_weight_for_height()['text']}}</span>
                </td>
                <td>
                    @if($row->is_risk === 1)
                        <span class="badge bg-warning">Nguy cơ</span><br>
                    @else
                        <span class="badge bg-success">Bình thường</span><br>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{v('gender.color.'.$row->gender)}}">{{v('gender.'.$row->gender)}}</span><br>
                    <span class="">{{$row->get_age()}}</span><br>
                    <span class="">{{$row->ethnic->name ?? '#'}}</span>
                </td>
                <td>
                    <span class="small">{{$row->address ?? '#'}}</span><br>
                    <span class="small">{{$row->ward->full_name ?? '#'}}, {{$row->district->full_name ?? '#'}}</span><br>
                    <span  class="small">{{$row->province->full_name ?? '#'}}</span>
                </td>
                <td>
                    @php $creator = $row->creator; @endphp
                    <span class="small">{{$creator->name ?? 'Khách vãng lai'}}</span><br>
                    <span class="small">{!!  $creator->unit->name ?? '&nbsp;'  !!}</span><br>
                    <span class="small">{{ $row->created_at->format("d-m-Y") }}</span>
                </td>
                <td>
                    <div class="btn-group dropdown-primary me-2 mt-2">
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu">

                            <a href="{{route('result',['uid'=>$row->uid])}}" class="dropdown-item" target="_blank"><i class="ti ti-eye"></i> Xem kết quả</a>
                            <a href="{{route('print',['uid'=>$row->uid])}}" class="dropdown-item" target="_blank"><i class="ti ti-printer"></i> In</a>
                            <div class="dropdown-divider"></div>
                            <form class="dropdown-item" action="{{route('admin.history.destroy', ['history'=>$row])}}" method="POST" onsubmit="return confirm('Xác nhận xóa khoả sát này?');">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa lịch sử</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-2 d-flex">
        <div class="mx-auto">

        </div>
    </div>
</div>
