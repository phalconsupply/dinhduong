@extends('layouts.app')
@section('content')
    <section id="nuti-nutritional-status">
        <div class="container">
            <div class="row">
                @include('layouts.siderbar')
                <div class="col-xs-12 col-sm-6 col-md-7 col-lg-8">
                    <div class="tab">
                        <div id="tab-1" class="nutrition-menu-content">
                            <div class="heading-wrapper d-flex justify-between align-center mb-3">
                                <h3 class="heading mb-0">Kết quả đánh giá tình trạng dinh dưỡng</h3>
                                <div class="action-buttons">
                                    <a class="btn btn-primary btn-outline-primary me-2" href="{{ url('/in?uid=' . $row->uid) }}" target="_blank"><i class="bi bi-printer"></i> In</a>
                                    @if(Auth::check())
                                    <a class="btn btn-warning btn-outline-secondary" href="{{ url('/' . $row->slug."?edit=".$row->uid) }}"><i class="bi bi-pencil-square"></i> Chỉnh sửa</a>
                                    @endif
                                </div>
                            </div>

                            <form class="pro5-form">
                                <div class="pro5-avatar">
                                    @php
                                        $colorClass = ''; // Mặc định là 'orange'

                                        // Xác định lớp màu dựa trên giá trị của slug
                                        if ($row->slug == 'tu-0-5-tuoi') {
                                            $colorClass = 'orange'; // Ví dụ, nếu slug là 'tu-0-5-tuoi', màu sẽ là 'blue'
                                        } elseif ($row->slug == 'tu-5-19-tuoi') {
                                            $colorClass = 'pink'; // Ví dụ, nếu slug là 'tu-5-19-tuoi', màu sẽ là 'green'
                                        } elseif ($row->slug == 'tu-19-tuoi') {
                                            $colorClass = 'yellow'; // Ví dụ, nếu slug là 'tu-19-tuoi', màu sẽ là 'red'
                                        }
                                    @endphp
                                    <div id="avatar-wapper" class="{{ $colorClass }}" style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                        <img id="avatar-preview" src="{{ $row->thumb ?? asset('/web/frontend/images/ava01.png') }}"  alt="Avatar" style="max-width: 100%; max-height: 100%; display: block;">
                                    </div>
                                </div>

                                <div class="pro5-info">
                                    <div>
                                        <p>Họ và Tên</p>
                                        <p>{{$row->fullname}}</p>
                                    </div>
                                    <div>
                                        <p>Giới tính</p>
                                        <p>{{$row->get_gender()}}</p>
                                    </div>
                                    <div>
                                        <p>Ngày sinh</p>
                                        <p>{{$row->birthday ? $row->birthday->format('d/m/Y') : '' }}</p>
                                    </div>
                                    <div>
                                        <p>Dân tộc</p>
                                        <p>{{$row->ethnic->name}}</p>
                                    </div>
                                    <div>
                                        <p>Số tháng tuổi</p>
                                        <p>{{$row->age}} tháng</p>
                                    </div>
                                    <div>
                                        <p>Cân nặng</p>
                                        <p>{{$row->weight}} kg<br>
                                            <span>Chuẩn cân nặng theo tuổi : {{$row->WeightForAge()['Median'] ?? 'Chưa có dữ liệu'}} kg</span>
                                            <br><span>Chuẩn cân nặng theo chiều cao hiện có: {{$row->WeightForHeight()['Median'] ?? 'Chưa có dữ liệu'}} kg</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p>Chiều cao</p>
                                        <p>{{$row->height}} cm<br>
                                            <span>Chuẩn chiều cao theo tuổi: {{$row->HeightForAge()['Median'] ?? 'Chưa có dữ liệu'}} cm</span>
                                        </p>
                                    </div>
                                </div>
                            </form>
                            @if($row->slug == '' || $row->slug == 'tu-0-5-tuoi')
                                @php
                                    $weight_for_age = $row->check_weight_for_age();
                                    $height_for_age = $row->check_height_for_age();
                                    $weight_for_height = $row->check_weight_for_height();
                                    $bmi_for_age = $row->check_bmi_for_age();
                                    
                                    // Kiểm tra nếu cả 4 chỉ số đều bình thường
                                    $all_normal = ($weight_for_age['result'] == 'normal' && 
                                                   $height_for_age['result'] == 'normal' && 
                                                   $weight_for_height['result'] == 'normal' &&
                                                   $bmi_for_age['result'] == 'normal');
                                @endphp
                                
                                <table style="width: 100%; margin-top: 15px;">
                                    <thead>
                                    <th colspan="2" style="text-align: center; background: #2daab8; color: white">Đánh giá chung</th>
                                    </thead>
                                    <tbody>
                                    @if($all_normal)
                                        <!-- Nếu cả 4 chỉ số đều bình thường, gộp thành 1 dòng -->
                                        <tr style="background-color: green">
                                            <td colspan="2" style="text-align: center; font-weight: bold;">Trẻ bình thường</td>
                                        </tr>
                                    @else
                                        <!-- Hiển thị chi tiết từng chỉ số nếu có bất thường -->
                                        <tr style="background-color: {{$weight_for_age['color']}}">
                                            <td>Cân nặng theo tuổi</td>
                                            <td>{{$weight_for_age['text']}}</td>
                                        </tr>
                                        <tr style="background-color: {{$height_for_age['color']}}">
                                            <td>Chiều cao theo tuổi</td>
                                            <td>{{$height_for_age['text']}}</td>
                                        </tr>
                                        <tr style="background-color: {{$weight_for_height['color']}}">
                                            <td>Cân nặng theo chiều cao</td>
                                            <td>{{$weight_for_height['text']}}</td>
                                        </tr>
                                        <tr style="background-color: {{$bmi_for_age['color']}}">
                                            <td>BMI theo tuổi</td>
                                            <td>{{$bmi_for_age['text']}}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            @else
                                <table style="width: 100%">
                                    <thead>
                                    <th style="text-align: center; background: #418c39; color: white">Đánh giá chung</th>
                                    </thead>
                                    <tbody>
                                    <tr style="text-align: center; background-color: {{$row->check_bmi_for_age()['color']}}">
                                        <td>{{$row->check_bmi_for_age()['text']}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif

                            <h4 class="small-title">Lời khuyên @if(Auth::check())<button id="edit_advices" class="btn btn-sm">Sửa lời khuyên</button>@endif</h4>
                            <!-- Nội dung hiển thị HTML -->
                            <div class="nuti-recommendations">
                                @php
                                    $advices = json_decode($setting['advices'], true);
                                    $ageGroup = $row->getAgeGroupKey(); // Get age group: '0-5', '6-11', etc.

                                    $default_advice = '<ul>';
                                    
                                    // Get advice for specific age group, fallback to old structure if not found
                                    $waResult = $row->check_weight_for_age()['result'];
                                    $waAdvice = $advices[$ageGroup]['weight_for_age'][$waResult] 
                                             ?? $advices['weight_for_age'][$waResult] 
                                             ?? '';
                                    
                                    $whResult = $row->check_weight_for_height()['result'];
                                    $whAdvice = $advices[$ageGroup]['weight_for_height'][$whResult] 
                                             ?? $advices['weight_for_height'][$whResult] 
                                             ?? '';
                                    
                                    $haResult = $row->check_height_for_age()['result'];
                                    $haAdvice = $advices[$ageGroup]['height_for_age'][$haResult] 
                                             ?? $advices['height_for_age'][$haResult] 
                                             ?? '';
                                    
                                    if ($waAdvice) $default_advice .= '<li>' . $waAdvice . '</li>';
                                    if ($whAdvice) $default_advice .= '<li>' . $whAdvice . '</li>';
                                    if ($haAdvice) $default_advice .= '<li>' . $haAdvice . '</li>';
                                    
                                    $default_advice .= '</ul>';
                                @endphp

                                {!! $default_advice !!}
                                <div id="advices_content" >
                                    {!! $row->advice_content !!}
                                </div>
                            </div>
                            <!-- Form chỉnh sửa lời khuyên (ẩn mặc định) -->
                            <div id="advices_editor" style="display: none;">
                                <div id="advices_textarea" class="form-control" style="height: 200px;">
                                    {!! $row->advice_content !!}
                                </div>
                                <br>
                                <button id="save_advices" class="btn btn-primary btn-sm">Lưu</button>
                                <button id="cancel_edit_advices" class="btn btn-secondary btn-sm">Hủy</button>
                            </div>
                            <p class="amz-contact-expert">
                                <strong>Hãy liên hệ Chuyên gia Dinh dưỡng theo số {{ $setting['phone'] }} để được tư vấn thêm.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
    <style>
        #advices_content ol{margin-left: 15px}
        .heading-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .heading-wrapper .action-buttons a {
            display: inline-block;
            padding: 8px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            text-decoration: none;
            transition: 0.2s;
            margin-left: 8px;
        }

        .heading-wrapper .action-buttons a:hover {
            background-color: #f0f0f0;
        }

    </style>
@endpush

@push('foot')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

@if(Auth::check())
    <script>
        const quill = new Quill('#advices_textarea', {
            theme: 'snow'
        });

        $(document).ready(function() {
            $('#edit_advices').on('click', function() {
                $('#advices_content').hide();
                $('#advices_editor').show();
            });

            $('#cancel_edit_advices').on('click', function() {
                $('#advices_editor').hide();
                $('#advices_content').show();
            });

            $('#save_advices').on('click', function() {
                const updatedContent = quill.root.innerHTML;

                $.ajax({
                    url: '{{ route("admin.history.update_advice") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ $row->id }}',
                        content: updatedContent,
                    },
                    success: function(response) {
                        // Cập nhật giao diện
                        $('#advices_content').html(updatedContent);
                        $('#advices_editor').hide();
                        $('#advices_content').show();
                    },
                    error: function() {
                        alert('Đã xảy ra lỗi khi lưu lời khuyên.');
                    }
                });
            });
        });
    </script>

@endif

@endpush
