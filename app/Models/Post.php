<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\Ingredient;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail',
        'num_of_likes',
        'user_id',
    ];

    public function tag()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function getTagIdAttribute()
    {
        return $this->tag->pluck('id');
    }

    public function ingredient()
    {
        return $this->belongsToMany(Ingredient::class, 'post_ingredients');
    }

    public function getTagIdAttribute()
    {
        return $this->tag->pluck('id');
    }
}
