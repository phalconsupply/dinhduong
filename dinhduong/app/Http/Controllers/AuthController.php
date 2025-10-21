<?php
namespace App\Http\Controllers;

use App\Models\Wallet;
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
        if(Auth::check()){
            return redirect(route('index'));
        }
        if($request->method() == 'POST')
        {
            $request->validate([
                'username' => 'required|max:12',
                'password'  => 'required|min:4|max:12',
            ]);
            $credential_username = array(
                'username'    => $request->get('username'),
                'password' => $request->get('password')
            );
            $remember = ($request->get('remember')) ? true : false;
            if (Auth::attempt($credential_username, $remember)) {

                if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('employee') || Auth::user()->hasRole('manager')){
                    if (Auth::user()->is_active == 1)
                    {
                        return redirect(route('index'));
                    }else
                    {
                        Auth::logout();
                        return redirect()->back()->withInput()->withErrors('Tài khoản của bạn chưa được kích hoạt, hãy kích hoạt trước khi sử dụng hệ thống!');
                    }
                }
                abort(403);
            }
            else
            {
                return redirect()->back()->withInput()->withErrors('Tài khoản hoặc mật khẩu không đúng!');
            }
        }
        $data = array(
            "title"    => 'Đăng nhập',
        );
        return view('auth.login', compact('data'));
    }
    public function register(Request $request)
    {
        if(Auth::check()){
            return redirect(route('web.home.index'));
        }
        if($request->method() == 'POST')
        {
            $validator = [
                'name' => 'required|max:32',
                'username' => 'required|string|alpha_dash|max:32|unique:users',
                'phone' => 'required|digits:10',
                'password'  => 'required|min:6|max:32',
                'terms'     => 'accepted',
            ];
            if($request->input('email')){
                $validator['email'] = 'email|unique:users';
            }
            $request->validate($validator);
            $auth_token = Str::random(100);
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => 1,
                'verify_email_token' => $auth_token
            ];
            $user = User::create($userData);
            $user->assignRole('user');
            //Tạo ví
            if($user){
                Wallet::create([
                    'code'    => $user->username,
                    'user_id' => $user->id,
                    'balance'  => 0,
                ]);
            }
//            //Gửi email
//            $link = url('/auth/active?token='.$auth_token);
//            $mail_data = array('name'=>$request->name, 'email'=> $request->email, 'link' => $link, 'template' => 'web.auth.email.verify', 'subject'=>'Xác nhận tài khoản');
//            event(new SendEmail($mail_data));

            return redirect(route('auth.login'))->with('success', 'Tài khoản đã tạo thành công!');
        }
        $data = array(
            "title"    => 'Đăng ký tài khoản',
        );
        return view('web.auth.register', compact('data'));
    }
    public function logout()
    {
        Auth::logout();
        return redirect(route('index'));
    }
    public function active(Request $request){
        if(Auth::check()){
            return redirect(route('web.home.index'));
        }
        $token = $request->token;
        if($token){
            $user = User::where('verify_email_token',$token)->first();
            if($user){
                $user->is_active = 1;
                $user->verify_email_token = NULL;
                $user->email_verified_at = Carbon::now();
                $user->save();
                return redirect(route('auth.login'))->with('success', 'Xác minh email thành công!');
            }
        }
        return redirect(route('auth.login'))->with('warning', 'Xác minh email không thành công!');
    }
    public function reset_password(Request $request){

        if(Auth::check()){
            return redirect(route('web.home.index'));
        }
        if($request->method() == 'POST')
        {
            $validator = Validator::make($request->all(), [
                'email'     => 'required|email'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            $user = User::where('email', $request->email)->first();
            if($user){
                $reset_password_token = Str::random(100);
                $user->reset_password_token = $reset_password_token;
                $user->save();
                //Gửi email
                $link = url('/auth/update-password?token='.$reset_password_token);
                $mail_data = array('name'=>$request->email, 'email'=> $request->email, 'link' => $link, 'template' => 'web.auth.email.reset-password', 'subject'=>'Khôi phục lại mật khẩu');
                event(new SendEmail($mail_data));
            }
            return redirect()->back()->with('warning', 'Bạn sẽ nhận được email chứa link khôi phục nếu email là đúng!');

        }
        $data = array(
            "title"    => 'Quên mật khẩu'
        );
        return view('web.auth.reset-password', compact('data'));
    }

    public function update_password(Request $request){
        if(Auth::check()){
            return redirect(route('web.home.index'));
        }
        if($request->method() == 'POST')
        {
            if($request->token) {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed|min:6|max:10',
                    'password_confirmation' => 'required|min:6|max:10',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator);
                }
                $user = User::where('reset_password_token', $request->get('token'))->first();
                if ($user) {

                    $user->reset_password_token = NULL;
                    $user->password = Hash::make($request->password);
                    $user->save();
                    return redirect(route('auth.login'))->with('success', 'Thiết lập lại mật khẩu thành công');
                }
            }
            abort(404);

        }
        if($request->token){
            $user = User::where('reset_password_token', $request->token)->first();
            if($user){
                $data = array(
                    "title"    => 'Thiết lập mật khẩu mơí'
                );
                $token = $request->token;
                return view('web.auth.update-password', compact('data', 'token'));
            }
        }
        abort(404);
    }
}
