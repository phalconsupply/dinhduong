{{-- Kết quả từng chỉ số của một hồ sơ, giống hệt trang kết quả (History::ketQuaChiSo()) --}}
@forelse($m->ketQuaChiSo() as $chiSo)
    <span class="small" style="background-color: {{ $chiSo['color'] }}">{{ $chiSo['label'] }}: {{ $chiSo['text'] }}</span>@if(!$loop->last)<br>@endif
@empty
    <span class="small">#</span>
@endforelse
