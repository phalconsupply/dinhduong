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
                            <h5>Thiết lập lời khuyên</h5>
                            <form method="POST" action="{{ route('admin.setting.update_advices') }}">
                                @csrf
                                <div class="col-md-12">
                                    <h5><strong>Lời khuyên: Cân nặng theo tuổi</strong></h5>
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
                                        $advices = json_decode($setting['advices'], true)

                                    @endphp
                                    @foreach ($items as $resultKey => $label)
                                        <div class="mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <div class="form-icon position-relative">
                                                <i class="ti ti-text-wrap fea icon-sm icons"></i>
                                                <textarea name="advices[{{ $key }}][{{ $resultKey }}]" class="form-control ps-5" rows="3">{{ old("advices.$key.$resultKey.content", $advices[$key][$resultKey] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                {{-- Tương tự cho weight_for_height --}}
                                <div class="col-md-12">
                                    <h5><strong>Lời khuyên: Cân nặng theo chiều cao</strong></h5>
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
                                                <textarea name="advices[{{ $key }}][{{ $resultKey }}]" class="form-control ps-5" rows="3">{{ old("advices.$key.$resultKey.content", $advices[$key][$resultKey] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                {{-- Tương tự cho height_for_age --}}
                                <div class="col-md-12">
                                    <h5><strong>Lời khuyên: Chiều cao theo tuổi</strong></h5>
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
                                                <textarea name="advices[{{ $key }}][{{ $resultKey }}]" class="form-control ps-5" rows="3">{{ old("advices.$key.$resultKey.content", $advices[$key][$resultKey] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                <button type="submit" class="btn btn-sm btn-primary">Lưu tất cả lời khuyên</button>
                            </form>

                        </div><!--end col-->
                    </div>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end container-->
    </section>
@endsection
