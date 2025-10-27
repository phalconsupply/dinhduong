<?php

namespace App\Libs;

use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromCollection, WithHeadings, WithStyles
{
    protected $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $user = Auth::user();
        $query = History::query()->byUserRole($user);

        if (!empty($this->request['from_date'])) {
            $query->whereDate('created_at', '>=', $this->request['from_date']);
        }

        if (!empty($this->request['to_date'])) {
            $query->whereDate('created_at', '<=', $this->request['to_date']);
        }

        if (!empty($this->request['province_code'])) {
            $query->where('province_code', $this->request['province_code']);
        }

        if (!empty($this->request['district_code'])) {
            $query->where('district_code', $this->request['district_code']);
        }

        if (!empty($this->request['ward_code'])) {
            $query->where('ward_code', $this->request['ward_code']);
        }

        $histories = $query->get();

        return $histories->map(function ($item) {
            return [
                $item->id,
                $item->fullname,
                $item->phone,
                $item->id_number,
                $item->height,
                $item->weight,
                $item->bmi,
                $item->cal_date_f() ?? '',
                $item->birthday_f() ?? '',
                $item->check_weight_for_age()['text'] ?? '',
                $item->check_height_for_age()['text'] ?? '',
                $item->check_weight_for_height()['text'] ?? '',
                $item->nutrition_status ?? 'Chưa xác định',
                v("gender.{$item->gender}"),
                $item->get_age(),
                optional($item->ethnic)->name,
                $item->address,
                optional($item->ward)->full_name,
                optional($item->district)->full_name,
                optional($item->province)->full_name,
                optional($item->creator)->name ?? 'Khách vãng lai',
                optional(optional($item->creator)->unit)->name,
                $item->created_at?->format('d-m-Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Họ tên',
            'Điện thoại',
            'CCCD',
            'Chiều cao (cm)',
            'Cân nặng (kg)',
            'BMI',
            'Ngày cân',
            'Ngày sinh',
            'Cân nặng theo tuổi',
            'Chiều cao theo tuổi',
            'Cân nặng theo chiều cao',
            'Trạng thái',
            'Giới tính',
            'Tuổi',
            'Dân tộc',
            'Địa chỉ',
            'Phường/Xã',
            'Quận/Huyện',
            'Tỉnh/Thành',
            'Người lập',
            'Đơn vị',
            'Ngày lập',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Tự động wrap text nếu cần
        $sheet->getStyle('A1:W1000')->getAlignment()->setWrapText(true);

        // Đặt chiều cao dòng
        foreach (range(1, 1000) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }

        // Cài đặt chiều rộng cho các cột nếu cần
        foreach (range('A', 'W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}

