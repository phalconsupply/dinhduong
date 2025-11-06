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
                    <h5 class="mb-0"><a href="{{route('admin.history.index')}}"><i class="ti ti-history icons icon-sm"></i> Lịch sử tra cứu</a></h5>
                    <div class="row">
                        <div class="col-12 mt-4 mb-2">
                            <div class="d-flex">
                                <form action="" method="GET">
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <div class="form-group position-relative">
                                            <label>Từ khóa</label>
                                            <input
                                                name="keyword"
                                                id="keyword"
                                                type="text"
                                                class="form-control ps-3"
                                                value="{{ app()->request->get('keyword') }}"
                                                placeholder="Nhập tên, sđt, cc..."
                                            >
                                        </div>


                                        <div class="form-group">
                                            <label class="small">Từ ngày:</label>
                                            <input name="from_date" class="form-control" value="{{ request()->get('from_date','') }}" type="date">
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Đến ngày:</label>
                                            <input name="to_date" class="form-control" value="{{ request()->get('to_date','') }}" type="date">
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Tỉnh/TP:</label>
                                            <select name="province_code" id="province_code" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="">Tỉnh/thành phố</option>
                                                @foreach($provinces as $province)
                                                    <option value="{{ $province->code }}" @if(request()->get('province_code') == $province->code) selected @endif>{{ $province->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Quận huyện:</label>
                                            <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="">Quận/huyện</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->code }}" @if($district->code == request()->get('district_code')) selected @endif>{{ $district->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="small">Phường xã:</label>
                                            <select name="ward_code" id="ward_code" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="">Phường/xã</option>
                                                @foreach($wards as $ward)
                                                    <option value="{{ $ward->code }}" @if($ward->code == request()->get('ward_code')) selected @endif>{{ $ward->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label  class="small">Dân tộc:</label>
                                            <select name="ethnic_id" id="ethnic_id" class="form-select form-control text-end" aria-label="Default select example">
                                                <option value="all" @if(request()->get('ethnic_id') == 'all') selected @endif>Tất cả</option>
                                                <option value="ethnic_minority" @if(request()->get('ethnic_id') == 'ethnic_minority') selected @endif>Tất cả dân tộc thiểu số</option>
                                                @foreach($ethnics as $ethnic)
                                                    <option value="{{ $ethnic->id }}" @if($ethnic->id == request()->get('ethnic_id')) selected @endif>{{ $ethnic->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-primary text-white">Lọc</button>
                                                <button type="reset" class="btn btn-sm btn-warning text-white" onclick="resetForm()">Làm mới</button>
                                            </div>
                                        </div>
                                    </div> <!-- ✅ Thêm dòng này để đóng div.d-flex -->
                                </form>
                                <div class="d-flex align-items-end justify-content-end mb-1 ms-2">
                                    <div class="form-group">
                                        <form action="{{ route('admin.history.export') }}" method="POST" class="mb-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary text-white">
                                                <i class="ti ti-download icons icon-sm"></i> Xuất khảo sát
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive shadow rounded mt-4">
                                <table id="history-table" class="table table-center bg-white mb-0">
                                    <thead>
                                    <tr>
                                        <th class="border-bottom p-2">ID</th>
                                        <th class="border-bottom p-2">Ảnh</th>
                                        <th class="border-bottom p-2">Tên<br />Điện thoại<br />CCCD</th>
                                        <th class="border-bottom p-2" >Chỉ số</th>
                                        <th class="border-bottom p-2" >Ngày cân<br />Ngày sinh</th>
                                        <th class="border-bottom p-2" >Kết quả</th>
                                        <th class="border-bottom p-2" >Trạng thái</th>
                                        <th class="border-bottom p-2" >Giới tính<br />Tuổi<br />Dân tộc</th>
                                        <th class="border-bottom p-2" >Địa chỉ</th>
                                        <th class="border-bottom p-2" >Người lập<br />Đơn vị<br />Ngày lập</th>
                                        <th class="border-bottom p-2">Menu</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($history as $row)
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
                                                @php
                                                    // Lấy trạng thái dinh dưỡng với màu WHO chuẩn
                                                    $nutritionStatusData = $row->get_nutrition_status_auto();
                                                    $nutritionStatus = $nutritionStatusData['text'] ?? ($row->nutrition_status ?? 'Chưa xác định');
                                                    $statusColor = $nutritionStatusData['color'] ?? '#9E9E9E';
                                                    
                                                    // Xác định class badge dựa trên màu WHO
                                                    $badgeClass = 'bg-secondary'; // default
                                                    if ($statusColor === '#F44336') { // WHO Red - Nguy hiểm
                                                        $badgeClass = 'bg-danger';
                                                    } elseif ($statusColor === '#FF9800') { // WHO Orange - Cảnh báo
                                                        $badgeClass = 'bg-warning';
                                                    } elseif ($statusColor === '#4CAF50') { // WHO Green - Bình thường
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($statusColor === '#00BCD4') { // WHO Cyan - Cao hơn bình thường
                                                        $badgeClass = 'bg-info';
                                                    }
                                                @endphp

                                                <span class="badge {{ $badgeClass }}">{{ $nutritionStatus }}</span>
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
                                        {{ $history->appends(['keyword' => request()->get('keyword')])->links("pagination::bootstrap-4") }}
                                    </div>
                                </div>
                            </div>
                        </div><!--end col-->
                    </div><!--end row-->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('foot')
    <script>
        function resetForm() {
            window.location.href = "{{ route('admin.history.index') }}";
        }
        $(document).ready(function() {

            // Khi có thay đổi trong select province
            $('#province_code').change(function() {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '{{route('admin.ajax_get_district_by_province')}}', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { province_code: province_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var districtSelect = $('#district_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="">Chọn quận huyện</option>');
                        $.each(response.districts, function(key, value) {
                            districtSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });

                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi xảy ra trong yêu cầu Ajax
                        console.log(error);
                    }
                });
            });

            $(document).on('change','#district_code',function() {
                var district_code = $(this).val(); // Lấy giá trị province id được chọn
                console.log(district_code)
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '{{route('admin.ajax_get_ward_by_district')}}', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { district_code: district_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        wardSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        wardSelect.append('<option value="">Chọn phường xã</option>');
                        $.each(response.wards, function(key, value) {
                            wardSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi xảy ra trong yêu cầu Ajax
                        console.log(error);
                    }
                });
            });
        });
    </script>
@endpush
