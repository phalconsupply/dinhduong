<h6 class="mb-3">
    <i class="uil uil-analytics text-primary"></i>
    <?php echo e($data['label']); ?>

</h6>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm" id="table-who-<?php echo e(strtolower(str_replace(' ', '-', $data['label']))); ?>">
        <thead class="table-light">
            <tr>
                <th rowspan="2" class="align-middle text-center" style="width: 120px;">Nhóm tuổi<br>(tháng)</th>
                <th rowspan="2" class="align-middle text-center" style="width: 80px;">N</th>
                <th colspan="4" class="text-center bg-info bg-opacity-10">Weight-for-Age</th>
                <th colspan="4" class="text-center bg-success bg-opacity-10">Height-for-Age</th>
                <th colspan="7" class="text-center bg-warning bg-opacity-10">Weight-for-Height</th>
            </tr>
            <tr>
                
                <th class="text-center" style="width: 70px;">< -3 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">< -2 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">Mean<br>(SD)</th>
                <th class="text-center" style="width: 70px;">SD</th>
                
                
                <th class="text-center" style="width: 70px;">< -3 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">< -2 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">Mean<br>(SD)</th>
                <th class="text-center" style="width: 70px;">SD</th>
                
                
                <th class="text-center" style="width: 70px;">< -3 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">< -2 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">> +1 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">> +2 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">> +3 SD<br>(%)</th>
                <th class="text-center" style="width: 70px;">Mean<br>(SD)</th>
                <th class="text-center" style="width: 70px;">SD</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = ['0-5', '6-11', '12-23', '24-35', '36-47', '48-60']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ageKey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($data['stats'][$ageKey])): ?>
                    <?php
                        $row = $data['stats'][$ageKey];
                        // Determine gender from label
                        $genderValue = 'total';
                        if (isset($data['label'])) {
                            if (str_contains($data['label'], 'trai')) {
                                $genderValue = 'male';
                            } elseif (str_contains($data['label'], 'gái')) {
                                $genderValue = 'female';
                            }
                        }
                        // Format age group for data attribute (0-5m format)
                        $ageGroupFormatted = $ageKey . 'm';
                    ?>
                    <tr>
                        <td class="text-center"><?php echo e($row['label']); ?></td>
                        <td class="text-center fw-bold"
                            data-clickable="true"
                            data-tab="who-combined"
                            data-gender="<?php echo e($genderValue); ?>"
                            data-age-group="<?php echo e($ageGroupFormatted); ?>"
                            data-classification="all"
                            data-title="WHO Combined: <?php echo e($data['label']); ?> - <?php echo e($row['label']); ?>">
                            <?php echo e(number_format($row['n'])); ?>

                        </td>
                        
                        
                        <td class="text-end"><?php echo e(number_format($row['wa']['lt_3sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wa']['lt_2sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wa']['mean'], 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wa']['sd'], 2)); ?></td>
                        
                        
                        <td class="text-end"><?php echo e(number_format($row['ha']['lt_3sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['ha']['lt_2sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['ha']['mean'], 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['ha']['sd'], 2)); ?></td>
                        
                        
                        <td class="text-end"><?php echo e(number_format($row['wh']['lt_3sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['lt_2sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['gt_1sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['gt_2sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['gt_3sd_pct'], 1)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['mean'], 2)); ?></td>
                        <td class="text-end"><?php echo e(number_format($row['wh']['sd'], 2)); ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            
            <?php if(isset($data['stats']['total'])): ?>
                <?php
                    $total = $data['stats']['total'];
                    // Determine gender from label
                    $genderValue = 'total';
                    if (isset($data['label'])) {
                        if (str_contains($data['label'], 'trai')) {
                            $genderValue = 'male';
                        } elseif (str_contains($data['label'], 'gái')) {
                            $genderValue = 'female';
                        }
                    }
                ?>
                <tr class="table-primary fw-bold">
                    <td class="text-center"><?php echo e($total['label']); ?></td>
                    <td class="text-center"
                        data-clickable="true"
                        data-tab="who-combined"
                        data-gender="<?php echo e($genderValue); ?>"
                        data-classification="all"
                        data-title="WHO Combined: <?php echo e($data['label']); ?> - Tổng">
                        <?php echo e(number_format($total['n'])); ?>

                    </td>
                    
                    
                    <td class="text-end"><?php echo e(number_format($total['wa']['lt_3sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wa']['lt_2sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wa']['mean'], 2)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wa']['sd'], 2)); ?></td>
                    
                    
                    <td class="text-end"><?php echo e(number_format($total['ha']['lt_3sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['ha']['lt_2sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['ha']['mean'], 2)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['ha']['sd'], 2)); ?></td>
                    
                    
                    <td class="text-end"><?php echo e(number_format($total['wh']['lt_3sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['lt_2sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['gt_1sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['gt_2sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['gt_3sd_pct'], 1)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['mean'], 2)); ?></td>
                    <td class="text-end"><?php echo e(number_format($total['wh']['sd'], 2)); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="alert alert-light mt-3">
    <small>
        <strong><i class="uil uil-info-circle"></i> Chú thích:</strong><br>
        • <strong>N:</strong> Số lượng trẻ trong nhóm tuổi<br>
        • <strong>< -3 SD / < -2 SD:</strong> Tỷ lệ % trẻ dưới ngưỡng -3SD và -2SD (suy dinh dưỡng nghiêm trọng / vừa)<br>
        • <strong>> +1 SD / +2 SD / +3 SD:</strong> Tỷ lệ % trẻ trên ngưỡng +1SD, +2SD, +3SD (thừa cân / béo phì)<br>
        • <strong>Mean (SD):</strong> Giá trị trung bình và độ lệch chuẩn của Z-score<br>
        • <strong>W/A:</strong> Weight-for-Age (Cân nặng/Tuổi) - Đánh giá suy dinh dưỡng<br>
        • <strong>H/A:</strong> Height-for-Age (Chiều cao/Tuổi) - Đánh giá thấp còi<br>
        • <strong>W/H:</strong> Weight-for-Height (Cân nặng/Chiều cao) - Đánh giá gầy còm/thừa cân
    </small>
</div>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/statistics/tabs/partials/who-table.blade.php ENDPATH**/ ?>