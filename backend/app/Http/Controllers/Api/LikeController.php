<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        // 1. Cek apakah user benar-benar login. Jika tidak, hentikan dan kembalikan error 401.
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated. Silakan login kembali.'
            ], 401);
        }

        // 2. Ambil ID user yang sudah pasti ada (karena sudah lolos Auth::check)
        $userId = Auth::id();

        // 3. Cek apakah user sudah like post ini
        $existingLike = Like::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();

        if ($existingLike) {
            // Jika sudah ada, hapus (Unlike)
            $existingLike->delete();
            $isLiked = false;
        } else {
            // Jika belum ada, buat like baru
            Like::create([
                'user_id' => $userId,
                'post_id' => $post->id
            ]);
            $isLiked = true;
        }

        // 4. Hitung ulang jumlah like
        $likesCount = Like::where('post_id', $post->id)->count();

        return response()->json([
            'message' => $isLiked ? 'Post liked' : 'Post unliked',
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
