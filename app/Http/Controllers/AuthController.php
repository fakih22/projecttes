<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email tidak ditemukan'
            ], 401);
        }

        if (!Hash::check($request->password, $customer->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Password salah'
            ], 401);
        }


        $token = $customer->createToken('mobile')->plainTextToken;

        Auth::login($customer);

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => $customer,
            'token'   => $token,              
        ], 200);
    }

    
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $request->user()
        ]);
    }

    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
