<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Services\WHO2006ZScoreService;
use Illuminate\Console\Command;

/**
 * Tính lại và đóng băng snapshot kết quả cho toàn bộ hồ sơ cân đo.
 *
 * Sau lệnh này, trang kết quả / bản in / thống kê đọc từ cột đã lưu thay vì tính
 * lại — nên việc sửa engine về sau không còn làm thay đổi phiếu đã lập.
 *
 * Chuẩn WHO áp dụng được xác định theo TUỔI TẠI THỜI ĐIỂM CÂN ĐO:
 *   - tuổi tháng < 60  -> who2006 (engine đã chuẩn hoá theo WHO Anthro)
 *   - tuổi tháng >= 60 -> who2007 (engine sẽ làm ở Phase 2 — hiện để trống)
 *
 * Cũng sửa luôn lỗi gán chéo cột của code cũ: result_height_age từng chứa kết quả
 * Weight-for-age và ngược lại. Từ lệnh này trở đi mỗi cột chứa đúng chỉ số của nó.
 */
class BackfillZScores extends Command
{
    protected $signature = 'who:backfill-zscores
                            {--dry-run : Chỉ hiển thị thay đổi, không ghi DB}
                            {--id= : Chỉ xử lý một bản ghi theo id}
                            {--limit= : Giới hạn số bản ghi xử lý}';

    protected $description = 'Tính lại Z-score và đóng băng snapshot cho toàn bộ hồ sơ cân đo';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('=== Backfill snapshot Z-score ===');
        $this->line('Engine 0-5 : ' . WHO2006ZScoreService::ENGINE_VERSION);
        $this->line('Chế độ     : ' . ($dryRun ? 'DRY-RUN (không ghi DB)' : 'GHI DB'));
        $this->newLine();

        $query = History::withTrashed()->orderBy('id');

        if ($this->option('id')) {
            $query->where('id', (int) $this->option('id'));
        }
        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $records = $query->get();
        $this->line('Số bản ghi: ' . $records->count());
        $this->newLine();

