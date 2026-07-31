<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'avatar',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // RELASI
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Cek apakah user sudah like post tertentu
    public function hasLiked(Post $post)
    {
        return $this->likes()->where('post_id', $post->id)->exists();
    }

    // Relasi Following (user yang saya follow)
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
    }

    // Relasi Followers (user yang follow saya)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
    }

    // Cek apakah saya follow user tertentu (HANYA ADA 1 KALI DI SINI)
    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }
}
