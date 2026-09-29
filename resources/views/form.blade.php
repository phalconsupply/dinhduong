@extends('layouts.app')
@section('content')
    <!-- Main Content Wrapper with Modern Style -->
    <div class="main-content-wrapper">
        <div class="content-body">
            <section id="nuti-medical">
                <div class="container-fluid">
                    <div class="row">
                        {{-- Removed sidebar, now full width --}}
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            @include('sections.form-heading')
                            
                            <!-- Progress Steps -->
                            <div class="form-progress-wrapper">
                                {{-- div role=navigation thay vì <nav>: web/js/b47b5bf.js (template cũ) ép mọi thẻ <nav>
                                     thành position:fixed khi cuộn quá 103px, làm thanh này đè lên nội dung --}}
                                <div class="form-steps" role="navigation" aria-label="Các phần của biểu mẫu">
                                    <a href="#phan-thong-tin" class="step active" data-step="1" aria-current="step">
                                        <div class="step-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="step-label">Thông tin cá nhân</div>
                                        <div class="step-connector"></div>
                                    </a>
                                    <a href="#phan-dia-chi" class="step" data-step="2">
                                        <div class="step-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="step-label">Địa chỉ</div>
                                        <div class="step-connector"></div>
                                    </a>
                                    <a href="#phan-chi-so" class="step" data-step="3">
                                        <div class="step-icon">
                                            <i class="fas fa-weight"></i>
                                        </div>
                                        <div class="step-label">Chỉ số sức khỏe</div>
                                    </a>
                                </div>
                            </div>
                    
                    <div class="">
                        <div id="tab-2" class="profile-detail-menu-content" style="">
                            @include('layouts.alert')
                            <form class="pro5-form" action="{{ route('form.post', ['slug' => $slug]) }}" method="POST" enctype="multipart/form-data">
                                
                                <!-- BLOCK 1: Avatar (1/3) + Personal Information (2/3) -->
                                <div class="row">
                                    <!-- Avatar Section - 1/3 width -->
                                    <div class="col-xs-12 col-md-4">
                                        @include('sections.form-avatar')
                                    </div>
                                    
                                    <!-- Personal Information Section - 2/3 width -->
                                    <div class="col-xs-12 col-md-8">
                                        <div class="form-section-card" id="phan-thong-tin" data-buoc="1">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-user-circle"></i>
                                                </div>
                                                <h3 class="card-title">Thông tin cá nhân</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="pro5-input">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-floating-group">
                                                    <label for="last-name">Họ và tên <span class="required">*</span></label>
                                                    <input type="text" name="fullname" value="{{old('fullname', $item->fullname)}}" class="form-control @error('fullname') is-invalid @enderror" id="last-name" placeholder="Nhập họ và tên" maxlength="50" autocomplete="name" required @error('fullname') aria-invalid="true" @enderror>
                                                    <div class="input-icon">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group">
                                                    <label for="id_number">Mã định danh (CCCD)</label>
                                                    <input type="text" inputmode="numeric" pattern="[0-9]{10,12}" maxlength="12" name="id_number" value="{{old('id_number', $item->id_number)}}" class="form-control" id="id_number" placeholder="Nhập số CCCD" title="10–12 chữ số">
                                                    <div class="input-icon">
                                                        <i class="fas fa-id-card"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group">
                                                    <label for="phone">Số điện thoại</label>
                                                    <input type="tel" inputmode="tel" autocomplete="tel" pattern="[0-9]{10,12}" maxlength="12" name="phone" value="{{old('phone', $item->phone)}}" class="form-control" id="phone" placeholder="Nhập số điện thoại" title="10–12 chữ số">
                                                    <div class="input-icon">
                                                        <i class="fas fa-phone"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group">
                                                    <label for="gender">Giới tính <span class="required">*</span></label>
                                                    <select name="gender" id="gender" class="form-control" style="width: 100%;">
                                                        <option value="1" @if(old('gender', $item->gender) == 1) selected @endif>Nam</option>
                                                        <option value="0" @if(old('gender', $item->gender) == 0) selected @endif>Nữ</option>
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-venus-mars"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group">
                                                    <label for="ethnic_id">Dân tộc <span class="required">*</span></label>
                                                    <select name="ethnic_id" id="ethnic_id" class="form-control" required="">
                                                        @foreach($ethnics as $ethnic)
                                                            <option value="{{ $ethnic->id }}" @if(old('ethnic_id', $item->ethnic_id) == $ethnic->id) selected @endif>{{ $ethnic->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-globe-asia"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group calendar-group-modern">
                                                    <label for="cal-date">Ngày cân đo <span class="required">*</span></label>
                                                    <input type="text" name="cal_date" value="{{old('cal_date', $item?->cal_date?->format('d/m/Y'))}}" class="form-control" id="cal-date" placeholder="Chọn ngày cân đo" required>
                                                    <div class="input-icon">
                                                        <i class="fas fa-calendar-day"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="form-floating-group calendar-group-modern">
                                                    <label for="calendar-birth">Ngày sinh <span class="required">*</span></label>
                                                    <input type="text" name="birthday" value="{{old('birthday', $item?->birthday?->format('d/m/Y'))}}" class="form-control" id="calendar-birth" placeholder="Chọn ngày sinh" required>
                                                    <div class="input-icon">
                                                        <i class="fas fa-birthday-cake"></i>
                                                    </div>
                                                    <input id="over19" type="hidden" name="over19" value="{{old('over19', $item->over19)}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 1 -->
                                
                                <!-- BLOCK 2: Address (Full Width) -->
                                <div class="form-section-card" id="phan-dia-chi" data-buoc="2">
                                    <div class="card-header">
                                        <div class="card-icon">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <h3 class="card-title">Địa chỉ</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="pro5-input">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-floating-group">
                                                        <label for="address">Địa chỉ <span class="required">*</span></label>
                                                        <input type="text" name="address" value="{{old('address', $item->address)}}" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Nhập địa chỉ (tối đa 500 ký tự)" maxlength="500" required @error('address') aria-invalid="true" @enderror>
                                                        <div class="input-icon">
                                                            <i class="fas fa-home"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="clearfix"></div>
                                            @if($item->exists && $item->can_chon_xa_moi)
                                                {{-- Hồ sơ cũ mà xã cũ bị tách / không ánh xạ được sang địa bàn 2026 --}}
                                                <div class="alert alert-warning small">
                                                    Hồ sơ này lập theo địa bàn cũ: <strong>{{ $item->dia_ban_cu }}</strong>.
                                                    Không xác định được xã tương ứng theo địa bàn mới (sau sáp nhập 01/7/2025) —
                                                    vui lòng chọn lại Tỉnh/Thành phố và Phường/Xã.
                                                    @if($goiY = $item->ungVienXaMoi())
                                                        Gợi ý: {{ implode(', ', $goiY) }}.
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-floating-group">
                                                        <label for="province_code">Tỉnh/Thành phố <span class="required">*</span></label>
                                                        <select name="province_code" id="province_code" data-wards-url="{{ route('web.ajax_get_ward_by_province') }}" class="form-control" data-placeholder="Tỉnh/Thành phố" style="width: 100%;" required>
                                                            <option value="">Chọn Tỉnh/thành phố</option>
                                                            @foreach($provinces as $province)
                                                                <option value="{{ $province->code }}" @if(old('province_code', $item->province_code_2026) == $province->code) selected @endif>{{ $province->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-icon">
                                                            <i class="fas fa-map"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-floating-group">
                                                        <label for="ward_code">Phường / Xã <span class="required">*</span></label>
                                                        <select name="ward_code" id="ward_code" data-placeholder="Chọn Phường/Xã" class="form-control" aria-label="Default select example" required="">
                                                            <option value="">Chọn Phường/Xã</option>
                                                            @foreach(session('wards', []) as $ward)
                                                                <option value="{{ $ward->code }}" @if(old('ward_code', $item->ward_code_2026) == $ward->code) selected @endif>{{ $ward->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="input-icon">
                                                            <i class="fas fa-map-pin"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 2 -->
                                    
                                <!-- BLOCK 3: Birth Information (left) + Health Measurements (right) - Equal Width -->
                                <div class="row" id="phan-chi-so" data-buoc="3">
                                    {{-- Thông tin lúc sinh chỉ dùng cho trẻ 0-5 tuổi; với đối
                                         tượng 5-19 tuổi nó không tham gia đánh giá nào nên ẩn đi.
                                         Khi ẩn, khối đo lường chiếm trọn chiều ngang. --}}
                                    @if($category == 1)
                                    <!-- Birth Information Section (LEFT 50%) -->
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-section-card">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-baby"></i>
                                                </div>
                                                <h3 class="card-title">Thông tin lúc sinh</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-floating-group">
                                                    <label for="birth-weight">Cân nặng lúc sinh</label>
                                                    <input id="birth-weight" min="0" type="number" step="1" name="birth_weight" value="{{old('birth_weight', $item->birth_weight)}}" class="form-control" placeholder="Nhập cân nặng (gram)">
                                                    <div class="input-icon">
                                                        <i class="fas fa-weight"></i>
                                                    </div>
                                                    <small class="text-muted" style="display: block; margin-top: 5px;">Đơn vị: gram</small>
                                                </div>
                                                
                                                <div class="form-floating-group">
                                                    <label for="gestational-age">Tuổi thai lúc sinh</label>
                                                    <select name="gestational_age" id="gestational-age" class="form-control">
                                                        <option value="">Chọn tuổi thai</option>
                                                        <option value="Đủ tháng" {{old('gestational_age', $item->gestational_age) == 'Đủ tháng' ? 'selected' : ''}}>Đủ tháng</option>
                                                        <option value="Thiếu tháng" {{old('gestational_age', $item->gestational_age) == 'Thiếu tháng' ? 'selected' : ''}}>Thiếu tháng</option>
                                                    </select>
                                                    <div class="input-icon">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </div>
                                                </div>
                                                
                                                <div class="form-floating-group">
                                                    <label for="birth-weight-category">Phân loại cân nặng</label>
                                                    <input id="birth-weight-category" type="text" name="birth_weight_category_display" value="{{old('birth_weight_category', $item->birth_weight_category)}}" class="form-control" placeholder="Tự động tính" readonly style="background-color: #f8f9fa; font-weight: 600;">
                                                    <input type="hidden" name="birth_weight_category" id="birth-weight-category-hidden" value="{{old('birth_weight_category', $item->birth_weight_category)}}">
                                                    <div class="input-icon">
                                                        <i class="fas fa-info-circle"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Health Measurements Section -->
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-section-card">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-heartbeat"></i>
                                                </div>
                                                <h3 class="card-title">Chỉ số sức khỏe</h3>
                                            </div>
                                            <div class="card-body">
                                                <!-- Measurement Cards Grid -->
                                                <div class="measurement-grid">
                                                    <!-- Weight Card -->
                                                    <div class="measurement-card weight">
                                                        <div class="measurement-icon">⚖️</div>
                                                        <div class="measurement-value">
                                                            <input id="weight-user-profile" aria-label="Cân nặng (kg)" inputmode="decimal" min="0" type="number" step="0.1" required name="weight" value="{{old('weight', $item->weight)}}" placeholder="0.0">
                                                            <span class="unit">kg</span>
                                                        </div>
                                                        <div class="measurement-label">Cân nặng</div>
                                                    </div>
                                                    
                                                    <!-- Height Card -->
                                                    <div class="measurement-card height">
                                                        <div class="measurement-icon">📏</div>
                                                        <div class="measurement-value">
                                                            <input id="length-user-profile" aria-label="Chiều cao (cm)" inputmode="decimal" type="number" step="0.1" min="0" required name="height" value="{{old('height', $item->height)}}" placeholder="0.0">
                                                            <span class="unit">cm</span>
                                                        </div>
                                                        <div class="measurement-label">Chiều cao</div>
                                                    </div>
                                                    
                                                    <!-- Age Card -->
                                                    <div class="measurement-card age">
                                                        <div class="measurement-icon">🎂</div>
                                                        <div class="measurement-value">
                                                            <output id="age-display" class="age-output" for="calendar-birth cal-date" aria-live="polite">{{ old('age_show', $item->age_show) ?: '--' }}</output>
                                                        </div>
                                                        <div class="measurement-label">Tuổi</div>
                                                        <input name="age_show" value="{{old('age_show', $item->age_show)}}" id="age_show" type="hidden">
                                                        <input name="age" value="{{old('age',  $item->age)}}" id="age" type="hidden" readonly>
                                                        <input type="hidden" name="realAge" id="real-age" value="{{ old('realAge', $item->realAge) }}">
                                                    </div>
                                                    
                                                    <!-- BMI Card -->
                                                    <div class="measurement-card bmi" id="bmi-card">
                                                        <div class="measurement-icon">📊</div>
                                                        <div class="measurement-value">
                                                            <input id="bmi-user-profile" aria-label="Chỉ số BMI (tự tính)" type="text" name="bmi" value="{{old('bmi', $item->bmi)}}" readonly="" placeholder="--">
                                                            <span class="unit">BMI</span>
                                                        </div>
                                                        <div class="measurement-label">Chỉ số BMI</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Classification Info Panel: toàn hàng ở 0-5 tuổi (đã có 2 thẻ phía trên), nửa hàng ở biểu mẫu còn lại -->
                                    <div class="col-xs-12 {{ $category == 1 ? 'col-md-12' : 'col-md-6' }}">
                                        <div class="form-section-card classification-info-panel">
                                            <div class="card-header">
                                                <div class="card-icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <h3 class="card-title">Phân loại & Bảng chuẩn WHO</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="classification-display" id="classification-info">
                                                    <div class="info-card age-group-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-child"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Nhóm tuổi</h6>
                                                            <p class="info-value" id="age-group-info">Chưa xác định</p>
                                                            <small class="info-detail" id="age-group-detail">Nhập ngày sinh để xác định</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card standard-table-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-table"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Bảng chuẩn sử dụng</h6>
                                                            <p class="info-value" id="standard-table-info">--</p>
                                                            <small class="info-detail" id="standard-table-detail">Tự động chọn theo tuổi</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card measurement-method-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-ruler"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Phương pháp đo</h6>
                                                            <p class="info-value" id="measurement-method-info">--</p>
                                                            <small class="info-detail" id="measurement-method-detail">Phụ thuộc vào tuổi</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="info-card calculation-method-card">
                                                        <div class="info-icon">
                                                            <i class="fas fa-calculator"></i>
                                                        </div>
                                                        <div class="info-content">
                                                            <h6 class="info-title">Phương pháp tính toán</h6>
                                                            <p class="info-value" id="calculation-method-info">{{ [1 => 'WHO 2006 — LMS', 2 => 'WHO 2007 — LMS', 3 => 'Không áp dụng chuẩn WHO trẻ em'][$category] ?? '--' }}</p>
                                                            <small class="info-detail" id="calculation-method-detail">{{ $category == 3 ? 'Ngoài phạm vi 0–19 tuổi' : 'Lambda-Mu-Sigma' }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Link to Guide -->
                                                <div class="guide-link-wrapper">
                                                    <a href="/huong-dan-danh-gia-dinh-duong.html" target="_blank" class="guide-link">
                                                        <i class="fas fa-book-open"></i>
                                                        Xem hướng dẫn chi tiết
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End BLOCK 3 -->
                                
                                <!-- Submit Button -->
                                <div class="submit-button-wrapper" style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
                                        @csrf
                                        <input id="category-user-profile" type="hidden" name="category" value="{{$category}}">
                                        <input name="slug" value="{{$slug}}" type="hidden">
                                        @if($item->id)
                                            <input name="id" value="{{$item->id}}" type="hidden">
                                            <input name="uid" value="{{$item->uid}}" type="hidden">
                                        @endif
                                        <button class="btn-submit-form" type="submit">
                                            <i class="fas fa-search"></i> Xem kết quả
                                        </button>
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
        </div>
    </div>
@endsection

@push('foot')
    <script src="{{ asset('web/js/dia-ban-2026.js') }}"></script>
    <script>
        // Lỗi validation từ server: đánh dấu từng ô (is-invalid, aria-invalid, aria-describedby),
        // chèn thông báo ngay dưới ô và đưa focus tới phần tóm tắt lỗi. Sửa ô nào thì gỡ lỗi ô đó.
        (function () {
            var nguon = document.getElementById('loi-theo-truong');
            if (!nguon) return;
            var loi = JSON.parse(nguon.textContent || '{}');
            var oDanXuat = {age: 'birthday', realAge: 'birthday', thumb: null};
            var daGan = {};
            Object.keys(loi).forEach(function (truong) {
                var ten = oDanXuat.hasOwnProperty(truong) ? oDanXuat[truong] : truong;
                var o = ten ? document.querySelector('[name="' + ten + '"]') : document.getElementById('avatar-wapper');
                if (!o || daGan[ten || truong]) return;
                daGan[ten || truong] = true;
                var idLoi = 'loi-' + (ten || truong);
                var tb = document.createElement('div');
                tb.className = 'loi-truong';
                tb.id = idLoi;
                tb.textContent = loi[truong];
                var khung = o.closest('.form-floating-group, .measurement-card, .pro5-avatar') || o.parentNode;
                khung.appendChild(tb);
                o.classList.add('is-invalid');
                o.setAttribute('aria-invalid', 'true');
                o.setAttribute('aria-describedby', ((o.getAttribute('aria-describedby') || '') + ' ' + idLoi).trim());
                var go = function () {
                    o.classList.remove('is-invalid');
                    o.removeAttribute('aria-invalid');
                    if (tb.parentNode) tb.parentNode.removeChild(tb);
                };
                o.addEventListener('input', go, {once: true});
                o.addEventListener('change', go, {once: true});
            });
            var tomTat = document.getElementById('tom-tat-loi');
            if (tomTat) tomTat.focus();
        })();
    </script>
    <!-- controler monthAction 550 -->
    <script type="text/javascript">

        $(window).load(function() {

            var getMonthUrl = "{{url('/ajax/tinh-ngay-sinh')}}";
            var gMonth;

            // Hiển thị tuổi cho người dùng đọc; ô ẩn #age_show mang cùng chuỗi đi lưu
            function hienTuoi(chuoi, laLoi) {
                $('#age_show').val(laLoi ? '' : chuoi);
                $('#age-display').text(chuoi || '--').toggleClass('age-output--error', !!laLoi);
            }

            function getMonthAjax(birthdate, date) {
                // Ngày vừa đổi: tuổi cũ không còn đúng, xoá trước khi tính lại
                $('#age').val('');
                $('#real-age').val('');
                hienTuoi('Đang tính…');
                $.ajax({
                    url: getMonthUrl,
                    data: {
                        'birthday': birthdate,
                        'date': date
                    },
                    success: function(response) {
                        var months = response;
                        var age = Math.floor(months / 12);
                        // Ô #age LUÔN mang tuổi theo THÁNG THẬP PHÂN — đây là đơn vị
                        // mà engine WHO dùng để tra bảng. Ô #age_show chỉ để người
                        // dùng đọc. Trước đây với category 2 và tuổi >= 72 tháng, #age
                        // bị gán bằng số NĂM nên engine tra sai hoàn toàn.

                        if (category == 1) {
                            // Dưới 60 tháng: chuẩn WHO 2006 (0-5 tuổi)
                            if (months < 60) {
                                hienTuoi(months + ' tháng');
                                $("#age").val(months);
                            } else {
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                hienTuoi('--');
                                alert('Trẻ đã từ 5 tuổi (60 tháng) trở lên. Vui lòng dùng biểu mẫu "Từ 5 đến 19 tuổi".');
                                return false;
                            }
                        } else if (category == 2) {
                            // Từ 60 tháng đến dưới 19 tuổi (228 tháng): chuẩn WHO 2007
                            if (months >= 60 && months < 229) {
                                hienTuoi(moTaTuoi(months));
                                $("#age").val(months);
                            } else {
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert(months < 60
                                    ? 'Trẻ chưa đủ 5 tuổi (60 tháng). Vui lòng dùng biểu mẫu "Từ 0 đến 5 tuổi".'
                                    : 'Đã từ 19 tuổi trở lên. Vui lòng dùng biểu mẫu "Từ 19 tuổi".');
                                return false;
                            }
                        } else if (category == 3) {
                            if (months >= 229) {
                                hienTuoi(moTaTuoi(months));
                                $('#age').val(months);
                                $('#over19').val('1');
                            } else {
                                $("#calendar-birth").val("");
                                $('#age').val('');
                                $('#real-age').val('');
                                alert('Bé nhỏ hơn 19 tuổi. Vui lòng chọn độ tuổi thích hợp!!');
                                return false;
                            }
                        }

                        $('#real-age').val(months / 12);
                        
                        // Cập nhật thông tin phân loại và bảng chuẩn
                        updateClassificationInfo(months);
                    },
                    error: function(jqXHR, textStatus) {
                        $('#age').val('');
                        hienTuoi('Không tính được tuổi', true);
                        if (jqXHR.status == 401) {
                            alert(jqXHR.responseText);
                        } else {
                            alert('Không tính được tuổi của đối tượng — có thể do lỗi kết nối. Vui lòng kiểm tra lại ngày sinh, ngày cân đo rồi chọn lại.');
                        }
                    }
                })
            }

            // Mô tả tuổi cho người dùng đọc: 150.4 tháng -> "12 tuổi 6 tháng"
            function moTaTuoi(thang) {
                var nam = Math.floor(thang / 12);
                var du = Math.floor(thang % 12);
                if (du === 0) {
                    return nam + ' tuổi';
                }
                return nam + ' tuổi ' + du + ' tháng';
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

            // Có lỗi validation thì focus đã nằm ở phần tóm tắt lỗi, không cướp lại
            if (!document.getElementById('tom-tat-loi')) {
                $("#last-name").focus();
            }
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

            function checkValidateBeforeSubmitForm() {
                var isValid = true;
                var invalidCounter = 0;

                function isValidDate(d) {
                    return d instanceof Date && !isNaN(d);
                }

                var ngaySinhVal = $('#calendar-birth').val() || '';
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

                if (!$('#real-age').val() || !$('#age').val()) {
                    event.preventDefault();
                    alert("Chưa tính được tuổi của đối tượng. Vui lòng kiểm tra lại ngày sinh, ngày cân đo và đường truyền.");
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

        // Thanh bước: đánh dấu phần người dùng đang nhập (không phải wizard nhiều trang)
        document.querySelector('.pro5-form').addEventListener('focusin', function (e) {
            var phan = e.target.closest('[data-buoc]');
            if (!phan) return;
            document.querySelectorAll('.form-steps .step').forEach(function (buoc) {
                var dung = buoc.getAttribute('data-step') === phan.getAttribute('data-buoc');
                buoc.classList.toggle('active', dung);
                if (dung) { buoc.setAttribute('aria-current', 'step'); } else { buoc.removeAttribute('aria-current'); }
            });
        });

        document.getElementById('avatar-wapper').addEventListener('click', function() {
            document.getElementById('avatar-input').click();
        });
        // Bàn phím: Enter / Space trên khung ảnh cũng mở hộp chọn tệp
        document.getElementById('avatar-wapper').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                document.getElementById('avatar-input').click();
            }
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

        // Logic phân loại cân nặng lúc sinh.
        // Khối này chỉ tồn tại ở biểu mẫu 0-5 tuổi nên phải kiểm tra trước khi gắn
        // sự kiện, nếu không toàn bộ script phía sau sẽ chết ở các biểu mẫu khác.
        var oCanNangLucSinh = document.getElementById('birth-weight');
        if (oCanNangLucSinh) {
            oCanNangLucSinh.addEventListener('input', function() {
                classifyBirthWeight();
            });
        }

        function classifyBirthWeight() {
            const oCanNang = document.getElementById('birth-weight');
            if (!oCanNang) {
                return;
            }
            const birthWeight = parseFloat(oCanNang.value);
            const categoryDisplay = document.getElementById('birth-weight-category');
            const categoryHidden = document.getElementById('birth-weight-category-hidden');
            if (!categoryDisplay || !categoryHidden) {
                return;
            }
            
            if (isNaN(birthWeight) || birthWeight <= 0) {
                categoryDisplay.value = '';
                categoryHidden.value = '';
                categoryDisplay.style.backgroundColor = '#f5f5f5';
                categoryDisplay.style.color = '#333';
                return;
            }

            let category = '';
            let bgColor = '#f5f5f5';
            let textColor = '#333';

            if (birthWeight < 2500) {
                category = 'Nhẹ cân';
                bgColor = '#fff3cd'; // Vàng nhạt
                textColor = '#856404';
            } else if (birthWeight >= 2500 && birthWeight <= 4000) {
                category = 'Đủ cân';
                bgColor = '#d4edda'; // Xanh nhạt
                textColor = '#155724';
            } else if (birthWeight > 4000) {
                category = 'Thừa cân';
                bgColor = '#f8d7da'; // Đỏ nhạt
                textColor = '#721c24';
            }

            categoryDisplay.value = category;
            categoryHidden.value = category;
            categoryDisplay.style.backgroundColor = bgColor;
            categoryDisplay.style.color = textColor;
            categoryDisplay.style.fontWeight = 'bold';
        }

        // Chạy phân loại khi load trang nếu đã có giá trị
        window.addEventListener('DOMContentLoaded', function() {
            var o = document.getElementById('birth-weight');
            if (o && o.value) {
                classifyBirthWeight();
            }
        });

        /**
         * Cập nhật thông tin phân loại và bảng chuẩn WHO LMS dựa trên tuổi
         */
        function updateClassificationInfo(ageInMonths) {
            const ageInWeeks = ageInMonths * 4.33;
            
            // Xác định nhóm tuổi
            let ageGroup = '';
            let ageGroupDetail = '';
            let standardTable = '';
            let standardTableDetail = '';
            let measurementMethod = '';
            let measurementMethodDetail = '';
            
            // Mốc nằm/đứng của WHO là 731 ngày tuổi (≈ 24,02 tháng), không phải 24 tháng tròn
            const duoiMocNam = ageInMonths * 30.4375 < 731;
            if (ageInMonths < 60) {
                ageGroup = ageInWeeks <= 13 ? 'Trẻ sơ sinh (0-13 tuần)' : (duoiMocNam ? 'Trẻ dưới 2 tuổi' : 'Trẻ 2-5 tuổi');
                ageGroupDetail = duoiMocNam ? 'Giai đoạn tăng trưởng nhanh' : 'Giai đoạn ổn định tăng trưởng';
                standardTable = 'WHO 2006 (0-60 tháng)';
                standardTableDetail = 'Tra theo ngày tuổi — CN/T, CC/T, CN/CC, BMI/T';
                measurementMethod = duoiMocNam ? 'Chiều dài nằm' : 'Chiều cao đứng';
                measurementMethodDetail = duoiMocNam ? 'Cân nặng theo chiều dài (dưới 731 ngày tuổi)' : 'Cân nặng theo chiều cao (từ 731 ngày tuổi)';
            } else if (ageInMonths < 120) {
                // Từ 60 tháng trở lên chuyển sang WHO Reference 2007.
                // Cân nặng theo tuổi chỉ có chuẩn tới 120 tháng nên phải nói rõ
                // chỉ số nào sẽ được đánh giá ở từng mốc tuổi.
                ageGroup = 'Trẻ 5-9 tuổi';
                ageGroupDetail = 'Giai đoạn tiền dậy thì';
                standardTable = 'WHO 2007 (5-19 tuổi)';
                standardTableDetail = 'Đánh giá: CC/T, BMI/T, CN/T';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'BMI for Age là chỉ số chính';
            } else if (ageInMonths < 180) {
                ageGroup = 'Trẻ 10-14 tuổi';
                ageGroupDetail = 'Giai đoạn dậy thì';
                standardTable = 'WHO 2007 (5-19 tuổi)';
                standardTableDetail = 'Đánh giá: CC/T, BMI/T (WHO không có CN/T sau 10 tuổi)';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'BMI for Age là chỉ số chính';
            } else if (ageInMonths < 229) {
                ageGroup = 'Vị thành niên 15-19 tuổi';
                ageGroupDetail = 'Giai đoạn hoàn thiện tăng trưởng';
                standardTable = 'WHO 2007 (5-19 tuổi)';
                standardTableDetail = 'Đánh giá: CC/T, BMI/T (WHO không có CN/T sau 10 tuổi)';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'BMI for Age là chỉ số chính';
            } else {
                ageGroup = 'Từ 19 tuổi trở lên';
                ageGroupDetail = 'Ngoài phạm vi chuẩn tăng trưởng của WHO';
                standardTable = 'Không áp dụng';
                standardTableDetail = 'Dùng phân loại BMI cho người trưởng thành';
                measurementMethod = 'Chiều cao đứng';
                measurementMethodDetail = 'BMI người lớn';
            }
            
            // Cập nhật giao diện
            document.getElementById('calculation-method-info').textContent =
                ageInMonths < 60 ? 'WHO 2006 — LMS' : (ageInMonths < 229 ? 'WHO 2007 — LMS' : 'Không áp dụng chuẩn WHO trẻ em');
            document.getElementById('age-group-info').textContent = ageGroup;
            document.getElementById('age-group-detail').textContent = ageGroupDetail;
            document.getElementById('standard-table-info').textContent = standardTable;
            document.getElementById('standard-table-detail').textContent = standardTableDetail;
            document.getElementById('measurement-method-info').textContent = measurementMethod;
            document.getElementById('measurement-method-detail').textContent = measurementMethodDetail;
            
            // Cập nhật màu sắc theo nhóm tuổi
            const ageGroupCard = document.querySelector('.age-group-card .info-icon');
            if (ageInWeeks <= 13) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #ff6b6b, #ff8e53)';
            } else if (duoiMocNam) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #4ecdc4, #44a08d)';
            } else if (ageInMonths < 60) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #667eea, #764ba2)';
            } else if (ageInMonths < 229) {
                ageGroupCard.style.background = 'linear-gradient(45deg, #11998e, #38ef7d)';
            } else {
                ageGroupCard.style.background = 'linear-gradient(45deg, #f093fb, #f5576c)';
            }
        }
    </script>
    
    <!-- CSS cho Classification Panel -->
    <style>
        .classification-info-panel .card-body {
            padding: 1.5rem;
        }
        
        .classification-display {
            display: grid;
            /* Tự dàn 1-4 cột theo độ rộng thẻ (thẻ rộng cả hàng ở biểu mẫu 0-5 tuổi) */
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 16rem), 1fr));
            gap: 1rem;
        }
        .classification-display .info-content { min-width: 0; overflow-wrap: anywhere; }
        
        .info-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 1.1em;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #495057;
            font-size: 0.9em;
        }
        
        .info-value {
            font-weight: bold;
            margin-bottom: 0.25rem;
            color: #212529;
            font-size: 1em;
        }
        
        .info-detail {
            color: #6c757d;
            font-size: 0.8em;
            line-height: 1.3;
        }
        
        .guide-link-wrapper {
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .guide-link {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .guide-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .guide-link i {
            margin-right: 0.5rem;
        }
        
        /* Age Group Specific Colors */
        .age-group-card .info-icon {
            background: linear-gradient(45deg, #6c757d, #495057);
        }
        
        .standard-table-card .info-icon {
            background: linear-gradient(45deg, #17a2b8, #138496);
        }
        
        .measurement-method-card .info-icon {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }
        
        .calculation-method-card .info-icon {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
    </style>
@endpush

