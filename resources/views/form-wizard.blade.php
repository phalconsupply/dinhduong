@extends('layouts.app')
@section('content')
    <!-- Tailwind Wizard Form Container -->
    <div class="bg-light p-4 sm:p-8 lg:p-12">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-xl p-6 sm:p-10">
            
            <!-- Header -->
            <header class="text-center mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-primary mb-2">ĐÁNH GIÁ DINH DƯỠNG TRẺ 0-5 TUỔI</h1>
                <p class="text-gray-600 text-sm sm:text-base">Hệ thống hỗ trợ nhập liệu và đánh giá tình trạng dinh dưỡng của trẻ.</p>
            </header>

            <!-- Form Wizard: Progress Bar -->
            <div class="mb-10 flex justify-between items-center relative">
                <!-- Line connector -->
                <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-gray-200 -z-10 mx-10">
                    <div id="progress-line" class="absolute h-full bg-primary transition-all duration-500" style="width: 0%;"></div>
                </div>

                <!-- Step 1: Thông tin cơ bản -->
                <div id="step-1-indicator" class="step-item active text-center flex-1 z-10">
                    <div class="step-dot w-10 h-10 mx-auto rounded-full border-2 border-primary bg-white flex items-center justify-center font-bold transition-all duration-300">
                        <span class="text-xl">1</span>
                    </div>
                    <p class="text-xs sm:text-sm mt-2 font-medium text-gray-700">Thông tin cơ bản</p>
                </div>

                <!-- Step 2: Chỉ số sức khỏe -->
                <div id="step-2-indicator" class="step-item text-center flex-1 z-10">
                    <div class="step-dot w-10 h-10 mx-auto rounded-full border-2 border-gray-400 bg-white flex items-center justify-center font-bold transition-all duration-300 text-gray-400">
                        <span class="text-xl">2</span>
                    </div>
                    <p class="text-xs sm:text-sm mt-2 font-medium text-gray-500">Chỉ số sức khỏe</p>
                </div>

                <!-- Step 3: Kết quả -->
                <div id="step-3-indicator" class="step-item text-center flex-1 z-10">
                    <div class="step-dot w-10 h-10 mx-auto rounded-full border-2 border-gray-400 bg-white flex items-center justify-center font-bold transition-all duration-300 text-gray-400">
                        <span class="text-xl">3</span>
                    </div>
                    <p class="text-xs sm:text-sm mt-2 font-medium text-gray-500">Xem kết quả</p>
                </div>
            </div>

            @include('layouts.alert')

            <!-- Form Content -->
            <form id="nutrition-form" action="{{ route('form.post', ['slug' => $slug]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Step 1 Content: Thông tin cơ bản & Địa chỉ -->
                <div id="step-1-content" class="step-content">

                    <!-- Card: Thông tin cá nhân -->
                    <div class="wizard-card">
                        <div class="wizard-card-header">
                            <i data-lucide="user-round" class="wizard-card-icon"></i>
                            <h2 class="wizard-card-title">1. THÔNG TIN CÁ NHÂN</h2>
                        </div>

                        <div class="input-grid">
                            <div class="floating-label-group">
                                <input type="text" id="fullname" name="fullname" value="{{old('fullname', $item->fullname)}}" class="form-input" placeholder=" " required>
                                <label for="fullname" class="floating-label">Họ và tên trẻ *</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{old('date_of_birth', $item->date_of_birth)}}" class="form-input" placeholder=" " required>
                                <label for="date_of_birth" class="floating-label">Ngày sinh *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="gender" name="gender" class="form-input appearance-none" required>
                                    <option value="">Chọn giới tính</option>
                                    <option value="1" @if(old('gender', $item->gender) == 1) selected @endif>Nam</option>
                                    <option value="0" @if(old('gender', $item->gender) == 0) selected @endif>Nữ</option>
                                </select>
                                <label for="gender" class="floating-label">Giới tính *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="ethnic_id" name="ethnic_id" class="form-input appearance-none" required>
                                    <option value="">Chọn dân tộc</option>
                                    @foreach($ethnics as $ethnic)
                                        <option value="{{ $ethnic->id }}" @if(old('ethnic_id', $item->ethnic_id) == $ethnic->id) selected @endif>{{ $ethnic->name }}</option>
                                    @endforeach
                                </select>
                                <label for="ethnic_id" class="floating-label">Dân tộc *</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="tel" id="phone" name="phone" value="{{old('phone', $item->phone)}}" class="form-input" placeholder=" ">
                                <label for="phone" class="floating-label">SĐT người chăm sóc</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="text" id="id_number" name="id_number" value="{{old('id_number', $item->id_number)}}" class="form-input" placeholder=" ">
                                <label for="id_number" class="floating-label">Mã định danh/CCCD</label>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Địa chỉ -->
                    <div class="wizard-card">
                        <div class="wizard-card-header">
                            <i data-lucide="map-pin" class="wizard-card-icon"></i>
                            <h2 class="wizard-card-title">2. ĐỊA CHỈ</h2>
                        </div>

                        <div class="input-grid">
                            <div class="floating-label-group col-span-full">
                                <input type="text" id="address" name="address" value="{{old('address', $item->address)}}" class="form-input" placeholder=" " required>
                                <label for="address" class="floating-label">Địa chỉ cụ thể (Số nhà, đường...) *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="province_code" name="province_code" class="form-input appearance-none" required>
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->code }}" @if(old('province_code', $item->province_code) == $province->code) selected @endif>{{ $province->name }}</option>
                                    @endforeach
                                </select>
                                <label for="province_code" class="floating-label">Tỉnh/Thành phố *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="district_code" name="district_code" class="form-input appearance-none" required>
                                    <option value="">Chọn Quận/Huyện</option>
                                    @foreach(session('districts', []) as $district)
                                        <option value="{{ $district->code }}" @if(old('district_code', $item->district_code) == $district->code) selected @endif>{{ $district->name }}</option>
                                    @endforeach
                                </select>
                                <label for="district_code" class="floating-label">Quận/Huyện *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="ward_code" name="ward_code" class="form-input appearance-none" required>
                                    <option value="">Chọn Phường/Xã</option>
                                    @foreach(session('wards', []) as $ward)
                                        <option value="{{ $ward->code }}" @if(old('ward_code', $item->ward_code) == $ward->code) selected @endif>{{ $ward->name }}</option>
                                    @endforeach
                                </select>
                                <label for="ward_code" class="floating-label">Phường/Xã *</label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-end mt-8">
                        <button type="button" onclick="nextStep(1)" class="btn-wizard btn-wizard-primary">
                            Tiếp tục
                            <i data-lucide="chevron-right" class="w-5 h-5 ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2 Content: Chỉ số sức khỏe & Thông tin lúc sinh -->
                <div id="step-2-content" class="step-content hidden">

                    <!-- Card: Chỉ số sức khỏe hiện tại -->
                    <div class="wizard-card">
                        <div class="wizard-card-header">
                            <i data-lucide="heart-pulse" class="wizard-card-icon"></i>
                            <h2 class="wizard-card-title">3. CHỈ SỐ SỨC KHỎE HIỆN TẠI</h2>
                        </div>

                        <div class="input-grid">
                            <div class="floating-label-group">
                                <input type="number" step="0.1" min="0" id="weight" name="weight" value="{{old('weight', $item->weight)}}" class="form-input" placeholder=" " required>
                                <label for="weight" class="floating-label">Cân nặng (kg) *</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="number" step="0.1" min="0" id="height" name="height" value="{{old('height', $item->height)}}" class="form-input" placeholder=" " required>
                                <label for="height" class="floating-label">Chiều cao (cm) *</label>
                            </div>

                            <div class="floating-label-group relative">
                                <input type="text" id="age_show" name="age_show" value="{{old('age_show', $item->age_show)}}" class="form-input" placeholder=" " readonly>
                                <label for="age_show" class="floating-label bg-gray-100">Tuổi (Tự động tính)</label>
                                <span class="absolute right-3 top-3 text-sm text-gray-500">tháng</span>
                            </div>

                            <div class="floating-label-group">
                                <input type="text" id="bmi" name="bmi" value="{{old('bmi', $item->bmi)}}" class="form-input" placeholder=" " readonly>
                                <label for="bmi" class="floating-label bg-gray-100">BMI (Tự động tính)</label>
                            </div>

                            <!-- Hidden fields -->
                            <input type="hidden" name="age" id="age" value="{{old('age', $item->age)}}">
                        </div>
                    </div>

                    <!-- Card: Thông tin lúc sinh -->
                    <div class="wizard-card">
                        <div class="wizard-card-header">
                            <i data-lucide="baby" class="wizard-card-icon"></i>
                            <h2 class="wizard-card-title">4. THÔNG TIN LÚC SINH</h2>
                        </div>

                        <div class="input-grid">
                            <div class="floating-label-group">
                                <input type="number" step="1" min="100" id="birth_weight" name="birth_weight" value="{{old('birth_weight', $item->birth_weight)}}" class="form-input" placeholder=" " required>
                                <label for="birth_weight" class="floating-label">Cân nặng lúc sinh (gram) *</label>
                            </div>

                            <div class="floating-label-group">
                                <select id="gestational_age" name="gestational_age" class="form-input appearance-none" required>
                                    <option value="">Chọn tuổi thai</option>
                                    <option value="Đủ tháng" @if(old('gestational_age', $item->gestational_age) == 'Đủ tháng') selected @endif>Đủ tháng</option>
                                    <option value="Thiếu tháng" @if(old('gestational_age', $item->gestational_age) == 'Thiếu tháng') selected @endif>Thiếu tháng</option>
                                </select>
                                <label for="gestational_age" class="floating-label">Tuổi thai lúc sinh *</label>
                            </div>

                            <div class="floating-label-group">
                                <input type="text" id="birth_weight_category" name="birth_weight_category" value="{{old('birth_weight_category', $item->birth_weight_category)}}" class="form-input" placeholder=" " readonly>
                                <label for="birth_weight_category" class="floating-label bg-gray-100">Phân loại (Tự động)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between mt-8">
                        <button type="button" onclick="prevStep(2)" class="btn-wizard btn-wizard-secondary">
                            <i data-lucide="chevron-left" class="w-5 h-5 mr-2"></i>
                            Quay lại
                        </button>
                        <button type="button" onclick="nextStep(2)" class="btn-wizard btn-wizard-primary">
                            Xem kết quả
                            <i data-lucide="file-text" class="w-5 h-5 ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3 Content: Review & Submit -->
                <div id="step-3-content" class="step-content hidden text-center">
                    <div class="wizard-card max-w-lg mx-auto border-t-4 border-green-500">
                        <i data-lucide="check-circle" class="w-16 h-16 text-green-500 mx-auto mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-800 mb-3">HOÀN TẤT NHẬP LIỆU!</h2>
                        <p class="text-gray-600 mb-6">Bạn đã hoàn thành việc nhập thông tin cho trẻ. Vui lòng kiểm tra lại và nhấn nút dưới đây để xem kết quả đánh giá dinh dưỡng.</p>

                        <div class="review-summary text-left text-sm">
                            <div class="review-item">
                                <span class="review-label">Trẻ:</span>
                                <span id="review_name" class="review-value"></span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Tuổi:</span>
                                <span id="review_age" class="review-value"></span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Cân nặng:</span>
                                <span id="review_weight" class="review-value"></span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">Chiều cao:</span>
                                <span id="review_height" class="review-value"></span>
                            </div>
                            <div class="review-item">
                                <span class="review-label">BMI:</span>
                                <span id="review_bmi" class="review-value"></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 btn-wizard btn-wizard-success py-3">
                            XEM ĐÁNH GIÁ CHI TIẾT
                        </button>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-start mt-8">
                        <button type="button" onclick="prevStep(3)" class="btn-wizard btn-wizard-secondary">
                            <i data-lucide="chevron-left" class="w-5 h-5 mr-2"></i>
                            Quay lại chỉnh sửa
                        </button>
                    </div>
                </div>
            </form>

            <!-- Toast Message -->
            <div id="message-box" class="wizard-message error hidden">
                Vui lòng điền đầy đủ các trường bắt buộc.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentStep = 1;
        const totalSteps = 3;

        // Khởi tạo icons Lucide
        lucide.createIcons();

        // --- Utility Functions ---

        /**
         * Tính tuổi của trẻ theo tháng
         */
        function calculateAgeInMonths(dateString) {
            if (!dateString) return 0;
            const today = new Date();
            const birthDate = new Date(dateString);
            
            let years = today.getFullYear() - birthDate.getFullYear();
            let months = today.getMonth() - birthDate.getMonth();
            
            if (months < 0 || (months === 0 && today.getDate() < birthDate.getDate())) {
                years--;
                months = 12 + months;
            }
            
            const totalMonths = years * 12 + months;
            return totalMonths > 0 ? totalMonths : 0;
        }

        /**
         * Tính chỉ số BMI
         */
        function calculateBMI(weight, height) {
            if (!weight || !height || height === 0) return 0;
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            return parseFloat(bmi.toFixed(2));
        }

        /**
         * Phân loại cân nặng lúc sinh
         */
        function classifyBirthWeight(weight) {
            if (!weight || weight <= 0) return '';
            
            if (weight < 2500) {
                return 'Nhẹ cân (<2500g)';
            } else if (weight >= 2500 && weight <= 4000) {
                return 'Đủ cân (2500-4000g)';
            } else {
                return 'Thừa cân (>4000g)';
            }
        }

        /**
         * Hiển thị thông báo
         */
        function showMessage(message, type = 'error') {
            const msgBox = document.getElementById('message-box');
            msgBox.textContent = message;
            msgBox.classList.remove('hidden', 'error', 'success');
            msgBox.classList.add(type);
            
            setTimeout(() => {
                msgBox.classList.add('hidden');
            }, 3000);
        }

        // --- Wizard Navigation Functions ---

        /**
         * Cập nhật giao diện theo bước hiện tại
         */
        function updateStepUI() {
            const progressLine = document.getElementById('progress-line');
            const progress = (currentStep - 1) / (totalSteps - 1) * 100;
            progressLine.style.width = `${progress}%`;

            for (let i = 1; i <= totalSteps; i++) {
                const content = document.getElementById(`step-${i}-content`);
                const indicator = document.getElementById(`step-${i}-indicator`);
                const dot = indicator.querySelector('.step-dot');
                
                if (i === currentStep) {
                    content.classList.remove('hidden');
                    indicator.classList.add('active');
                    dot.classList.remove('text-gray-400', 'border-gray-400', 'bg-green-500', 'border-green-500');
                    dot.classList.add('bg-primary', 'border-primary', 'text-white');
                } else {
                    content.classList.add('hidden');
                    indicator.classList.remove('active');
                }

                if (i < currentStep) {
                    indicator.classList.add('completed');
                    dot.classList.remove('bg-primary', 'border-primary', 'text-gray-400', 'border-gray-400');
                    dot.classList.add('bg-green-500', 'border-green-500', 'text-white');
                } else if (i > currentStep) {
                    indicator.classList.remove('completed');
                    dot.classList.remove('bg-primary', 'border-primary', 'bg-green-500', 'border-green-500');
                    dot.classList.add('text-gray-400', 'border-gray-400', 'bg-white');
                }
            }

            // Re-initialize Lucide icons
            lucide.createIcons();
        }

        /**
         * Chuyển sang bước tiếp theo
         */
        function nextStep(step) {
            const currentContent = document.getElementById(`step-${step}-content`);
            const requiredInputs = currentContent.querySelectorAll('[required]');
            let allValid = true;

            requiredInputs.forEach(input => {
                if (!input.checkValidity() || input.value.trim() === '') {
                    allValid = false;
                    input.classList.add('error');
                    setTimeout(() => input.classList.remove('error'), 1500);
                }
            });

            if (!allValid) {
                showMessage('Vui lòng điền đầy đủ các trường có đánh dấu (*).', 'error');
                return;
            }

            if (step === 2) {
                updateReviewData();
            }

            if (currentStep < totalSteps) {
                currentStep++;
                updateStepUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        /**
         * Quay lại bước trước
         */
        function prevStep(step) {
            if (currentStep > 1) {
                currentStep--;
                updateStepUI();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        /**
         * Cập nhật các trường tính toán tự động
         */
        function updateCalculatedFields() {
            // Tính tuổi
            const dateOfBirth = document.getElementById('date_of_birth').value;
            const ageInMonths = calculateAgeInMonths(dateOfBirth);
            document.getElementById('age_show').value = ageInMonths > 0 ? ageInMonths : '';
            document.getElementById('age').value = ageInMonths;

            // Tính BMI
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            const bmi = calculateBMI(weight, height);
            document.getElementById('bmi').value = bmi > 0 ? bmi : '';

            // Phân loại cân nặng lúc sinh
            const birthWeight = parseInt(document.getElementById('birth_weight').value);
            const category = classifyBirthWeight(birthWeight);
            document.getElementById('birth_weight_category').value = category;
        }

        /**
         * Cập nhật dữ liệu review
         */
        function updateReviewData() {
            const fullname = document.getElementById('fullname').value;
            const ageShow = document.getElementById('age_show').value;
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;
            const bmi = document.getElementById('bmi').value;

            document.getElementById('review_name').textContent = fullname || 'Chưa nhập';
            document.getElementById('review_age').textContent = ageShow ? `${ageShow} tháng` : '0 tháng';
            document.getElementById('review_weight').textContent = weight ? `${weight} kg` : '0.0 kg';
            document.getElementById('review_height').textContent = height ? `${height} cm` : '0.0 cm';
            document.getElementById('review_bmi').textContent = bmi || '0.0';
        }

        // --- Event Listeners ---
        document.addEventListener('DOMContentLoaded', () => {
            // Auto-calculate fields
            const calculatedFields = ['date_of_birth', 'weight', 'height', 'birth_weight'];
            calculatedFields.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', updateCalculatedFields);
                    element.addEventListener('change', updateCalculatedFields);
                }
            });

            // Initialize UI
            updateStepUI();
            updateCalculatedFields();

            // Province/District/Ward cascade (existing AJAX code)
            $('#province_code').on('change', function() {
                var province_code = $(this).val();
                if (province_code) {
                    $.ajax({
                        url: '{{ route("web.ajax_get_district_by_province") }}',
                        type: 'GET',
                        data: { province_code: province_code },
                        success: function(data) {
                            $('#district_code').html('<option value="">Chọn Quận/Huyện</option>');
                            $.each(data, function(key, value) {
                                $('#district_code').append('<option value="'+ value.code +'">'+ value.name +'</option>');
                            });
                        }
                    });
                }
            });

            $('#district_code').on('change', function() {
                var district_code = $(this).val();
                if (district_code) {
                    $.ajax({
                        url: '{{ route("web.ajax_get_ward_by_district") }}',
                        type: 'GET',
                        data: { district_code: district_code },
                        success: function(data) {
                            $('#ward_code').html('<option value="">Chọn Phường/Xã</option>');
                            $.each(data, function(key, value) {
                                $('#ward_code').append('<option value="'+ value.code +'">'+ value.name +'</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
