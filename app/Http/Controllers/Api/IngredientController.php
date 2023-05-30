<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllIngredients(Request $request)
    {
        $ingredient = Ingredient::get();
        if ($ingredient->count() < 1) {
            return response()->json(['message' => 'Nothing ingredient To Show.'], 404);
        }
        return response()->json($ingredient, 200);
    }

    public function create(Request $request)
    {
        $data = $request->all();        
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        DB::beginTransaction();
        try {
            $ingredient = Ingredient::create([
                'name' => $request->name,
            ]);
            DB::commit();
            return response()->json($ingredient, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }      
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();        
        $validator = Validator::make($data, [
            'name' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }
        DB::beginTransaction();
        try {
            $ingredient = Ingredient::find($id);
            $ingredient->name = $request->name;
            $ingredient->update();
            DB::commit();
            return response()->json($ingredient, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $ingredient = Ingredient::find($id);
        $ingredient->delete();
        return response()->json(['message' => 'ingredient Succesfully Deleted.'], 200);
    }
}
