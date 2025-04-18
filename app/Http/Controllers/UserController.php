<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'role_id' => $request->role_id,
        ]);

        $token = JWTAuth::fromUser($user);

        $this->log('สมัครสมาชิก', "ผู้ใช้ $user->username สมัครสมาชิกสำเร็จ", $user->id);

        return $this->returnCreated([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // ใช้ login แทน email/username
            'password' => 'required|string',
        ]);

        // ตรวจสอบว่า login เป็นอีเมลหรือยูสเซอร์เนม
        $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $login_type => $request->login,
            'password' => $request->password,
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->returnError('ไม่พบผู้ใช้หรือรหัสผ่านไม่ถูกต้อง', 401);
        }

        return $this->returnJson([
            'user' => Auth::user()->load('role'),
            'token' => $token,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return $this->returnSuccess('ออกจากระบบเรียบร้อยแล้ว');
    }

    public function profile()
    {
        return $this->returnJson(Auth::user());
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F',
        ]);

        $user->update($request->only([
            'first_name',
            'last_name',
            'phone_number',
            'date_of_birth',
            'gender',
        ]));

        $this->log('อัปเดตโปรไฟล์', "ผู้ใช้ ID: {$user->id} อัปเดตโปรไฟล์");

        return $this->returnSuccess('อัปเดตโปรไฟล์สำเร็จ');
    }
}
