{{-- Địa bàn 2026 của một bản ghi (hồ sơ / đơn vị / tài khoản).
     Bản ghi cũ chưa xác định được xã mới thì hiện địa bàn cũ kèm nhãn. --}}
@if($m->ward_code_2026)
    <span class="small">{{ $m->ward->full_name ?? '#' }}</span><br>
    <span class="small">{{ $m->province->full_name ?? '#' }}</span>
@elseif($m->can_chon_xa_moi)
    <span class="small text-warning" title="Chưa chuyển sang địa bàn 2026 — cần chọn lại xã">{{ $m->dia_ban_cu ?: '#' }}</span><br>
    <span class="badge bg-soft-warning text-warning">Địa bàn cũ</span>
@else
    <span class="small">#</span>
@endif
