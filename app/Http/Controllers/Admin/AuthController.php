<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use App\Events\SendEmail;
class AuthController extends Controller
{
    public function __construct()
    {

    }
    public function login(Request $request)
    {
//        dd(1);
//        if(Auth::check()){
//            return redirect(route('dashboard.index'));
//        }
        if($request->method() == 'POST')
        {
            $credentials = array(
                'username'    => $request->get('username'),
                'password' => $request->get('password')
            );
            $remember = ($request->get('remember')) ? true : false;
            if (Auth::attempt($credentials, $remember)) {
                if(!Auth::user()->hasAnyRole(['admin','manager','employee'])){
                    abort(403);
                }
                if (Auth::user()->is_active == 1)
                {
//                    $user = User::find(Auth::user()->id);
//                    $shoppe = New Shoppe();
//                    $shoppe->getRefreshToken();
                    return redirect(route('admin.dashboard.index'));
                }
                else
                {
                    Auth::logout();
                    return redirect()->back()->withInput()->withErrors('Tài khoản của bạn chưa được kích hoạt, hãy kích hoạt trước khi sử dụng hệ thống!');
                }
            }
            else
            {
                return redirect()->back()->withInput()->withErrors('Tài khoản hoặc mật khẩu không đúng!');
            }
        }
        $data = array(
            "title"    => 'Đăng nhập',
        );
        return view('admin.auth.login', compact('data'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect(route('admin.auth.login'));
    }
}
