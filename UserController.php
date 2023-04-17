<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  public function register(Request $request){
    $request->validate([
        'name' => ['string', 'required'],
        'email' => ['string', 'required', 'unique:users'],
        'password' => ['string', 'required', 'min:8'],
        'birth_date' => ['string', 'required'],
        'gender' => ['string', 'required', 'max:10'],
        'pronoun' => ['string', 'nullable', 'required_if:gender,custom'],
        'custom_gender' => ['string', 'nullable'],
    ]);

    $user = New User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->birth_date = $request->birth_date;
    $user->gender = $request->gender;
    $user->pronoun = $request->pronoun;
    $user->custom_gender = $request->custom_gender;
    $user->save();

    return response()->json([
        'messages' => 'register success',
        'datas' => $user
    ],201);
}

    public function login(Request $request){
        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $token = $user->createToken("authtoken")->plainTextToken;
            return response()->json([
                'messages' => 'login success',
                'token' => $token,
                'tokenType' => 'Bearer',
                'datas' => $user
            ]);
        }else{
            return response()->json([
                'messages' => 'login unsuccessful'
            ], 401);
        };
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'messages' => 'logout success'
        ]);
    }
}
