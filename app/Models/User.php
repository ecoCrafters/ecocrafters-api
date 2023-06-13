<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'username',
        'avatar',
        'email',
        'eco_points',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // public function sendPasswordResetNotification($token)
    // {

    //     $url = 'localhost:8000/reset-password?token=' . $token;

    //     $this->notify(new ResetPasswordNotification($url));
    // }

    public function posts()
    {
        return hasMany('App/Models/Post','user_id');
    }

    public function getNamaUserAttribute()
    {
        if ($this->user) {
            return $this->user->username;
        }
    }

    public function saved_posts()
    {
        return $this->belongsToMany(Post::class, 'user_save_posts');
    }

    public function getPostIdAttribute()
    {
        return $this->saved_posts->pluck('id');
    }

}
