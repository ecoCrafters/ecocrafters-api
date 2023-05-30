<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Ingredient extends Model
{
    use HasFactory;
    protected $table = 'ingredients';
    protected $fillable = ['name'];
    protected $guarded = []; 

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_ingredients');
    }
    
    public function getPostsIdAttribute()
    {
        return $this->posts->pluck('id');
    }
}
