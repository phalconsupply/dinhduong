<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Auth;
use Hash;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $is_disable = 'disabled';
        if($user->hasRole('admin')){
            $is_disable = '';
        }
        return view('admin.profile.index', compact('user', 'is_disable'));
    }

    public function changepassword ()
    {
        $user = Auth::user();
        $is_disable = 'disabled';
        if($user->hasRole('admin')){
            $is_disable = '';
        }
        return view('admin.profile.changepassword', compact('user', 'is_disable'));
    }

    public function update(Request $request){
        $admin = Auth::user();
        if($admin->hasRole('admin')){
            $request->validate([
                'name' => 'required|min:3|max:25',
                'phone' => 'required|min:10|max:12',
                'email'     => 'required|email|unique:users,email,'.$admin->id
            ]);
            $input = $request->all();
            $admin->update([
                'name'     => $request->name,
                'phone'    => $request->phone,
                'email'    => $request->email,
            ]);
            return redirect()->route('admin.profile.index')
                ->with('success', 'Cập nhật thông tin tài khoản thành công.');
        }
        return redirect()->route('admin.profile.index')
            ->with('warning', 'Không thể cập nhật thông tin profile!');
    }


    public function update_password(Request $request){
        $user = Auth::user();
        $request->validate([
            'password'  => 'required|confirmed|min:6|max:10',
            'password_confirmation' => 'required'
        ]);
        $userData = [
            'password' => Hash::make($request->password)
        ];
        $user->update($userData);
        return redirect()->route('admin.profile.changepassword')
            ->with('success', 'Thay đổi mật khẩu tài khoản thành công.');
    }
}
