<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index() {
        return view('admin.login');
    }
    // this function will be authenticate user
    public function authenticate(Request $request) {
        // xác thực yêu cầu
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password' => 'required'
        ]);
        // kiểm tra các trường đâu vào có hợp lệ không ?
        if ($validator->passes()) {
            // thử đăng nhập với guard 'admin'
            if (Auth::guard('admin')->attempt([ 'email'=> $request->email,
            'password'=> $request->password], $request->get('remember'))) {
                // lấy thông tin người dùng đã đăng nhập
                $admin = Auth::guard('admin')->user();
                // kiểm tra vai trò của user (2 ở đây là role admin)        
                if ($admin-> role == 2) {
                    // nếu là admin sẽ chuyển hướng đến trang dashboard
                    return redirect()->route('admin.dashboard'); 
                } else {
                    // nếu không phải là admin, đăng xuất và chuyển hướng lại đến trang đăng nhập để đăng nhập lại và thông báo lỗi
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.login')->with('error', 'You are not authorized to access
                    admin panel.');   

                }

            } else {
                 // Nếu thông tin đăng nhập không đúng, chuyển hướng lại đến trang đăng nhập với thông báo lỗi
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is Incorrect');   
            }
        } else {
            // Nếu xác thực yêu cầu thất bại, chuyển hướng lại đến trang đăng nhập với lỗi xác thực và giữ lại dữ liệu đầu vào của email
            return redirect()->route('admin.login')
            ->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }
   
}
