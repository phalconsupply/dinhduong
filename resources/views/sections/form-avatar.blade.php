@php
    // Màu khung và nhãn theo từng biểu mẫu
    $khungAnh = [
        'tu-0-5-tuoi'  => ['orange', 'Hồ sơ<br>trẻ từ 0-5 tuổi'],
        'tu-5-19-tuoi' => ['pink', 'Hồ sơ<br>trẻ từ 5-19 tuổi'],
        'tu-19-tuoi'   => ['yellow', 'Hồ sơ<br>trên 19 tuổi'],
    ][$slug] ?? null;
@endphp
@if($khungAnh)
    <div class="pro5-avatar">
        <input type="file" id="avatar-input" name="thumb" accept="image/*" style="display: none;">
        {{-- role=button + tabindex: chọn ảnh được bằng bàn phím (Enter/Space), không chỉ bằng chuột --}}
        <div id="avatar-wapper" class="{{ $khungAnh[0] }}" role="button" tabindex="0" aria-controls="avatar-input"
             aria-label="Chọn ảnh đại diện (không bắt buộc)"
             style="cursor: pointer; border: 1px dashed #ccc; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <img id="avatar-preview" src="{{ $item->thumb != '' ? $item->thumb : asset('/web/frontend/images/ava01.png') }}" alt="Ảnh đại diện"
                 style="max-width: 100%; max-height: 100%; display: block;">
            <i class="icon camera-icon" aria-hidden="true"></i>
            <h4 id="title-name" class="pro5-name desc">{!! $khungAnh[1] !!}</h4>
        </div>
    </div>
@endif
