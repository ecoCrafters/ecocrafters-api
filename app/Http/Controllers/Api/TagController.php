<?php

namespace App\Http\Controllers\Api;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllTags(Request $request)
    {
        $tags = Tag::get();
        if ($tags->count() < 1) {
            return response()->json(['message' => 'Nothing Tag To Show.'], 404);
        }
        return response()->json($tags, 200);
    }

    public function create(Request $request)
    {
        $data = $request->all();        
        $validator = Validator::make($data, [
            'tag' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        DB::beginTransaction();
        try {
            $tag = Tag::create([
                'tag' => $request->tag,
            ]);
            DB::commit();
            return response()->json($tag, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }      
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();        
        $validator = Validator::make($data, [
            'tag' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        DB::beginTransaction();
        try {
            $tag = Tag::find($id);
            $tag->tag = $request->tag;
            $tag->update();
            DB::commit();
            return response()->json($tag, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $tag = Tag::find($id);
        $tag->delete();
        return response()->json(['message' => 'Tag Succesfully Deleted.'], 200);
    }
}
