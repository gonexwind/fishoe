<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use PHPUnit\Exception;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:13',
                'password' => [new Password()],
            ]);
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
               'access_token' => $token,
               'token_type' => 'Bearer',
               'users' => $user,
            ], 'success registered user');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'something went wrong',
                'error' => $error,
            ], 'authenticated failed', 400);
        }
    }
}