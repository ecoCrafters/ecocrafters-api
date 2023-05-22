<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;


class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function show()
    {
        $user = getUser($this->user->id);
        $pengikut = Follow::where('user_id_two', $user->id)->pluck('user_id_one')->all();
        $mengikuti = Follow::where('user_id_one', $user->id)->pluck('user_id_two')->all();
        $user['pengikut'] = User::whereIn('id', $pengikut)->get();
        $user['mengikuti'] = User::whereIn('id', $mengikuti)->get();
        // $user['pengikut'] = User::whereIn('id', $pengikut)->get();
        return response()->json($user);
    }

    public function uploadFile(UploadedFile $file, $folder = null, $filename = null)
    {
        // $name = !is_null($filename) ? $filename : Str::random(25);
        $name = "avatar-" . auth()->user()->first_name . auth()->user()->last_name;

        return $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            'gcs'
        );
    }

    public function getUserByUsername(Request $request, $username)
    {
        $users = User::select('id', 'name', 'username', 'verified', 'profile_picture')
                    ->where('username', 'LIKE', '%'.$username.'%')
                    ->where('id', '<>' ,$this->user->id)
                    ->get();

        $users->map(function ($item) {
            $item->profile_picture = $item->profile_picture ? 
                url('storage/'.$item->profile_picture) : "";

            return $item; 
        });

        return response()->json($users);
    }

    public function update(Request $request)
    {

        try {
            $user = User::find($this->user->id);
            $data = $request->only('first_name', 'last_name', 'email', 'avatar','password');

            if ($request->email != $user->email) {
                $isExistEmail = User::where('email', $request->email)->exists();
                if ($isExistEmail) {
                    return response(['message' => 'Email already taken'], 409);
                }
            }
    
            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            // How Retrieve Image ?
            // return Storage::disk('gcs')->url($user->avatar);
            if ($request->avatar){
                $this->deleteFile($user->avatar);
                $link = $request->hasFile('avatar') ? $this->uploadFile($request->file('avatar'), 'user_avatar') : null;
                $data['avatar'] = $link;
            }
    
            $user->update($data);
    
            return response()->json(['message' => 'Profil Updated!'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }

    }

    public function deleteFile($path = null)
    {
        Storage::disk('gcs')->delete($path);
    }
}
