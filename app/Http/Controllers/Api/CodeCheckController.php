<?php

namespace App\Http\Controllers\Api;

use App\Models\ResetCodePassword;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class CodeCheckController extends ApiController
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
            return response()->json(['message' => 'Code Is Expired.', 'status' => False], 422);
        }
        return response()->json(['message' => 'Code Is Valid.', 'status' => True], 200);
    }
}