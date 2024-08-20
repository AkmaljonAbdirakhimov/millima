<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validatsiya qiling
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Foydalanuvchini yaratish
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => $request->password,
        ]);

        // Avtomatik login qilish
        Auth::login($user);

        // Token yaratish
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'User registered and logged in successfully!',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }
}
