
@if (\Session::has('success'))
    <div class="alert-top alert alert-success alert-dismissible show" role="alert">
        {!! \Session::get('success') !!}
    </div>
@endif
@if (\Session::has('warning'))
    <div class="alert-top alert alert-warning alert-dismissible show" role="alert">
        {!! \Session::get('warning') !!}
    </div>
@endif
@if($errors->any())
    @php
        // Trường → id ô nhập để mỗi lỗi trong phần tóm tắt nhảy thẳng tới trường đó.
        // age/realAge là giá trị dẫn xuất từ ngày sinh nên dẫn về ô ngày sinh.
        $oLoi = [
            'fullname' => 'last-name', 'phone' => 'phone', 'gender' => 'gender', 'ethnic_id' => 'ethnic_id',
            'cal_date' => 'cal-date', 'birthday' => 'calendar-birth', 'age' => 'calendar-birth', 'realAge' => 'calendar-birth',
            'address' => 'address', 'province_code' => 'province_code', 'ward_code' => 'ward_code',
            'weight' => 'weight-user-profile', 'height' => 'length-user-profile', 'thumb' => 'avatar-wapper',
        ];
        // Mỗi ô chỉ một lỗi; lỗi của chính ngày sinh ưu tiên hơn lỗi tuổi dẫn xuất từ nó.
        $mucLoi = [];
        foreach ($errors->messages() as $truong => $thongBao) {
            $dich = $oLoi[$truong] ?? $truong;
            if (!isset($mucLoi[$dich]) || $truong === 'birthday') {
                $mucLoi[$dich] = ['truong' => $truong, 'co_o' => isset($oLoi[$truong]), 'loi' => $thongBao[0]];
            }
        }
    @endphp
    <div class="alert-top alert alert-danger alert-dismissible show" role="alert" id="tom-tat-loi" tabindex="-1">
        <strong>Vui lòng kiểm tra lại {{ count($mucLoi) }} mục:</strong>
        <ul class="danh-sach-loi">
            @foreach($mucLoi as $dich => $muc)
                <li>
                    @if($muc['co_o'])
                        <a href="#{{ $dich }}">{{ $muc['loi'] }}</a>
                    @else
                        {{ $muc['loi'] }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    {{-- Lỗi theo trường cho script ở form.blade.php gắn thông báo ngay dưới từng ô --}}
    <script type="application/json" id="loi-theo-truong">@json(collect($mucLoi)->mapWithKeys(fn ($m) => [$m['truong'] => $m['loi']]))</script>
@endif
