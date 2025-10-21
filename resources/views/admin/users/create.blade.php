@extends('admin.layouts.app-full')
@section('title')
    <?php echo getSetting('site_name') ?>
@endsection
@section('body_class', 'home')
@section('content')
    <div class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-12 d-lg-block d-none">
                    <div class="card border-bottom pb-4">

                        <div class="card-body">
                            <div class="card-title"><h5>Thêm nhân sự</h5></div>
                            <form action="{{route('admin.users.store')}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="name" id="name" type="text" class="form-control ps-5" placeholder="Họ và tên" value="{{old('name')}}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="user" class="fea icon-sm icons"></i>
                                                    <input name="username" id="username" type="text" class="form-control ps-5"  value="{{old('username')}}" placeholder="Tên đăng nhập" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Giới tính<span class="text-danger">*</span></label>
                                                <select name="gender" class="form-select form-control" required aria-label="Default select example">
                                                    <option value="1" @if(old('gender') == '1') selected @endif>Nam</option>
                                                    <option value="0" @if(old('gender') == '0') selected @endif>Nữ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Số căn cước<span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="user" class="fea icon-sm icons"></i>
                                                        <input name="id_number" id="id_number" type="text" class="form-control ps-5" placeholder="Số căn cước" value="{{old('id_number')}}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Ngày sinh</label>
                                                    <div class="form-icon position-relative">
                                                        <i class="ti ti-calendar icons"></i>
                                                        <input name="birthday" id="birthday" type="date" class="form-control ps-5" placeholder="Ngày sinh" value="{{old('birthday')}}" >
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Số điện thoại<span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="user" class="fea icon-sm icons"></i>
                                                        <input name="phone" id="phone" type="text" class="form-control ps-5" placeholder="Số điện thoại" value="{{old('phone')}}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Thư điện tử</label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="mail" class="fea icon-sm icons"></i>
                                                        <input name="email" id="email" type="email" class="form-control ps-5" placeholder="Thư điện tử" value="{{old('email')}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="lock" class="fea icon-sm icons"></i>
                                                        <input name="password" id="password" type="password" class="form-control ps-5" placeholder="Mật khẩu" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                                    <div class="form-icon position-relative">
                                                        <i data-feather="lock" class="fea icon-sm icons"></i>
                                                        <input name="password_confirmation" id="password_confirmation" type="password" class="form-control ps-5" placeholder="Xác nhận mật khẩu" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Địa chỉ<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <input name="address" id="address" type="text" class="form-control ps-5" value="{{old('address')}}" placeholder="Địa chỉ" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tỉnh/TP<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"></i>
                                                    <select name="province_code" id="province_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option>Tỉnh/thành phố</option>
                                                        @foreach($provinces as $province)
                                                            <option value="{{ $province->code }}" @if(old('province_code') == $province->code) selected @endif>{{ $province->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Quận/Huyện<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option value="">Quận/huyện</option>
                                                        @foreach(old('districts') ?? [] as $district)
                                                            <option value="{{ $district->code }}" @if($district->code == old('district_code')) selected @endif>{{ $district->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Phường/Xã<span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="map-pin" class="fea icon-sm icons"> </i>
                                                    <select name="ward_code" id="ward_code" class="form-select form-control text-end" aria-label="Default select example" required>
                                                        <option value="">Phường/xã</option>
                                                        @foreach(old('wards') ?? [] as $ward)
                                                            <option value="{{ $ward->code }}" @if($ward->code == old('ward_code')) selected @endif>{{ $ward->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-check form-switch">
                                                <input name="is_active" class="form-check-input" type="checkbox" id="is_active" @if(old('is_active') == 'on') checked @endif>
                                                <label class="form-check-label" for="is_active">Trạng thái</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Ảnh hồ sơ</label>
                                                <div class="form-icon position-relative">
                                                    <i class="ti ti-upload fea icon-sm icons"></i>
                                                    <input name="thumb" id="thumb" value="{{ old('thumb') }}" type="text" onclick="selectFileWithCKFinder('thumb')" class="form-control ps-5" placeholder="Nhập link ảnh">
                                                </div>
                                            </div>
                                        </div><!--end col-->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Thuộc đơn vị <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="shield" class="fea icon-sm icons"> </i>
                                                    <select name="unit_id" id="unit_id" class="form-select form-control pl-45px" aria-label="Default select example" required>
                                                        <option value="">Chọn đơn vị</option>
                                                        @foreach($units ?? [] as $unit)
                                                            <option value="{{ $unit->id }}" @if($unit->id == old('unit_id')) selected @endif>{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Thuộc bộ phân</label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="users" class="fea icon-sm icons"> </i>
                                                    <input name="department" id="department" type="text" class="form-control ps-5" value="{{old('department')}}" placeholder="Bộ phận">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Chức danh</label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="users" class="fea icon-sm icons"> </i>
                                                    <input name="role_title" id="role_title" type="text" class="form-control ps-5" value="{{old('role_title')}}" placeholder="Chức danh">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Chức vụ <span class="text-danger">*</span></label>
                                                <div class="form-icon position-relative">
                                                    <i data-feather="star" class="fea icon-sm icons"> </i>
                                                    <select name="role" id="role" class="form-select form-control pl-45px" aria-label="Default select example" required>
                                                        <option value="">Chọn chức vụ</option>
                                                        <option value="manager" @if('manager' == old('role')) selected @endif>Quản lý</option>
                                                        <option value="employee" @if('employee' == old('role')) selected @endif>Nhân viên</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div><!--end col-->
                                        <div class="mb-3">
                                            <label class="form-label">Ghi chú</label>
                                            <textarea name="note" class="form-control" placeholder="Ghi chú">{{old('note')}}</textarea>
                                        </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mt-3">
                                    <div class="col-8 ta-r">
                                        <input type="submit" id="submit" name="send" class="btn btn-sm btn-primary" value="Thêm tài khoản">
                                    </div><!--end col-->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('foot')
    <script>
        $(document).ready(function() {
            // Khi có thay đổi trong select province
            $('#province_code').change(function() {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '{{route('web.ajax_get_district_by_province')}}', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { province_code: province_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var districtSelect = $('#district_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="0">Chọn quận huyện</option>');
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
                    url: '{{route('web.ajax_get_ward_by_district')}}', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: { district_code: district_code }, // Truyền province id qua request
                    success: function(response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        wardSelect.find('option').remove();

                        // Thêm các option mới cho district từ danh sách nhận được
                        wardSelect.append('<option value="0">Chọn phường xã</option>');
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

