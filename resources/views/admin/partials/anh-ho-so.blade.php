{{-- Ảnh hồ sơ cân đo — dẫn tới trang kết quả của chính phiếu này (giống mục "Xem kết quả") --}}
<a href="{{ route('result', ['uid' => $m->uid]) }}" target="_blank" title="Xem kết quả của {{ $m->fullname }}">
    <img src="{{ $m->thumb ?: v('user.avatar') }}" class="img-thumbnail" width="80" alt="Ảnh {{ $m->fullname }}">
</a>
