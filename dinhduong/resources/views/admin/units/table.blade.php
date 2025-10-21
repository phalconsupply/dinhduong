<div class="table-responsive shadow rounded">
    <table id="history-table" class="table table-center bg-white mb-0">
        <thead>
        <tr>
            <th class="border-bottom ">ID</th>
            <th class="border-bottom ">Ảnh</th>
            <th class="border-bottom ">Tên đơn vị</th>
            <th class="border-bottom ">Số điện thoại/Email</th>
            <th class="border-bottom " >Địa chỉ</th>
            <th class="border-bottom " >Cấp bậc</th>
            <th class="border-bottom " >Trạng thái</th>
            <th class="border-bottom " >Ngày tạo/Người tạo</th>
            <th class="border-bottom ">Menu</th>
        </tr>
        </thead>
        <tbody>
        @foreach($units as $unit)
            <tr>
                <td class="">{{$unit->id}}</td>
                <td class="">
                    @if($unit->thumb)
                        <a href="{{route('admin.units.show_history', $unit)}}"><img src="{{$unit->thumb}}" class="img-thumbnail" width="80px" /></a>
                    @else
                        <a href="{{route('admin.units.show_history', $unit)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
                    @endif
                </td>
                <td class="">
                    <a href="{{route('admin.units.show', $unit)}}">{{$unit->name}}</a><br/>
                    {{--                                                <span class="small">{{$unit->unit_type->name}}</span>--}}
                </td>
                <td>
                    <span class="small">{{$unit->phone}}</span><br>
                    <span class="small">{{$unit->email}}</span>
                </td>

                <td><span class="small">{{$unit->address ?? '#'}}</span><br>
                    <span class="small">{{$unit->ward->full_name ?? '#'}}, {{$unit->district->full_name ?? '#'}}</span><br>
                    <span class="small">{{$unit->province->full_name ?? '#'}}</span>
                </td>
                <td>
                    <span class="badge bg-success">{{$unit->unit_type->name}}</span>
                </td>
                <td>
                    @if($unit->is_active == 1)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-danger">Chưa kích hoạt</span>
                    @endif
                </td>
                <td>
                    <span class="small">{{$unit->created_at->format('d-m-Y')}}</span><br>
                    <span class="small">{{$unit->creator->username}}</span>
                </td>
                <td>
                    <div class="btn-group dropdown-primary me-2 mt-2">
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Menu
                        </button>
                        <div class="dropdown-menu">

                            <a href="{{route('admin.units.show_history',['unit'=>$unit])}}" class="dropdown-item"><i class="ti ti-eye"></i> Xem lịch sử</a>
                            <a href="{{route('admin.units.show',$unit)}}" class="dropdown-item"><i class="ti ti-eye"></i> Xem chi tiết</a>
                            @if(is_roles(['admin', 'manager']))
                                <a href="{{route('admin.units.edit',['unit'=>$unit])}}" class="dropdown-item"><i class="ti ti-edit"></i> Chỉnh sửa</a>
                                <div class="dropdown-divider"></div>
                                @if(is_admin() || $unit->created_by == Auth::id())
                                <form class="dropdown-item" action="{{route('admin.units.destroy', ['unit'=>$unit])}}" method="POST" onsubmit="return confirm('Xác nhận xóa đơn vị này?');">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                                    <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa đơn vị</button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if ($units instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-2 d-flex">
            <div class="mx-auto">
                {{ $units->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4") }}
            </div>
        </div>
    @endif

</div>
