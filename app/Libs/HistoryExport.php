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

        $query->filterDiaBan($this->request);

        $histories = $query->with(['ward', 'province', 'oldWard', 'oldDistrict', 'oldProvince'])->get();

        return $histories->map(function ($item) {
            // Cùng kết quả với trang kết quả; chỉ số không áp dụng cho lứa tuổi để trống
            $chiSo = $item->ketQuaChiSo();
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
                $chiSo['wfa']['text'] ?? '',
                $chiSo['hfa']['text'] ?? '',
                $chiSo['wfh']['text'] ?? '',
                $chiSo['bmi']['text'] ?? '',
                $item->get_nutrition_status_auto()['text'] ?? 'Chưa xác định',
                v("gender.{$item->gender}"),
                $item->get_age(),
                optional($item->ethnic)->name,
                $item->address,
                optional($item->ward)->full_name,
                optional($item->province)->full_name,
                $item->dia_ban_cu,
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
            'BMI theo tuổi',
            'Trạng thái',
            'Giới tính',
            'Tuổi',
            'Dân tộc',
            'Địa chỉ',
            'Phường/Xã',
            'Tỉnh/Thành',
            'Địa bàn cũ (trước 7/2025)',
            'Người lập',
            'Đơn vị',
            'Ngày lập',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Tự động wrap text nếu cần
        $sheet->getStyle('A1:X1000')->getAlignment()->setWrapText(true);

        // Đặt chiều cao dòng
        foreach (range(1, 1000) as $row) {
            $sheet->getRowDimension($row)->setRowHeight(30);
        }

        // Cài đặt chiều rộng cho các cột nếu cần
        foreach (range('A', 'X') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}

