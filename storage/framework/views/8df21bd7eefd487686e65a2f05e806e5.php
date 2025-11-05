<?php $__env->startSection('title'); ?>
    <?php echo getSetting('site_name') ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body_class', 'home'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="layout-specing">
            <form action="" method="POST">
                <?php echo csrf_field(); ?>

            <?php echo $__env->make('admin.type.tabs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="row">
                <div class="col-12 ">
                    <div class="card">
                        <div class="card-body p-0">
                            <table  id="transactions-table"  class="table bg-white mb-0" >
                                <thead>
                                <tr>
                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;"><?php echo e($col); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;">Giới tính</th>
                                    <th class="border-bottom sticky-top bg-white" style="top: 0; z-index: 1;">#</th>
                                </tr>
                                </thead>
                                <tbody id="dataBody">
                                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="existing-row" data-id="<?php echo e($row['id']); ?>" data-index="<?php echo e($index); ?>">
                                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><input name="<?php echo e($col); ?>[]" data-id="<?php echo e($row['id']); ?>"  data-key="<?php echo e($col); ?>" type="text" value="<?php echo e($row[$col]); ?>" class="form-control form-control-sm" required></td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <td >
                                            <select name="gender[]" data-id="<?php echo e($row['id']); ?>" class="form-control form-control-sm">
                                                <option value="">Chọn giới tính</option>
                                                <option value="1" <?php if($row['gender'] == 1): ?> selected <?php endif; ?>>Nam</option>
                                                <option value="0" <?php if($row['gender'] == 0): ?> selected <?php endif; ?>>Nữ</option>
                                            </select>
                                        </td>
                                        <td><button data-id="<?php echo e($row['id']); ?>" type="button" class="btnDel btn-sm btn btn-danger p-0 m-0"><i class="ti ti-trash-x icons icon-sm"></i></button></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!--end row-->
            <?php echo $__env->make('admin.type.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </form>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('head'); ?>
<style>
#dataBody tr{
    padding: 0;
    margin: 0;
}
#dataBody tr td{
    padding: 0;
    margin: 0;
    border-left: 1px solid #f0eeee;
}
#dataBody tr td input{
    border: 0px !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app-full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/type/index.blade.php ENDPATH**/ ?>