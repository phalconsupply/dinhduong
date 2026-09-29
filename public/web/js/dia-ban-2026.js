/**
 * Combobox địa bàn 2026: Tỉnh → Xã (từ 01/7/2025 không còn cấp huyện).
 *
 * Dùng chung cho form nhập liệu và bộ lọc:
 *   <select id="province_code" data-wards-url="/.../ajax_get_ward_by_province">
 *   <select id="ward_code" data-placeholder="Chọn phường/xã">
 *
 * Tuỳ chọn trên select tỉnh: data-ward-target="#id-khac" nếu select xã không
 * mang id ward_code. Endpoint nhận ?province_code= và trả {wards: [{code, name}]}.
 */
(function ($) {
    $(document).on('change', 'select[data-wards-url]', function () {
        var $ward = $($(this).data('ward-target') || '#ward_code');
        var placeholder = $ward.data('placeholder') || 'Chọn phường/xã';

        $ward.empty().append($('<option>', {value: '', text: placeholder}));
        if (!this.value) {
            return;
        }
        $.getJSON($(this).data('wards-url'), {province_code: this.value}, function (res) {
            $.each(res.wards || [], function (_, w) {
                $ward.append($('<option>', {value: w.code, text: w.name}));
            });
        });
    });
})(jQuery);
