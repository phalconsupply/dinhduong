{{-- Ngày cân và ngày sinh — có nhãn trên từng dòng, đúng thứ tự tiêu đề cột --}}
<span class="small"><span class="text-muted">Cân:</span> {{ $m->cal_date_f() ?? '#' }}</span><br>
<span class="small"><span class="text-muted">Sinh:</span> {{ $m->birthday_f() ?? '#' }}</span>
