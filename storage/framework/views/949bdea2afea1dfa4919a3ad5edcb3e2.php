<?php $__env->startPush('foot'); ?>
    <script>
        $(document).ready(function () {
            $('.btnStore').on('click', function () {
                const row = $(this).closest('tr');
                let data = {};

                // Get all inputs in this row
                row.find('input, select').each(function () {
                    const key = $(this).data('key');
                    const value = $(this).val();
                    if (key) {
                        data[key] = value;
                    }
                });

                // Optional: validate required fields here...

                // Send via AJAX to Laravel
                $.ajax({
                    url: `/admin/type/?tab=<?php echo e($tab); ?>`,
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // if CSRF is needed
                    },
                    success: function (response) {
                        if (response && response.message) {
                            showToast('success', response.message);
                        } else {
                            showToast('success', 'Thao tác thành công.');
                        }
                        location.reload()
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON;

                        if (res && res.message) {
                            showToast('error', res.message);
                        } else if (res && res.errors) {
                            // Hiển thị lỗi đầu tiên từ validate
                            for (const key in res.errors) {
                                showToast('error', res.errors[key][0]);
                                break;
                            }
                        } else {
                            showToast('error', 'Đã xảy ra lỗi, vui lòng thử lại.');
                        }
                    }

                });
            });


            $('.btnSave').on('click', function () {
                let id = $(this).data('id');
                let row = $('tr[data-id="' + id + '"]');
                let data = {};

                // Lấy tất cả input trong row
                row.find('input[data-key]').each(function () {
                    let key = $(this).data('key');
                    let value = $(this).val();
                    data[key] = value;
                });

                // Lấy gender từ <select>
                let genderVal = row.find('select').val();
                data['gender'] = genderVal === 'Nam' ? 1 : 0;

                // Gửi request AJAX
                $.ajax({
                    url: `/admin/type/${id}?tab=<?php echo e($tab); ?>`,
                    type: 'PUT',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        if (res && res.message) {
                            showToast('success', res.message);
                        } else {
                            showToast('success', 'Thao tác thành công.');
                        }
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON;

                        if (res && res.message) {
                            showToast('error', res.message);
                        } else if (res && res.errors) {
                            // Hiển thị lỗi đầu tiên từ validate
                            for (const key in res.errors) {
                                showToast('error', res.errors[key][0]);
                                break;
                            }
                        } else {
                            showToast('error', 'Đã xảy ra lỗi, vui lòng thử lại.');
                        }
                    }
                });
            });
            $('.btnDel').on('click', function () {
                let id = $(this).data('id');
                if (!confirm('Bạn có chắc muốn xóa không?')) return;

                $.ajax({
                    url: `/admin/type/${id}?tab=<?php echo e($tab); ?>`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        // Xóa dòng trên giao diện
                        $('tr[data-id="' + id + '"]').remove();
                        if (res && res.message) {
                            showToast('success', res.message);
                        } else {
                            showToast('success', 'Thao tác thành công.');
                        }
                    },
                    error: function (xhr) {
                        const res = xhr.responseJSON;

                        if (res && res.message) {
                            showToast('error', res.message);
                        } else if (res && res.errors) {
                            // Hiển thị lỗi đầu tiên từ validate
                            for (const key in res.errors) {
                                showToast('error', res.errors[key][0]);
                                break;
                            }
                        } else {
                            showToast('error', 'Đã xảy ra lỗi, vui lòng thử lại.');
                        }
                    }
                });
            });
        });
    </script>

<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\dinhduong\resources\views/admin/type/script.blade.php ENDPATH**/ ?>