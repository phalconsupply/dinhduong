/**
 * Combobox địa bàn 2026: Tỉnh → Xã (từ 01/7/2025 không còn cấp huyện).
 *
 * Dùng chung cho form nhập liệu và bộ lọc:
 *   <select id="province_code" data-wards-url="/.../ajax_get_ward_by_province">
 *   <select id="ward_code" data-placeholder="Chọn phường/xã">
 *
 * Tuỳ chọn trên select tỉnh: data-ward-target="#id-khac" nếu select xã không
 * mang id ward_code. Endpoint nhận ?province_code= và trả {wards: [{code, name}]}.
 *
 * Đổi tỉnh liên tục: request cũ bị huỷ và mọi phản hồi không khớp tỉnh đang
 * chọn đều bị bỏ, nên danh sách xã không bao giờ trộn giữa hai tỉnh.
 */
(function ($) {
    $(document).on('change', 'select[data-wards-url]', function () {
        var $tinh = $(this);
        var $ward = $($tinh.data('ward-target') || '#ward_code');
        var placeholder = $ward.data('placeholder') || 'Chọn phường/xã';
        var maTinh = this.value;

        var cu = $tinh.data('xhrXa');
        if (cu) {
            cu.abort();
        }

        $ward.empty().append($('<option>', {value: '', text: placeholder}));
        if (!maTinh) {
            $ward.prop('disabled', false).removeAttr('aria-busy');
            return;
        }

        $ward.prop('disabled', true).attr('aria-busy', 'true');
        $ward.find('option:first').text('Đang tải danh sách…');

        var xhr = $.getJSON($tinh.data('wards-url'), {province_code: maTinh});
        $tinh.data('xhrXa', xhr);

        xhr.done(function (res) {
            if ($tinh.val() !== maTinh) {
                return; // người dùng đã chọn tỉnh khác trong lúc chờ
            }
            $ward.find('option:first').text(placeholder);
            $.each(res.wards || [], function (_, w) {
                $ward.append($('<option>', {value: w.code, text: w.name}));
            });
        }).fail(function (_, trangThai) {
            if (trangThai === 'abort' || $tinh.val() !== maTinh) {
                return;
            }
            $ward.find('option:first').text('Không tải được danh sách xã — chọn lại tỉnh để thử lại');
        }).always(function () {
            if ($tinh.val() === maTinh) {
                $ward.prop('disabled', false).removeAttr('aria-busy');
            }
        });
    });
})(jQuery);
