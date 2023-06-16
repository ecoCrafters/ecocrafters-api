<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Intervention\Image\ImageManagerStatic as Image;
use Intervention\Image\Facades\Image;
use File;
// use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\Post;
use App\Models\Ingredient;
use App\Models\PostIngredient;

class DetectionController extends Controller
{


    public function upload(Request $request)
    {
        $image = $request->file('image');
        try {
 
            $client = new Client();
            // $response = $client->post('http://localhost:7000/models/api', [
            // $response = $client->post('https://model-dot-ecocrafters-api.et.r.appspot.com/models/api', [
            // $response = $client->post('https://modeleco.pythonanywhere.com/models/api', [
            $response = $client->post('http://34.101.118.41:8000/models/api', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($image->getPathname(), 'r'),
                        'filename' => iconv("utf-8", "cp936", $image->getClientOriginalName()),
                    ],
                    [
                        'name' => 'name',
                        'contents' => mb_convert_encoding(auth()->user()->username, 'UTF-8', 'UTF-8'),
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $string = trim($responseBody, "[]");

            // Membagi string menjadi array menggunakan tanda koma sebagai pemisah
            $array = explode(",", $string);

            // Mengubah elemen array menjadi float
            $array = array_map('floatval', $array);
            
            $post_ingredients = PostIngredient::whereIn('ingredient_id', $array)->pluck('post_id');
            $ingredients = Ingredient::whereIn('id', $array)->get();
            $posts = Post::whereIn('id',$post_ingredients)->with('user')->get();
            return response()->json([
                'status' => $statusCode,
                'label' => $array,
                'ingredients' => $ingredients,
                'posts' => $posts
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        } 
    }
}
