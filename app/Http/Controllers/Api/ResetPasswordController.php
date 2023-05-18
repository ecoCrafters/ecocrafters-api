<?php

namespace App\Http\Controllers\Api;

use App\Models\ResetCodePassword;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ResetPasswordController extends ApiController
{
    /**
     * @param  mixed $request
     * @return void
     */
    public function __invoke(Request $request)
    {
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);
        if (!$passwordReset) {
            return response()->json(['message' => 'Code Is Invalid / Not Found.', 'status' => False], 404);
        }

        if ($passwordReset->isExpire()) {
            // return $this->jsonResponse(null, trans('passwords.code_is_expire'), 422);
            return response()->json(['message' => 'Code Is Expired.', 'status' => False], 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        $user->update([
            "password" => Hash::make($request->password)
        ]);

        $passwordReset->where('code', $request->code)->delete();

        // return $this->jsonResponse(null, trans('site.password_has_been_successfully_reset'), 200);
        return response()->json(['message' => 'Password has been successfully reset.', 'status' => True], 200);
    }
}