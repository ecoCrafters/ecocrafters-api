<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\UserSavePost;
use Illuminate\Http\Request;
use Str;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function uploadFile(UploadedFile $file, $folder = null, $filename = null)
    {
        // $name = !is_null($filename) ? $filename : Str::random(25);
        $name = "thumbnail-" . $filename;
        return $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            'gcs'
        );
    }

    public function create(Request $request)
    {
        $data = $request->all();
        // return $data;
        
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'thumbnail' => 'file|image|mimes:jpeg,png,jpg|max:8012',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        
        DB::beginTransaction();

        try {
            $post = Post::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'content' => $request->content,
                // 'thumbnail' => $thumbnail,
                'user_id' => auth()->user()->id,
            ]);
            $thumbnail = "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png";
            if ($request->thumbnail){
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                // $data['thumbnail'] = $link;
            }
            $post->update([
                'thumbnail' => $thumbnail,
            ]);
            DB::commit();
            return response()->json($post, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }      
    }

    public function detail($slug, $id)
    {
        $post = Post::where('slug', $slug)->where('id', $id)->first();
        if ($post == NULL){
            return response()->json(['message' => 'Data Tidak Ditemukan.'], 404);
        }
        return response()->json($post, 200);
        
    }

    public function getPostByTitle(Request $request, $title)
    {
        $posts = Post::where('title', 'LIKE', '%'.$title.'%')->get();
        return response()->json($posts, 200);
    }

    public function searchPostOrUser(Request $request, $search)
    {
        $result['posts'] = Post::where('title', 'LIKE', '%'.$search.'%')->get();
        $result['users'] = User::where('full_name', 'LIKE', '%'.$search.'%')->get();
        return response()->json($result, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        // return $data;
        
        $validator = Validator::make($data, [
            'title' => 'required|string',
            'content' => 'required|string',
            'thumbnail' => 'file|image|mimes:jpeg,png,jpg|max:8012',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        
        DB::beginTransaction();

        try {
            $post = Post::find($id);
            if ($request->thumbnail && $post->thumbnail != "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png"){
                $this->deleteFile($post->thumbnail);
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                $post->thumbnail = $thumbnail;
            } else {
                $thumbnail = $request->hasFile('thumbnail') ? $this->uploadFile($request->file('thumbnail'), 'post_thumbnail', $post->id . "-" . Str::slug($request->title)) : null;
                $post->thumbnail = $thumbnail;
            }
            $post->title = $request->title;
            $post->content = $request->content;
            $post->slug = Str::slug($request->title);
            // $data = $request->only('title', 'content', 'thumbnail');
            $post->update();

            DB::commit();
            return response()->json($post, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function deleteFile($path = null)
    {
        Storage::disk('gcs')->delete($path);
    }

    public function delete(Request $request, $id)
    {
        $post = Post::find($id);
        if ($post->thumbnail != "https://storage.googleapis.com/ecocrafters_bucket/post_thumbnail/default-image.png") {
            Storage::disk('gcs')->delete($post->thumbnail);
        }
        $post->delete();
        return response()->json(['message' => 'Post Succesfully Deleted.'], 200);
    }

    public function savePost(Request $request, $id)
    {
        $post = Post::find($id);
        $user = auth()->user()->id;
        UserSavePost::create([
            'user_id' => $user,
            'post_id' => $id,
        ]);
        return response()->json(['message' => 'Post Succesfully Saved.'], 200);
    }
    
}
