<?php
namespace App\Http\Controllers\Admin;

use App\Models\VnWard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AjaxController extends Controller
{
    /** Danh sách xã 2026 của một tỉnh, lọc theo phạm vi đơn vị của người dùng. */
    public function ajax_get_ward_by_province(Request $request)
    {
        $wards = VnWard::theoTinh($request->input('province_code'), null, true);
        return response()->json(['wards' => $wards]);
    }
}
