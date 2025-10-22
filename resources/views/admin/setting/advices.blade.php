@extends('admin.layouts.app-full')
@section('title')
    Cấu hình
@endsection
@section('body_class', 'home')
@section('content')
    <section class="container-fluid">
        <div class="layout-specing">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 d-lg-block d-none">
                    @include('admin.setting.sidebar')
                </div><!--end col-->

                <div class=" col-lg-8 col-12">

                    <div class="card border-bottom pb-4">
                        <div class="card-body">
                            <h5>Thiết lập lời khuyên theo nhóm tuổi</h5>
                            <p class="text-muted">Cấu hình lời khuyên riêng cho từng nhóm tuổi: 0-5, 6-11, 12-23, 24-35, 36-47, 48-59 tháng</p>
                            
                            <!-- Age Group Tabs -->
                            <ul class="nav nav-pills mb-3" id="ageGroupTabs" role="tablist">
                                @php
                                    $ageGroups = [
                                        '0-5' => '0-5 tháng',
                                        '6-11' => '6-11 tháng',
                                        '12-23' => '12-23 tháng',
                                        '24-35' => '24-35 tháng',
                                        '36-47' => '36-47 tháng',
                                        '48-59' => '48-59 tháng',
                                    ];
                                @endphp
                                @foreach($ageGroups as $groupKey => $groupLabel)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                                id="age-{{ $groupKey }}-tab" 
                                                data-bs-toggle="pill" 
                                                data-bs-target="#age-{{ $groupKey }}" 
                                                type="button" 
                                                role="tab">
                                            {{ $groupLabel }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <form method="POST" action="{{ route('admin.setting.update_advices') }}">
                                @csrf
                                
                                <div class="tab-content" id="ageGroupTabContent">
                                    @foreach($ageGroups as $groupKey => $groupLabel)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                             id="age-{{ $groupKey }}" 
                                             role="tabpanel">
                                             
                                            <div class="alert alert-info">
                                                <i class="uil uil-info-circle"></i> 
                                                Đang cấu hình lời khuyên cho trẻ <strong>{{ $groupLabel }}</strong>
                                            </div>

                                            <!-- Weight for Age Section -->
                                            <div class="col-md-12 mb-4">
                                                <h5><strong>Lời khuyên: Cân nặng theo tuổi (W/A)</strong></h5>
                                                @php
                                                    $key = 'weight_for_age';
                                                    $items = [
                                                        'normal' => 'Trẻ bình thường',
                                                        'underweight_severe' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ nặng',
                                                        'underweight_moderate' => 'Trẻ suy dinh dưỡng thể nhẹ cân, mức độ vừa',
                                                        'obese' => 'Trẻ béo phì',
                                                        'overweight' => 'Trẻ thừa cân',
                                                        'unknown' => 'Không xác định',
                                                    ];
                                                    $advices = json_decode($setting['advices'], true);
                                                @endphp
                                                @foreach ($items as $resultKey => $label)
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <div class="form-icon position-relative">
                                                            <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                            <textarea name="advices[{{ $groupKey }}][{{ $key }}][{{ $resultKey }}]" 
                                                                      class="form-control ps-5" 
                                                                      rows="3" 
                                                                      placeholder="Nhập lời khuyên cho trẻ {{ $label }} ở độ tuổi {{ $groupLabel }}">{{ old("advices.$groupKey.$key.$resultKey", $advices[$groupKey][$key][$resultKey] ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Weight for Height Section -->
                                            <div class="col-md-12 mb-4">
                                                <h5><strong>Lời khuyên: Cân nặng theo chiều cao (W/H)</strong></h5>
                                                @php
                                                    $key = 'weight_for_height';
                                                    $items = [
                                                        'normal' => 'Trẻ bình thường',
                                                        'underweight_severe' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ nặng',
                                                        'underweight_moderate' => 'Trẻ suy dinh dưỡng thể gầy còm, mức độ vừa',
                                                        'obese' => 'Trẻ béo phì',
                                                        'overweight' => 'Trẻ thừa cân',
                                                        'unknown' => 'Không xác định',
                                                    ];
                                                @endphp
                                                @foreach ($items as $resultKey => $label)
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <div class="form-icon position-relative">
                                                            <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                            <textarea name="advices[{{ $groupKey }}][{{ $key }}][{{ $resultKey }}]" 
                                                                      class="form-control ps-5" 
                                                                      rows="3" 
                                                                      placeholder="Nhập lời khuyên cho trẻ {{ $label }} ở độ tuổi {{ $groupLabel }}">{{ old("advices.$groupKey.$key.$resultKey", $advices[$groupKey][$key][$resultKey] ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Height for Age Section -->
                                            <div class="col-md-12 mb-4">
                                                <h5><strong>Lời khuyên: Chiều cao theo tuổi (H/A)</strong></h5>
                                                @php
                                                    $key = 'height_for_age';
                                                    $items = [
                                                        'normal' => 'Trẻ bình thường',
                                                        'stunted_severe' => 'Trẻ suy dinh dưỡng thể còi, mức độ nặng',
                                                        'stunted_moderate' => 'Trẻ suy dinh dưỡng thể thấp còi, mức độ vừa',
                                                        'above_2sd' => 'Trẻ cao hơn so với tuổi',
                                                        'above_3sd' => 'Trẻ rất cao',
                                                        'unknown' => 'Không xác định',
                                                    ];
                                                @endphp
                                                @foreach ($items as $resultKey => $label)
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <div class="form-icon position-relative">
                                                            <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                            <textarea name="advices[{{ $groupKey }}][{{ $key }}][{{ $resultKey }}]" 
                                                                      class="form-control ps-5" 
                                                                      rows="3" 
                                                                      placeholder="Nhập lời khuyên cho trẻ {{ $label }} ở độ tuổi {{ $groupLabel }}">{{ old("advices.$groupKey.$key.$resultKey", $advices[$groupKey][$key][$resultKey] ?? '') }}</textarea>
                                                        </div>
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
