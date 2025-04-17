<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'email|unique:users|required',
            'password'=>'min:6|required',
            'confirm_password'=>'required_with:password|same:password|min:6|required',
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            $user = User::where('email',$request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'status' => 401,
                    'success' => false
                ], 401);
            }
            if($user->isAdmin == 0){
                return response()->json([
                    'message' => 'Unauthorized user',
                    'status' => 403,
                    'success' => false
                ], 403);
            }
            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'status' => 200,
                'success' => true,
                'token' => $token,
                'expires_in' => now()->addHours(3)
            ]);
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'email|unique:users|required',
            'password'=>'min:6|required',
            'confirm_password'=>'required_with:password|same:password|min:6|required',
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>$validator->errors(),
                'status'=>400,
                'success'=>false
            ],400);
        }else{
            try{
                $user = User::create([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>$request->password,
                    ]);
                return response()->json([
                    'message' => 'User Created successful',
                    'status' => 201,
                    'success' => true
                ],201);
            }catch(Exception $e){
                return response()->json([
                    'message'=>'Something went wrong',
                    'status'=>400,
                    'success'=>false
                ],400);
            }
            
        }
    }
}
