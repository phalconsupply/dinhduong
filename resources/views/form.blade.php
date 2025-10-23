@extends('layouts.app')
@section('content')
    <section id="nuti-medical">
        <div class="container">
            <div class="row">
                @include('layouts.siderbar')
                <div class="col-xs-12 col-sm-6 col-md-7 col-lg-8">
                    @include('sections.form-heading')
                    <div class="">
                        <div id="tab-2" class="profile-detail-menu-content" style="">
                            @include('layouts.alert')
                            <form class="pro5-form" action="{{ route('form.post', ['slug' => $slug]) }}" method="POST" enctype="multipart/form-data">
                                @include('sections.form-avatar')
                                <div class="pro5-info">
                                    <div class="pro5-input">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <p>Họ và tên (<span class="text-danger">*</span>)</p>
                                                    <input type="text" name="fullname" value="{{old('fullname', $item->fullname)}}" class="form-control" id="last-name" placeholder="Họ và tên" required>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <p>Mã định danh (CCCD)</p>
                                                <div class="form-group calendar-group">
                                                    <input type="text" name="id_number" value="{{old('id_number', $item->id_number)}}" class="form-control" id="id_number" placeholder="Mã định danh">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <p>Số điện thoại</p>
                                                    <input type="number" minlength="10" maxlength="11" name="phone" value="{{old('phone', $item->phone)}}" class="form-control" id="phone" placeholder="Điện thoại">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group gender-group">
                                                    <p>Giới tính (<span class="text-danger">*</span>)</p>
                                                    <select name="gender" class=" form-control" data-placeholder="Giới tính" style="width: 100%;">
                                                        <option value="1" @if($item->gender == old('gender', 1)) selected @endif>Nam</option>
                                                        <option value="0" @if($item->gender == old('gender', 2)) selected @endif>Nữ</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group gender-group">
                                                    <p>Dân tộc (<span class="text-danger">*</span>)</p>
                                                    <select name="ethnic_id" id="ethnic_id" class="form-select form-control text-end" aria-label="Default select example" required="">
                                                        @foreach($ethnics as $ethnic)
                                                            <option value="{{ $ethnic->id }}" @if(old('ethnic_id') && old('ethnic_id', $item->ethnic_id) == $ethnic->id) selected @endif>{{ $ethnic->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <p>Ngày cân đo (<span class="text-danger">*</span>)</p>
                                                <div class="form-group calendar-group">
                                                    <input type="text" name="cal_date" value="{{old('cal_date', $item?->cal_date?->format('d/m/YYYY'))}}" class="form-control" id="cal-date" placeholder="Ngày cân đo dd/mm/yyyy" required>
                                                    <i class="icon calendar-icon"></i>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                @if($category != 3)
                                                <p>Ngày sinh (<span class="text-danger">*</span>)</p>
                                                <div class="form-group calendar-group">
                                                    <input type="text" name="birthday" value="{{old('birthday', $item?->birthday?->format('d/m/YYYY'))}}" class="form-control" id="calendar-birth" placeholder="dd/mm/yyyy" required>
                                                    <input id="over19" type="hidden" name="over19" value="{{old('over19', $item->over19)}}" />
                                                    <i class="icon calendar-icon"></i>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <p>Địa chỉ (<span class="text-danger">*</span>)</p>
                                                    <input type="text" name="address" value="{{old('address', $item->address)}}" class="form-control" id="address" placeholder="Địa chỉ" required>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group gender-group">
                                                    <p>Tỉnh/Thành phố (<span class="text-danger">*</span>)</p>
                                                    <select name="province_code" id="province_code" class=" form-control" data-placeholder="Tỉnh/Thành phố" style="width: 100%;" required>
                                                        <option value="">Tỉnh/thành phố</option>
                                                        @foreach($provinces as $province)
                                                            <option value="{{ $province->code }}" @if(old('province_code', $item->province_code) == $province->code) selected @endif>{{ $province->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group gender-group">
                                                    <p>Quận / Huyện (<span class="text-danger">*</span>)</p>
                                                    <select name="district_code" id="district_code" class="form-select form-control text-end" aria-label="Default select example" required="">
                                                        <option value="">Quận/huyện</option>
                                                        @foreach(session('districts', []) as $district)
                                                            <option value="{{ $district->code }}" @if(old('district_code', $item->district_code) == $district->code) selected @endif>{{ $district->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="clearfix visible-xs-block"></div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group gender-group">
                                                    <p>Phường / Xã (<span class="text-danger">*</span>)</p>
                                                    <select name="ward_code" id="ward_code" class="form-select form-control text-end" aria-label="Default select example" required="">
                                                        <option value="">Phường/Xã</option>
                                                        @foreach(session('wards', []) as $ward)
                                                            <option value="{{ $ward->code }}" @if(old('ward_code', $item->ward_code) == $ward->code) selected @endif>{{ $ward->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pro5-divider"></div>
                                <div class="clearfix"></div>
                                <div class="row from-number">
                                    <div class="col-sm-3">
                                        <div class="form-group input-group">
                                            <input id="weight-user-profile" min="0" type="number" step="0.1" required name="weight" value="{{old('weight', $item->weight)}}" class="form-control" placeholder="Nặng 000.0" aria-describedby="addon1" required>
                                            <span class="input-group-addon" id="addon1">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group">
                                            <input id="length-user-profile" type="number" step="0.1" min="0" required name="height" value="{{old('height', $item->height)}}" class="form-control" placeholder="Cao 000.0" aria-describedby="addon2" required>
                                            <span class="input-group-addon" id="addon2">cm</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group">
                                            <input name="age_show" value="{{old('age_show', $item->age_show)}}" class="form-control" placeholder="Số tuổi" aria-describedby="addon3" id="age_show" title="" type="text" readonly style="padding: 6px 8px;">
                                            <span class="input-group-addon" id="addon3">tuổi</span>
                                            <input name="age" value="{{old('age',  $item->age)}}" class="form-control" placeholder="Số tuổi" aria-describedby="addon3" id="age" title="" type="hidden" readonly >
                                            <input type="hidden" name="realAge" id="real-age" value="0">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group input-group">
                                            <input id="bmi-user-profile" type="text" name="bmi" value="{{old('bmi', $item->bmi)}}" class="form-control" placeholder="Số BMI" aria-describedby="addon4" readonly="">
                                            <span class="input-group-addon" id="addon4">BMI</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row" style="width: 100%;">
                                    <div class="col-sm-12 text-center">
                                        @csrf
                                        <input id="category-user-profile" type="hidden" name="category" value="{{$category}}">
                                        <input name="slug" value="{{$slug}}" type="hidden">
                                        @if($item->id)
                                            <input name="id" value="{{$item->id}}" type="hidden">
                                            <input name="uid" value="{{$item->uid}}" type="hidden">
                                        @endif
                                        <button class="nuti-button white" type="submit">Kết quả</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Validate start -->
        <div class="modal nuti-modal common-modal fade modal400" id='amz_common_error_modal' tabindex="-1" role="dialog"
             aria-labelledby="nutiModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="icon close-icon"></i>
                        </button>
                        <h4 class="modal-title" id="nutiModalLabel"></h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="redirect_url" value="" />
                        <strong></strong>

                    </div>
                </div>
            </div>
        </div>
        <!-- Validate end -->
    </section>
@endsection

@push('foot')
    <!-- controler monthAction 550 -->
    <script type="text/javascript">
        $(document).ready(function() {
            // Khi có thay đổi trong select province
            $('#province_code').change(function () {
                var province_code = $(this).val(); // Lấy giá trị province id được chọn
                // Gửi yêu cầu Ajax
                $.ajax({
                    url: '{{route('web.ajax_get_district_by_province')}}', // Đường dẫn tới route xử lý lấy danh sách district
                    method: 'GET',
                    data: {province_code: province_code}, // Truyền province id qua request
                    success: function (response) {
                        // Xử lý khi nhận được danh sách district từ server
                        var districtSelect = $('#district_code'); // Select element cho district
                        var wardSelect = $('#ward_code'); // Select element cho district

                        // Xóa tất cả các option cũ trong select district
                        districtSelect.find('option').remove();
                        wardSelect.find('option').remove();
                        wardSelect.append('<option value="">Chọn phường xã</option>');

                        // Thêm các option mới cho district từ danh sách nhận được
                        districtSelect.append('<option value="">Chọn quận huyện</option>');
                        $.each(response.districts, function (key, value) {
                            districtSelect.append('<option value="' + value.code + '">' + value.name + '</option>');
                        });

                    },
                    error: function (xhr, status, error) {
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

        $(window).load(function() {
            document.getElementById("age").addEventListener("change", age19);

            function age19() {
                var a = document.getElementById("age").value;
                if (a < 19) {
                    alert('Bé nhỏ hơn 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                    $("#age").val('');
                }
            }

            var getMonthUrl = "{{url('/ajax/tinh-ngay-sinh')}}";
            var gMonth;

            function getMonthAjax(birthdate, date) {
                $.ajax({
                    url: getMonthUrl,
                    data: {
                        'birthday': birthdate,
                        'date': date
                    },
                    success: function(response) {
                        var months = response;
                        var age = Math.floor(months / 12);
                        $("#addon3").text('tuổi');
                        if (category == 1) {
                            if (months < 61) { //0 - 60 tháng == 0- 5 tuổi
                                $("#age_show").val(months + ' tháng');
                                $("#age").val(months);
                            } else {
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé lớn hơn 5 tuổi hoặc > 61 tháng. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        } else if (category == 2) {
                            if (months >= 61 && months < 72) {
                                $("#age_show").val(months + ' tháng');
                                $("#age").val(months);
                            } else if (months >= 72 && months < 229) {
                                console.log(getAge(birthdate, date));
                                $("#addon3").text('');
                                $('#age').val(getAge(birthdate, date));
                            } else {
                                console.log(getAge(birthdate, date));
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé nhỏ hơn 5 tuổi hoặc > 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        } else if (category == 3) {
                            if (months >= 229) {
                                $('#age').val(age);
                            } else {
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé nhỏ hơn 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        }

                        $('#real-age').val(months / 12);
                    },
                    error: function(jqXHR, textStatus) {
                        if (jqXHR.status == 401) {
                            $('#age').val('');
                            alert(jqXHR.responseText);
                        } else {
                            // alert('Không thể kiểm tra tuổi của đối tượng, có thể xảy ra lỗi kết nối đến hệ thống. Xin vui lòng kiểm tra lại');
                        }

                    }
                })
            }

            function getAge(dateString, date) {
                console.log(date);
                console.log(dateString);
                // var today = new Date(now.getYear(),now.getMonth(),now.getDate());
                var now = new Date(date.substring(6, 10),
                    date.substring(3, 5) - 1,
                    date.substring(0, 2)
                );
                var yearNow = now.getYear();
                var monthNow = now.getMonth();
                var dateNow = now.getDate();

                var dob = new Date(dateString.substring(6, 10),
                    dateString.substring(3, 5) - 1,
                    dateString.substring(0, 2)
                );
                console.log(now);
                console.log(dob);
                var yearDob = dob.getYear();
                var monthDob = dob.getMonth();
                var dateDob = dob.getDate();
                var age = {};
                var ageString = "";
                var yearString = "";
                var monthString = "";
                var dayString = "";

                yearAge = yearNow - yearDob;

                if (monthNow >= monthDob)
                    var monthAge = monthNow - monthDob;
                else {
                    yearAge--;
                    var monthAge = 12 + monthNow - monthDob;
                }

                if (dateNow >= dateDob)
                    var dateAge = dateNow - dateDob;
                else {
                    monthAge--;
                    var dateAge = 31 + dateNow - dateDob;

                    if (monthAge < 0) {
                        monthAge = 11;
                        yearAge--;
                    }
                }

                age = {
                    years: yearAge,
                    months: monthAge,
                    days: dateAge
                };

                if (age.years > 1) yearString = " tuổi";
                else yearString = " tuổi";
                if (age.months > 1) monthString = " tháng";
                else monthString = " tháng";
                if (age.days > 1) dayString = " ngày";
                else dayString = " ngày";

                if ((age.years > 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.years + yearString + ", " + age.months + monthString;
                else if ((age.years == 0) && (age.months == 0) && (age.days > 0))
                    ageString = "Chỉ " + age.days + dayString + " tuổi!";
                else if ((age.years > 0) && (age.months == 0) && (age.days == 0))
                    ageString = age.years + yearString + " 0 tháng";
                else if ((age.years > 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.years + yearString + " " + age.months + monthString + ".";
                else if ((age.years == 0) && (age.months > 0) && (age.days > 0))
                    ageString = age.months + monthString;
                else if ((age.years > 0) && (age.months == 0) && (age.days > 0))
                    ageString = age.years + yearString + " " + age.months + monthString;
                else if ((age.years == 0) && (age.months > 0) && (age.days == 0))
                    ageString = age.months + monthString + ".";
                else ageString = "Oops! Could not calculate age!";

                return ageString;
            }

            function monthDiff(d1, d2) {
                var d1Y = d1.getFullYear();
                var d2Y = d2.getFullYear();
                var d1M = d1.getMonth();
                var d2M = d2.getMonth();

                return (d1M + 12 * d1Y) - (d2M + 12 * d2Y);
            }

            var category = {{ $category }}; // Get category from server-side blade variable

            $("#cal-date").datetimepicker({
                format: 'DD/MM/YYYY',
                defaultDate: new Date(),
                maxDate: new Date()
            }).on('dp.change', function(e) {
                var decrementDay = moment(new Date(e.date));
                // decrementDay.subtract(1, 'days');
                $('#calendar-birth').data('DateTimePicker').maxDate(decrementDay);
                $(this).data("DateTimePicker").hide();
                
                // Chỉ gọi AJAX nếu cả 2 trường đã có giá trị
                if ($("#calendar-birth").val() && $("#cal-date").val()) {
                    getMonthAjax($("#calendar-birth").val(), $("#cal-date").val());
                }
            });

            $("#calendar-birth").datetimepicker({
                @if(old('birthday'))
                defaultDate: moment('{{ old('birthday') }}', 'DD/MM/YYYY').toDate(),
                @endif
                format: 'DD/MM/YYYY',
                maxDate: new Date()
            }).on('dp.change', function(e) {
                var incrementDay = moment(new Date(e.date));
                // incrementDay.add(1, 'days');
                $('#cal-date').data('DateTimePicker').minDate(incrementDay);
                $(this).data("DateTimePicker").hide();
                
                // Chỉ gọi AJAX nếu cả 2 trường đã có giá trị
                if ($("#calendar-birth").val() && $("#cal-date").val()) {
                    getMonthAjax($("#calendar-birth").val(), $("#cal-date").val());
                }
            });

            $("#last-name").focus();
            // $("#calendar-birth").val("");
            var availableCities = [
                "AN GIANG",
                "BÀ RỊA     - VŨNG TÀU",
                "BẮC GIANG",
                "BẮC KẠN",
                "BẠC LIÊU",
                "BẮC NINH",
                "BẾN TRE",
                "BÌNH ĐỊNH",
                "BÌNH DƯƠNG",
                "BÌNH PHƯỚC",
                "BÌNH THUẬN",
                "CÀ MAU",
                "CẦN THƠ",
                "CAO BẰNG",
                "ĐÀ NẴNG",
                "ĐẮK LẮK",
                "ĐẮK NÔNG",
                "ĐIỆN BIÊN",
                "ĐỒNG NAI",
                "ĐỒNG THÁP",
                "GIA LAI",
                "HÀ GIANG",
                "HÀ NAM",
                "HÀ NỘI",
                "HÀ TĨNH",
                "HẢI DƯƠNG",
                "HẢI PHÒNG",
                "HẬU GIANG",
                "HỒ CHÍ MINH",
                "HÒA BÌNH",
                "HƯNG YÊN",
                "KHÁNH HÒA",
                "KIÊN GIANG",
                "KON TUM",
                "LAI CHÂU",
                "LÂM ĐỒNG",
                "LẠNG SƠN",
                "LÀO CAI",
                "LONG AN",
                "NAM ĐỊNH",
                "NGHỆ AN",
                "NINH BÌNH",
                "NINH THUẬN",
                "PHÚ THỌ",
                "PHÚ YÊN",
                "QUẢNG BÌNH",
                "QUẢNG NAM",
                "QUẢNG NGÃI",
                "QUẢNG NINH",
                "QUẢNG TRỊ",
                "SÓC TRĂNG",
                "SƠN LA",
                "TÂY NINH",
                "THÁI BÌNH",
                "THÁI NGUYÊN",
                "THANH HÓA",
                "THỪA THIÊN HUẾ",
                "TIỀN GIANG",
                "TRÀ VINH",
                "TUYÊN QUANG",
                "VĨNH LONG",
                "VĨNH PHÚC",
                "YÊN BÁI",
            ];
            $("#address").autocomplete({
                source: function(request, response) {
                    var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                    response($.grep(availableCities, function(item) {
                        var result = matcher.test(item);
                        return result
                    }));
                },
            });

            if (category === 3) {
                $("#age").change(function() {
                    var date = new Date();
                    var age = $("#age").val()
                    var year = date.getFullYear() - parseInt(age);
                    // $('#real-age').val(age);
                    $('#real-age').attr('value', age);
                    // $("#calendar-birth").val('01/01/' + year);
                    $('#calendar-birth').attr('value', '01/01/' + year);
                    // $("#over19").val("1");
                    $('#over19').attr('value', '1');
                });
            }

            function checkValidateBeforeSubmitForm() {
                var isValid = true;
                var invalidCounter = 0;

                function isValidDate(d) {
                    return d instanceof Date && !isNaN(d);
                }

                var ngaySinhVal = $('#calendar-birth').val();
                //regex convert 20/09/2018 to 09/20/2018
                var ngaySinhCheck = new Date(ngaySinhVal.replace(/(\d{2})\/(\d{2})\/(\d{4})/, "$2/$1/$3"));
                console.log('ngaySinhVal', ngaySinhVal);
                //console.log('ngaySinhCheck', ngaySinhCheck, isValidDate(ngaySinhCheck));
                //co loi thi tang bien dem them 1;
                if (isValidDate(ngaySinhCheck) === false) {
                    invalidCounter++;
                }

                //TODO check valid other properties
                console.log('invalidCounter', invalidCounter);
                //has invalid return false
                return (invalidCounter > 0) ? false : true;
            }

            $(".pro5-form").submit(function(event) {
                if (checkValidateBeforeSubmitForm() !== true) {
                    event.preventDefault();
                };

                if ($('#real-age').val() == "") {
                    alert("VUI LÒNG KIỂM TRA LẠI ĐƯỜNG TRUYỀN VÀ NHẬP LẠI NGÀY THÁNG NĂM SINH CỦA ĐỐI TƯỢNG");
                    return false;
                }
            });


            function alert(message, title) {
                if (title == undefined) {
                    title = "Thông báo";
                }
                $("#amz_common_error_modal h4").html(title);
                $("#amz_common_error_modal .modal-body strong").html(message);
                $("#amz_common_error_modal").modal('show');
            }
        });

        document.getElementById('avatar-wapper').addEventListener('click', function() {
            document.getElementById('avatar-input').click();
        });

        document.getElementById('avatar-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                    document.getElementById('title-name').style.display = 'none'; // ẩn title
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
