<?php
// Script: check_age_under_1_month.php
// Mục đích: Liệt kê các record có tuổi nhỏ hơn 1 tháng

use App\Models\History;

require __DIR__ . '/vendor/autoload.php';

$records = History::where('age', '<', 1)->get();

if ($records->isEmpty()) {
    echo "Không có record nào có tuổi nhỏ hơn 1 tháng.\n";
    exit;
}

foreach ($records as $record) {
    echo "ID: {$record->id} | Họ tên: {$record->fullname} | Tuổi: {$record->age} tháng | Ngày sinh: {$record->birthday} | Ngày đo: {$record->cal_date}\n";
}
