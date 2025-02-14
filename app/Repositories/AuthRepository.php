<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{
  public function login(array $credentials)
  {
    if (!Auth::attempt($credentials)) {
      return response()->json([
        'error' => 'Invalid Credentials'
      ], 401);
    }
    $user = Auth::user();
    $token = $user->createToken('auth_token')->plainTextToken;
    return response()->json([
      'message' => 'Login Successful',
      'data' => [
        'user' => $user,
        'token' => $token
      ]
    ], 200);
  }
}
