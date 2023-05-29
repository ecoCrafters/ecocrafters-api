<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\UserSavePost;
use App\Models\UserLikePost;
use App\Models\UserLikeComment;
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

    public function getAllPosts(Request $request)
    {
        $posts = Post::get();
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
        $check = UserSavePost::wherePostId($id)->whereUserId($user)->exists();
        if ($check == False){
            UserSavePost::create([
                'user_id' => $user,
                'post_id' => $id,
            ]);
        } else {
            return response()->json(['message' => 'Post Already Saved Before.'], 500);
        }
        return response()->json(['message' => 'Post Succesfully Saved.'], 200);
    }

    public function likePost(Request $request, $id)
    {
        $post = Post::find($id);
        $user = auth()->user()->id;
        $check = UserLikePost::wherePostId($id)->whereUserId($user)->exists();
        if ($check == False){
            UserLikePost::create([
                'user_id' => $user,
                'post_id' => $id,
            ]);
            $post->num_of_likes = $post->num_of_likes + 1;
            $post->update();
        } else {
            return response()->json(['message' => 'Post Already Liked Before.'], 500);
        }
        return response()->json(['message' => 'Post Succesfully Liked.'], 200);
    }

    public function commentPost(Request $request, $id)
    {
        $post = Post::find($id);
        $user = auth()->user()->id;
        if ($request->comment) {
            Comment::create([
                'comment' => $request->comment,
                'user_id' => $user,
                'post_id' => $id,
            ]);
            $post->num_of_comments = $post->num_of_comments + 1;
            $post->update();
        } else {
            return response()->json(['message' => 'Failed to comment, make sure you have filled the comment input.'], 500);
        }
        
        return response()->json(['message' => 'Succesfully Comment on This Post.'], 200);
    }

    public function likeComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        $user = auth()->user()->id;
        $check = UserLikeComment::whereCommentId($id)->whereUserId($user)->exists();
        if ($check == False){
            UserLikeComment::create([
                'user_id' => $user,
                'comment_id' => $id,
            ]);
            $comment->num_of_likes = $comment->num_of_likes + 1;
            $comment->update();
        } else {
            return response()->json(['message' => 'Comment Already Liked Before.'], 500);
        }
        return response()->json(['message' => 'Comment Succesfully Liked.'], 200);
    }
    
}
