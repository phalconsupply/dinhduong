<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media.index');
    }
}
