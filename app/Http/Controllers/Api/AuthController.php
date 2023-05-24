<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use App\Models\User;
use App\Models\Wallet;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        // return $data;
        
        $validator = Validator::make($data, [
            'full_name' => 'required|string',
            'username' => 'string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $user = User::where('email', $request->email)->orWhere('username', $request->username)->exists();
        
        if ($user) {
            return response()->json(['message' => 'Email / username already taken.'], 409);
        }
        
        
        DB::beginTransaction();

        try {
            // Avatar Default Sementara, Nanti Diubah
            $avatar = "https://storage.googleapis.com/ecocrafters-api.appspot.com/avatar.png";
            if ($request->avatar) {
               $avatar = uploadBase64Image($request->avatar);
            }

            $user = User::create([
                'full_name' => $request->full_name,
                'username' => $request->username,
                'avatar' => $avatar,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            DB::commit();

            $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password]);

            $userResponse = getUser($user->id);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }      
            

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        try {
            $token = JWTAuth::attempt($credentials);
            
            if (!$token) {
                return response()->json(['message' => 'Login credentials are invalid'], 400);
            }

            $userResponse = getUser($request->email);
            $userResponse->token = $token;
            $userResponse->token_expires_in = auth()->factory()->getTTL() * 120;
            // $userResponse->token_expires_in = auth()->factory()->getTTL() * 60;
            $userResponse->token_type = 'bearer';

            return response()->json($userResponse);

        } catch (JWTException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
 	
    }


    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Log out success']);
    }

}
