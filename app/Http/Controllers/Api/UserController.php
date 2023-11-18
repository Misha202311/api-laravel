<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller

{
    private $sucess_status = 200;

    // ------------- [ User Sign Up ] ---------------
    public function createUser(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name'  => 'required',
                'role'  => 'required',
                'email'  =>  'required|email',
                'password'  => 'required|alpha_num|min:5'
            ]
        );

        if ($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $dataArray = array(
            "name"  =>  $request->name,
            "role"  =>  $request->role,
            "email"  =>  $request->email,
            "password" => bcrypt($request->password),

        );

        $user =  User::create($dataArray);

        if (!is_null($user)) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $user]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "User not created. please try again."]);
        }
    }

    // ------------------- [ User Login ] ----------------

    public function userLogin(Request $request)
    {

        $validator =  Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|alpha_num|min:6'
            ]
        );

        if ($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user  =  Auth::user();
            $token =  $user->createToken('token')->accessToken;

            return response()->json(["status" => $this->sucess_status, "success" => true, "login" => true, "token" => $token, "data" => $user]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Invalid email or password"]);
        }
    }

}
