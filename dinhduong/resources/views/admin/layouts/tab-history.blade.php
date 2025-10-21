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
        <div class="table-responsive shadow rounded">
            <table id="history-table" class="table table-center bg-white mb-0">
                <thead>
                <tr>
                    <th class="border-bottom p-3">ID</th>
                    <th class="border-bottom p-3">Ảnh</th>
                    <th class="border-bottom p-3">Tên</th>
                    <th class="border-bottom p-3" >Điện thoại/Email</th>
                    <th class="border-bottom p-3" >Chỉ số</th>
                    <th class="border-bottom p-3" >Ngày cân/Ngày sinh</th>
                    <th class="border-bottom p-3" >Giới tính</th>
                    <th class="border-bottom p-3" >Tuổi/Số BMI</th>
                    <th class="border-bottom p-3" >Địa chỉ</th>
                    <th class="border-bottom p-3">Menu</th>
                </tr>
                </thead>
                <tbody>
                @foreach($history as $row)
                    <tr>
                        <td class="text-center">{{$row->id}}</td>
                        <td class="text-center">
                            @if($row->thumb)
                                <a href="{{route('admin.users.show', $row)}}"><img src="{{$row->thumb}}" class="img-thumbnail" width="80px" /></a>
                            @else
                                <a href="{{route('admin.users.show', $row)}}"><img src="{{v('user.avatar')}}" class="img-thumbnail" width="80px" /></a>
                            @endif
                        </td>
                        <td>
                            <span class="small">{{$row->fullname}}</span><br>
                            <span class="small">{{$row->username}}</span><br>
                            <span class="small">{{$row->id_number}}</span>
                        </td>
                        <td>
                            <span class="small">{{$row->phone}}</span><br>
                            <span class="small">{{$row->email}}</span>
                        </td>
                        <td>
                            <span class="small">Chiều cao:{{$row->height}} cm</span><br>
                            <span class="small">Cân nặng:{{$row->weight}} kg</span>
                        </td>
                        <td>
                            <span class="small">{{$row->birthday_f() ?? '#'}}</span><br>
                            <span class="small">{{$row->cal_date_f() ?? '#'}}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{v('gender.color.'.$row->gender)}}">{{v('gender.'.$row->gender)}}</span><br>
                        </td>
                        <td>
                            <span class="">{{$row->realAge}}</span><br>
                            <span class="">{{$row->bmi ?? '#'}}</span>
                        </td>
                        <td><span class="small">{{$row->address ?? '#'}}</span><br>
                            <span class="small">{{$row->ward->full_name ?? '#'}}, {{$row->district->full_name ?? '#'}}, {{$row->province->full_name ?? '#'}}</span>
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
                                    @if($row->username != 'admin')
                                        <form class="dropdown-item" action="{{route('admin.users.destroy', ['user'=>$row])}}" method="POST" onsubmit="return confirm('Xác nhận xóa tài khoản này?');">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                                            <button class="none-btn" type="submit"><i class="ti ti-trash"></i> Xóa lịch sử</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-2 d-flex">
                <div class="mx-auto">
                    {{ $history->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4") }}
                </div>
            </div>
        </div>
    </div><!--end col-->
</div><!--end row-->
