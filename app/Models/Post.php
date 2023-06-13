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

    public function getIngredientIdAttribute()
    {
        return $this->ingredient->pluck('id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getNamaUserAttribute()
    {
        if ($this->user) {
            return $this->user->username;
        }
    }

    public function user_save_posts()
    {
        return $this->belongsToMany(User::class, 'user_save_posts');
    }
    
    public function getUsersIdAttribute()
    {
        return $this->user_save_posts->pluck('id');
    }

}
