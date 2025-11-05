<div class="d-md-flex justify-content-between align-items-center">
    <h5 class="mb-0">Quản lý đối tượng</h5>
</div>
<div class="row mt-3">
    <div class="col-lg-12">
        <ul class="nav nav-pills nav-tabs flex-column flex-sm-row rounded" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a href="<?php echo e(route('admin.type.index', ['tab'=>'weight-for-age'])); ?>" class="nav-link  <?php if($tab == 'weight-for-age'): ?> active <?php endif; ?>" id="shipping-tab"  >
                    <div class="text-center py-2">
                        <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Cân nặng theo tuổi</h6>
                    </div>
                </a><!--end nav link-->
            </li><!--end nav item-->
            <li  class="nav-item">
                <a href="<?php echo e(route('admin.type.index', ['tab'=>'height-for-age'])); ?>" class="nav-link  <?php if($tab == 'height-for-age'): ?> active <?php endif; ?>" id="info-tab"   >
                    <div class="text-center py-2">
                        <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Chiều cao theo tuổi</h6>
                    </div>
                </a><!--end nav link-->
            </li><!--end nav item-->
            <li class="nav-item">
                <a href="<?php echo e(route('admin.type.index', ['tab'=>'weight-for-height'])); ?>" class="nav-link  <?php if($tab == 'weight-for-height'): ?> active <?php endif; ?>" id="payment-tab"  >
                    <div class="text-center py-2">
                        <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> Cân nặng theo chiều cao</h6>
                    </div>
                </a><!--end nav link-->
            </li><!--end nav item-->
            <li class="nav-item">
                <a href="<?php echo e(route('admin.type.index', ['tab'=>'bmi-for-age'])); ?>" class="nav-link  <?php if($tab == 'bmi-for-age'): ?> active <?php endif; ?>" id="info-tab"   >
                    <div class="text-center py-2">
                        <h6 class="mb-0"><i class="ti ti-info-circle icons icon-sm"></i> BMI theo tuổi</h6>
                    </div>
                </a><!--end nav link-->
            </li>
            <li class="nav-item ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="btnAddRow" class="btn btn-success btn-sm d-flex align-items-center gap-1 px-2 py-1">
                        <i class="ti ti-plus icons icon-sm"></i>
                        <span style="font-size: 0.8rem;">Thêm dữ liệu</span>
                    </button>
                    <button type="submit" id="btnSaveAll" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-2 py-1">
                        <i class="ti ti-device-floppy icons icon-sm"></i>
                        <span style="font-size: 0.8rem;">Lưu tất cả</span>
                    </button>
                </div>
            </li>

        </ul><!--end nav pills-->

    </div><!--end col-->
</div><!--end row-->


<script type="text/template" id="rowTemplate">
    <tr data-id="__index__">
        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <td><input name="<?php echo e($col); ?>[]" type="text" class="form-control form-control-sm" required></td>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <td>
            <select name="gender[]" class="form-control form-control-sm" required>
                <option value="">Chọn giới tính</option>
                <option value="1">Nam</option>
                <option value="0">Nữ</option>
            </select>
        </td>
        <td><button type="button" class="btnRemove btn btn-danger btn-sm p-0 m-0"><i class="ti ti-trash-x"></i></button></td>
    </tr>
</script>

<?php $__env->startPush('foot'); ?>
<script>
    let rowIndex = 0;
    let prependAnchor = null;

    $('#btnAddRow').on('click', function () {
        const template = $('#rowTemplate').html();
        const $newRow = $(template);

        // Tìm vị trí prepend: trước dòng đầu tiên cũ, hoặc thêm vào đầu nếu chưa có dòng nào
        if (!prependAnchor) {
            prependAnchor = $('#dataBody').find('tr.existing-row').first();
        }

        if (prependAnchor.length) {
            // Nếu có dữ liệu cũ: chèn trước dòng đầu tiên cũ
            $newRow.insertBefore(prependAnchor);
        } else {
            // Nếu chưa có dữ liệu cũ: prepend vào đầu
            $('#dataBody').prepend($newRow);
        }

        rowIndex++;
    });

    $(document).on('click', '.btnRemove', function () {
        $(this).closest('tr').remove();
    });
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/type/tabs.blade.php ENDPATH**/ ?>