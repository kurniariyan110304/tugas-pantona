<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CmsUser;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        if (session()->has('user_id')) {
            return redirect('/users');
        }

        return view('login');
    }

    public function loginProcess(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = CmsUser::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Email tidak ditemukan'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password salah'
            ], 401);
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->nama,
            'user_email' => $user->email,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'redirect' => route('users.index')
        ]);
    }

    public function logout()
    {
        session()->flush();

        return response()->json([
            'status' => true,
            'redirect' => route('login')
        ]);
    }
}