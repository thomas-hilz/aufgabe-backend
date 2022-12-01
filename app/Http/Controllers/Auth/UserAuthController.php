<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller
{
  public function register(Request $request)
  {
      $data = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required'
      ],
      [
          'email.required'    => 'Bitte eine Email angeben.',
          'email.unique'      => 'Diese Email wurde bereits verwendet.',
          'email.email'       => 'Bitte eine gültige Email-Adresse angeben.' ,
          'password.required' => 'Bitte ein Passwort angeben.',
      ]
      );
      $data['password'] = bcrypt($request->password);
      $user = User::create($data);
      return response([ 'user' => $user]);
  }

  public function login(Request $request)
  {
      $data = $request->validate([
          'email' => 'required',
          'password' => 'required'
      ],
      [
          'email.required'    => 'Bitte eine Email angeben.',
          'password.required' => 'Bitte ein Passwort angeben.',
      ]);

      if (!auth()->attempt($data)) {
          return response(['incorrect_credentials' => 'Email existiert nicht oder das Passwort ist falsch.']);
      }

      $token = auth()->user()->createToken('API Token')->accessToken;
      Log::info("My token: {$token}");
      return response(['user' => auth()->user(), 'token' => $token]);

  }
}
