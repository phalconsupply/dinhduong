@extends('admin.layouts.app-full')
@section('title')
    Cấu hình
@endsection
@section('body_class', 'home')
@section('content')
    @php
        // Cấu hình lời khuyên theo TỪNG ĐỐI TƯỢNG.
        //
        // Khoá kết quả (normal, stunted_severe, ...) phải khớp đúng giá trị mà
        // classifyByZScore() trả về cho 0-5 và classifyByZScore2007() trả về cho
        // 5-19. Sai một khoá là lời khuyên im lặng không hiện ra.
        //
        // Tên trường giữ nguyên dạng advices[nhóm][chỉ số][kết quả] nên dữ liệu
        // đã cấu hình cho 0-5 vẫn dùng được, không phải nhập lại.

        $mucWfa05 = [
            'normal' => 'Trẻ bình thường',
            'underweight_severe' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng',
            'underweight_moderate' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa',
            'obese' => 'Trẻ béo phì',
            'overweight' => 'Trẻ thừa cân',
            'unknown' => 'Không xác định',
        ];
        $mucWfh05 = [
            'normal' => 'Trẻ bình thường',
            'underweight_severe' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng',
            'underweight_moderate' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa',
            'obese' => 'Trẻ béo phì',
            'overweight' => 'Trẻ thừa cân',
            'unknown' => 'Không xác định',
        ];
        $mucHfa05 = [
            'normal' => 'Trẻ bình thường',
            'stunted_severe' => 'Trẻ suy dinh dưỡng thể còi, mức độ nặng',
            'stunted_moderate' => 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa',
            'above_2sd' => 'Trẻ cao hơn so với tuổi',
            'above_3sd' => 'Trẻ rất cao',
            'unknown' => 'Không xác định',
        ];

        // Ngưỡng của 5-19 khác 0-5: BMI thừa cân từ +1SD, béo phì từ +2SD
        $mucHfa519 = [
            'normal' => 'Chiều cao bình thường',
            'stunted_severe' => 'Thấp còi, mức độ nặng',
            'stunted_moderate' => 'Thấp còi',
            'above_2sd' => 'Cao hơn bình thường',
            'above_3sd' => 'Cao vượt trội',
            'unknown' => 'Không xác định',
        ];
        $mucBmi519 = [
            'normal' => 'Bình thường (-2SD đến +1SD)',
            'wasted_severe' => 'Gầy còm, mức độ nặng (< -3SD)',
            'wasted_moderate' => 'Gầy còm (-3SD đến -2SD)',
            'overweight' => 'Thừa cân (> +1SD)',
            'obese' => 'Béo phì (> +2SD)',
            'unknown' => 'Không xác định',
        ];
        $mucWfa519 = [
            'normal' => 'Cân nặng theo tuổi bình thường',
            'underweight_severe' => 'Nhẹ cân, mức độ nặng',
            'underweight_moderate' => 'Nhẹ cân',
            'unknown' => 'Không xác định',
        ];

        $doiTuongs = [
            '0-5' => [
                'nhan' => 'Trẻ 0 - 5 tuổi',
                'mota' => 'Chuẩn WHO Child Growth Standards 2006. Cấu hình theo nhóm tháng tuổi.',
                'nhomTuoi' => [
                    '0-5' => '0-5 tháng', '6-11' => '6-11 tháng', '12-23' => '12-23 tháng',
                    '24-35' => '24-35 tháng', '36-47' => '36-47 tháng', '48-59' => '48-59 tháng',
                ],
                'chiSo' => [
                    'weight_for_age' => ['nhan' => 'Cân nặng theo tuổi (W/A)', 'muc' => $mucWfa05],
                    'weight_for_height' => ['nhan' => 'Cân nặng theo chiều cao (W/H)', 'muc' => $mucWfh05],
                    'height_for_age' => ['nhan' => 'Chiều cao theo tuổi (H/A)', 'muc' => $mucHfa05],
                ],
            ],
            '5-19' => [
                'nhan' => 'Trẻ 5 - 19 tuổi',
                'mota' => 'Chuẩn WHO Reference 2007. BMI theo tuổi là chỉ số chính; WHO không có chỉ số cân nặng theo chiều cao cho lứa tuổi này.',
                'nhomTuoi' => [
                    '5-9' => '5-9 tuổi', '10-14' => '10-14 tuổi', '15-19' => '15-19 tuổi',
                ],
                'chiSo' => [
                    'height_for_age' => ['nhan' => 'Chiều cao theo tuổi (H/A)', 'muc' => $mucHfa519],
                    'bmi_for_age' => ['nhan' => 'BMI theo tuổi (BMI/A)', 'muc' => $mucBmi519],
                    // WHO chỉ cấp chuẩn cân nặng theo tuổi tới 10 tuổi
                    'weight_for_age' => ['nhan' => 'Cân nặng theo tuổi (W/A)', 'muc' => $mucWfa519, 'chiNhom' => ['5-9']],
                ],
            ],
        ];

        $advices = json_decode($setting['advices'] ?? '{}', true) ?: [];
    @endphp

    <section class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                    @include('admin.setting.sidebar')
                </div><!--end col-->

                <div class=" col-lg-9 col-12">

                    <div class="card border-bottom pb-4">
                        <div class="card-body">
                            <h5>Thiết lập lời khuyên theo nhóm tuổi</h5>
                            <p class="text-muted mb-3">
                                Chọn đối tượng, sau đó chọn nhóm tuổi để nhập lời khuyên cho từng kết quả đánh giá.
                            </p>

                            <form method="POST" action="{{ route('admin.setting.update_advices') }}">
                                @csrf

                                {{-- Cấp 1: chọn đối tượng --}}
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    @foreach($doiTuongs as $dtKey => $dt)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#dt-{{ $dtKey }}"
                                                    type="button" role="tab">
                                                <strong>{{ $dt['nhan'] }}</strong>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($doiTuongs as $dtKey => $dt)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                             id="dt-{{ $dtKey }}" role="tabpanel">

                                            <p class="text-muted small">{{ $dt['mota'] }}</p>

                                            {{-- Cấp 2: chọn nhóm tuổi --}}
                                            <ul class="nav nav-pills mb-3" role="tablist">
                                                @foreach($dt['nhomTuoi'] as $groupKey => $groupLabel)
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                                data-bs-toggle="pill"
                                                                data-bs-target="#age-{{ $dtKey }}-{{ $groupKey }}"
                                                                type="button" role="tab">
                                                            {{ $groupLabel }}
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>

                                            <div class="tab-content">
                                                @foreach($dt['nhomTuoi'] as $groupKey => $groupLabel)
                                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                         id="age-{{ $dtKey }}-{{ $groupKey }}" role="tabpanel">

                                                        <div class="alert alert-info">
                                                            <i class="uil uil-info-circle"></i>
                                                            Đang cấu hình lời khuyên cho <strong>{{ $groupLabel }}</strong>
                                                            ({{ $dt['nhan'] }})
                                                        </div>

                                                        @foreach($dt['chiSo'] as $key => $chiSo)
                                                            @php
                                                                // Một số chỉ số chỉ áp dụng cho vài nhóm tuổi
                                                                $apDung = !isset($chiSo['chiNhom'])
                                                                          || in_array($groupKey, $chiSo['chiNhom'], true);
                                                            @endphp

                                                            @if($apDung)
                                                                <div class="col-md-12 mb-4">
                                                                    <h5><strong>Lời khuyên: {{ $chiSo['nhan'] }}</strong></h5>
                                                                    @foreach ($chiSo['muc'] as $resultKey => $label)
                                                                        <div class="mb-3">
                                                                            <label class="form-label">{{ $label }}</label>
                                                                            <div class="form-icon position-relative">
                                                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                                                <textarea name="advices[{{ $groupKey }}][{{ $key }}][{{ $resultKey }}]"
                                                                                          class="form-control ps-5"
                                                                                          rows="3"
                                                                                          placeholder="Nhập lời khuyên cho: {{ $label }} — {{ $groupLabel }}">{{ old("advices.$groupKey.$key.$resultKey", $advices[$groupKey][$key][$resultKey] ?? '') }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="col-md-12 mb-4">
                                                                    <h5 class="text-muted"><strong>{{ $chiSo['nhan'] }}</strong></h5>
                                                                    <div class="alert alert-secondary py-2 mb-0">
                                                                        <small>
                                                                            <i class="uil uil-info-circle"></i>
                                                                            WHO không cung cấp chuẩn cho chỉ số này ở nhóm
                                                                            <strong>{{ $groupLabel }}</strong>, nên không cần cấu hình.
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach

                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="uil uil-save"></i> Lưu tất cả lời khuyên
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                                        <i class="uil uil-redo"></i> Làm mới
                                    </button>
                                </div>
                            </form>

                        </div><!--end col-->
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section>
@endsection
