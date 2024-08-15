<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{
    function login(Request $request)
  {
    $credentials = $request->only("email", "password");
    if (Auth::attempt($credentials)) {
      $token = auth()->user()->createToken("api_token")->plainTextToken;
      $response = [
        'user' => Auth::user(),
        'token' => $token
      ];
      return response($response, 201);
    }
    return response(['message' => 'Unauthorized!'], 401);
  }

  public function logout()
  {
    auth()->user()->tokens()->delete();
    return response([
      'message' => 'Logout'
    ], 201);
  }

  public function signup(Request $request)
  {
    $user = new User();
    $user->fill([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);
    $user->save();

    $credentials = $request->only("email", "password");
    if (Auth::attempt($credentials)) {
      $token = auth()->user()->createToken("api_token")->plainTextToken;
      $response = [
        'user' => Auth::user(),
        'token' => $token
      ];
      return response($response, 201);
    }
    else
      return response([
      'message' => 'Unsuccessfully registered the user.' 
    ], 500);
  }
}
