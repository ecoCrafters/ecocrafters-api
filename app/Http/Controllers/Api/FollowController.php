<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request)
    {
        $user1 = auth()->user();
        try {
            $isExist = Follow::where('user_id_one', $user1->id)->where('user_id_two', $request->user_id_two)->exists();
            if ($user1->id != $request->user_id_two && !$isExist) {
                $follow = Follow::create([
                    'user_id_one' => $user1->id,
                    'user_id_two' => $request->user_id_two,
                ]);
                $follow['status'] = True;
                return response()->json($follow, 200);
            } else {
                $follow['status'] = False;
                return response()->json(['message' => 'Terjadi Galat, Silakan Coba Beberapa Saat Lagi atau Hubungi CS Untuk Info Lebih Lanjut.'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Terjadi Galat, Silakan Coba Beberapa Saat Lagi atau Hubungi CS Untuk Info Lebih Lanjut.'], 500);
        }
        
    }

    public function unfollow(Request $request)
    {
        $user = auth()->user();
        $target = $request->target;
        try {
            $isExist = Follow::where('user_id_one', $user->id)->where('user_id_two', $request->target)->exists();
            if ($isExist) {
                $unfollow_data = Follow::where('user_id_one', $user->id)->where('user_id_two', $target)->delete();
                return response()->json(['message' => 'Berhasil Unfollow'], 200);
            } else {
                return response()->json(['message' => 'Terjadi Galat, Silakan Coba Beberapa Saat Lagi atau Hubungi CS Untuk Info Lebih Lanjut.'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Terjadi Galat, Silakan Coba Beberapa Saat Lagi atau Hubungi CS Untuk Info Lebih Lanjut.'], 500);
            // return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
