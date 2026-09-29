{{-- Tình trạng dinh dưỡng tổng hợp, cùng nguồn với trang kết quả (Z-score đã lưu trong phiếu) --}}
@php
    $tinhTrang = $m->get_nutrition_status_auto();
    $lopHuyHieu = [
        '#F44336' => 'bg-danger',   // đỏ — nặng
        '#FF9800' => 'bg-warning',  // cam — vừa
        '#4CAF50' => 'bg-success',  // xanh — bình thường
        '#00BCD4' => 'bg-info',     // xanh ngọc — vượt chuẩn
    ][$tinhTrang['color'] ?? ''] ?? 'bg-secondary';
@endphp
<span class="badge {{ $lopHuyHieu }} text-wrap text-start">{{ $tinhTrang['text'] ?? 'Chưa xác định' }}</span>
