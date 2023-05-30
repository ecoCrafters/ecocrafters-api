<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;

class Tag extends Model
{
    use HasFactory;
    protected $table = 'tags';
    protected $fillable = [];
    protected $guarded = []; 

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
    
    public function getPostsIdAttribute()
    {
        return $this->posts->pluck('id');
    }
}
