<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Please provide valid email and password',
                ], 401);
            }
            $credentials = $request->only('email', 'password');
            $token = Auth::guard("api")->attempt($credentials);

            if (!$token) {
                return response()->json(['msg' => "error, email or password is wrong"]);
            }
            $user = Auth::guard("api")->user();
            $user->token = $token;
            return response()->json(['msg' => $user]);
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Please provide valid name, email and password',
            ], 401);
        }
        $user = User::Create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);
        if ($user) {
            return $this->login($request);
        }
        return response()->json(['msg' => "error"]);
    }

    public function logout (Request $request)
    {
        try{
            JWTAuth::invalidate($request->token);
            return response()->json(['msg'=>'success']);

        }catch(JWTException $E){
            return \response()->json(['msg'=>$E->getMessage()]);
        }
    }
    public function refresh (Request $request)
    {
        $new_token = JWTAuth::refresh($request->token);
        if ($new_token){
            return response()->json(['msg' => $new_token]);
        }
        return response()->json(['msg' => 'error']);

    }
}
