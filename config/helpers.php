<?php

use App\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


function getUser($param) {
    $user = User::where('id', $param)
                    ->orWhere('email', $param)
                    // ->orWhere('username', $param)
                    ->first();
    $user['avatar_url'] = Storage::disk('gcs')->url($user->avatar);
        
    // $wallet = Wallet::where('user_id', $user->id)->first();
    // $user->profile_picture = $user->profile_picture ? 
    //     url('storage/'.$user->profile_picture) : "";
    // $user->ktp = $user->ktp ? 
    //     url('storage/'.$user->ktp) : "";
    // $user->balance = $wallet->balance;
    // $user->card_number = $wallet->card_number;
    // $user->pin = $wallet->pin;

    return $user;
}