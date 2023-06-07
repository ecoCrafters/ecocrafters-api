<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use App\Models\Post;
use App\Models\Comment;
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

    public function show($username)
    {
        $user = getUser($username);
        // $pengikut = Follow::where('user_id_two', $user->id)->pluck('user_id_one')->all();
        // $mengikuti = Follow::where('user_id_one', $user->id)->pluck('user_id_two')->all();
        // $user['followers'] = User::whereIn('id', $pengikut)->get();
        // $user['followings'] = User::whereIn('id', $mengikuti)->get();
        // $user['posts'] = Post::whereUserId($user->id)->orderBy('id', 'desc')->get();
        // $user['comments'] = Comment::whereUserId($user->id)->orderBy('id', 'desc')->get();
        // $user['pengikut'] = User::whereIn('id', $pengikut)->get();
        return response()->json($user);
    }

    public function showFollowing($username)
    {
        $user = getUser($username);
        $mengikuti = Follow::where('user_id_one', $user->id)->pluck('user_id_two')->all();
        $user['followings'] = User::whereIn('id', $mengikuti)->get();
        return response()->json($user);
    }

    public function showFollowers($username)
    {
        $user = getUser($username);
        $pengikut = Follow::where('user_id_two', $user->id)->pluck('user_id_one')->all();
        $user['followers'] = User::whereIn('id', $pengikut)->get();
        return response()->json($user);
    }

    public function showPosts($username)
    {
        $user = getUser($username);
        $user['posts'] = Post::whereUserId($user->id)->orderBy('id', 'desc')->get();
        return response()->json($user);
    }

    public function showComments($username)
    {
        $user = getUser($username);
        // $user = User::whereUsername($username)->with('comments')->get();
        $user['comments'] = Comment::select('id','comment','num_of_likes','post_id')->whereUserId($user->id)->orderBy('id', 'desc')->with('posts')->get();
        return response()->json($user);
    }

    public function uploadFile(UploadedFile $file, $folder = null, $filename = null)
    {
        // $name = !is_null($filename) ? $filename : Str::random(25);
        $name = "avatar-" . auth()->user()->full_name;

        return $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            'gcs'
        );
    }

    public function searchUser(Request $request)
    {
        $search = $request->get('q');
        $users = User::select('id', 'full_name', 'username', 'avatar')
                    ->where('username', $search)
                    ->orWhere('full_name', $search)
                    ->where('id', '<>' ,$this->user->id)
                    ->get();
        
        if (count($users) > 0) {
            return response()->json($users);
        } else{
            return response()->json(['message' => "We Couldn't Find The User With That Keyword."], 404);
        }
    }

    public function update(Request $request)
    {

        try {
            $user = User::find($this->user->id);
            $data = $request->only('full_name', 'username', 'email', 'avatar','password');

            if ($request->email != $user->email || $request->username != $user->username) {
                $isExistEmail = User::where('email', $request->email)->exists();
                if ($isExistEmail) {
                    return response(['message' => 'Email / Username already taken.'], 409);
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

    public function checkFollow($id_target)
    {
        $user_login = auth()->user()->id;
        $check_follow = Follow::whereUserIdOne($user_login)->whereUserIdTwo($id_target)->exists();
        return response()->json($check_follow, 200);
    }
}