        $stat = [
            'who2006'        => 0,
            'who2007_cho'    => 0,
            'ngoai_pham_vi'  => 0,
            'thieu_du_lieu'  => 0,
            'doi_phan_loai'  => 0,
            'doi_hien_thi'   => 0,
            'da_ghi'         => 0,
        ];
        $changes = [];
        $visibleChanges = [];
        $outOfRange = [];

        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $row) {
            $bar->advance();

            $ageInDays = $row->getAgeInDays();
            if ($ageInDays === null || $row->gender === null) {
                $stat['thieu_du_lieu']++;
                continue;
            }

            $ageInMonths = WHO2006ZScoreService::ageInMonths($ageInDays);

            // Chuẩn áp dụng theo tuổi TẠI THỜI ĐIỂM ĐO
            if ($ageInMonths >= 60.0) {
                $stat['who2007_cho']++;
                $outOfRange[] = sprintf('id=%d age=%.2f tháng (%d ngày)', $row->id, $ageInMonths, $ageInDays);

                if (!$dryRun) {
                    // Ghi nhận chuẩn đúng, để trống Z-score cho Phase 2
                    $row->who_standard = 'who2007';
                    $row->saveQuietly();
                }
                continue;
            }

            $z = $row->getWho2006ZScores();
            if ($z === null || !$z['in_range']) {
                $stat['ngoai_pham_vi']++;
                $outOfRange[] = sprintf('id=%d age=%.2f tháng (%d ngày) — ngoài bảng WHO 0-5',
                    $row->id, $ageInMonths, $ageInDays);
                continue;
            }

            $stat['who2006']++;

            // Phân loại theo ngưỡng WHO 0-5 hiện hành
            $checkHfa = $row->classifyByZScore($z['z_hfa'], 'hfa');
            $checkWfa = $row->classifyByZScore($z['z_wfa'], 'wfa');
            $checkBmi = $row->classifyByZScore($z['z_bmi'], 'bmi');
            $checkWfh = $row->classifyByZScore($z['z_wfh'], 'wfh');

            $nutrition = $row->get_nutrition_status($checkWfa, $checkHfa, $checkWfh);

            // So với phân loại đang lưu, có tính tới việc cột cũ bị gán chéo
            $oldBmi = json_decode((string) $row->result_bmi_age, true)['result'] ?? null;
            $oldWfa = json_decode((string) $row->result_height_age, true)['result'] ?? null; // cột cũ chứa W/A
            $oldHfa = json_decode((string) $row->result_weight_age, true)['result'] ?? null; // cột cũ chứa H/A
            $oldWfh = json_decode((string) $row->result_weight_height, true)['result'] ?? null;

            $diffs = [];
            if ($oldBmi !== $checkBmi['result']) $diffs[] = "BMI/T {$oldBmi}→{$checkBmi['result']}";
            if ($oldWfa !== $checkWfa['result']) $diffs[] = "CN/T {$oldWfa}→{$checkWfa['result']}";
            if ($oldHfa !== $checkHfa['result']) $diffs[] = "CC/T {$oldHfa}→{$checkHfa['result']}";
            if ($oldWfh !== $checkWfh['result']) $diffs[] = "CN/CC {$oldWfh}→{$checkWfh['result']}";

            if ($diffs) {
                $stat['doi_phan_loai']++;
                if (!$row->trashed() && count($changes) < 25) {
                    $changes[] = sprintf('id=%-4d age=%6.2f  %s', $row->id, $ageInMonths, implode(' | ', $diffs));
                }
            }

            // Thay đổi so với thứ trang kết quả ĐANG hiển thị mới là thay đổi người
            // dùng nhìn thấy; lệch với result_* chỉ là dọn dữ liệu cũ tồn đọng.
            if (!$row->trashed()) {
                $visible = [];
                if ($row->check_bmi_for_age_auto()['result'] !== $checkBmi['result']) {
                    $visible[] = "BMI/T →{$checkBmi['result']}";
                }
                if ($row->check_weight_for_age_auto()['result'] !== $checkWfa['result']) {
                    $visible[] = "CN/T →{$checkWfa['result']}";
                }
                if ($row->check_height_for_age_auto()['result'] !== $checkHfa['result']) {
                    $visible[] = "CC/T →{$checkHfa['result']}";
                }
                if ($row->check_weight_for_height_auto()['result'] !== $checkWfh['result']) {
                    $visible[] = "CN/CC →{$checkWfh['result']}";
                }
                if ($visible) {
                    $stat['doi_hien_thi']++;
                    $visibleChanges[] = sprintf('id=%-4d age=%6.2f  %s',
                        $row->id, $ageInMonths, implode(' | ', $visible));
                }
            }

            if ($dryRun) {
                continue;
            }

            $isRisk = ($checkBmi['result'] !== 'normal' || $checkWfa['result'] !== 'normal'
                || $checkHfa['result'] !== 'normal' || $checkWfh['result'] !== 'normal') ? 1 : 0;

            $row->who_standard  = 'who2006';
            $row->z_hfa         = $z['z_hfa'];
            $row->z_wfa         = $z['z_wfa'];
            $row->z_bmi         = $z['z_bmi'];
            $row->z_wfh         = $z['z_wfh'];
            $row->z_flags       = $z['flags'] ? implode(',', $z['flags']) : null;
            $row->z_engine      = $z['engine'];
            $row->z_computed_at = now();

            // BMI lưu lại ở full precision của công thức WHO
            if ($z['bmi'] !== null) {
                $row->bmi = round($z['bmi'], 2);
            }

            // Gán ĐÚNG cột (code cũ gán chéo height/weight)
            $row->result_bmi_age       = json_encode($checkBmi, JSON_UNESCAPED_UNICODE);
            $row->result_height_age    = json_encode($checkHfa, JSON_UNESCAPED_UNICODE);
            $row->result_weight_age    = json_encode($checkWfa, JSON_UNESCAPED_UNICODE);
            $row->result_weight_height = json_encode($checkWfh, JSON_UNESCAPED_UNICODE);
            $row->nutrition_status     = $nutrition['text'];
            $row->is_risk              = $isRisk;

            $row->saveQuietly();
            $stat['da_ghi']++;
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('=== Kết quả ===');
        $this->line("Tính bằng WHO 2006          : {$stat['who2006']}");
        $this->line("Đổi phân loại so với result_* cũ: {$stat['doi_phan_loai']}");
        $this->line("  → trong đó ĐỔI THỨ ĐANG HIỂN THỊ (bản ghi còn dùng): {$stat['doi_hien_thi']}");
        $this->line("Tuổi >= 60 tháng, chờ Phase 2  : {$stat['who2007_cho']}");
        $this->line("Ngoài phạm vi bảng WHO 0-5     : {$stat['ngoai_pham_vi']}");
        $this->line("Thiếu dữ liệu đầu vào          : {$stat['thieu_du_lieu']}");
        $this->line("Đã ghi DB                      : {$stat['da_ghi']}");

        if ($outOfRange) {
            $this->newLine();
            $this->warn('Bản ghi không tính được bằng chuẩn 0-5:');
            foreach ($outOfRange as $line) {
                $this->line('  ' . $line);
            }
        }

        if ($visibleChanges) {
            $this->newLine();
            $this->warn('THAY ĐỔI NGƯỜI DÙNG NHÌN THẤY (bản ghi còn dùng) — cần rà:');
            foreach ($visibleChanges as $line) {
                $this->line('  ' . $line);
            }
        }

        if ($changes) {
            $this->newLine();
            $this->warn('Dọn result_* cũ tồn đọng (bản ghi còn dùng, tối đa 25 dòng):');
            foreach ($changes as $line) {
                $this->line('  ' . $line);
            }
            if ($stat['doi_phan_loai'] > count($changes)) {
                $this->line('  ... và ' . ($stat['doi_phan_loai'] - count($changes)) . ' bản ghi nữa');
            }
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN: không ghi gì vào DB. Bỏ --dry-run để ghi thật.');
        }

        return self::SUCCESS;
    }
}
