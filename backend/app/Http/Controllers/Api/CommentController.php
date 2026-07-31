<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Tambah komentar atau reply
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        $comment->load('user:id,username,name,avatar');

        // Format waktu relatif (contoh: "2j", "5m")
        $comment->time_ago = $comment->created_at->diffForHumans(['short' => true, 'locale' => 'id']);
        $comment->likes_count = 0;
        $comment->is_liked = false;

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'comment' => $comment
        ], 201);
    }

    // Like / Unlike Komentar
    public function toggleLike(Comment $comment)
    {
        $userId = Auth::id();

        $existingLike = CommentLike::where('user_id', $userId)
            ->where('comment_id', $comment->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $isLiked = false;
        } else {
            CommentLike::create([
                'user_id' => $userId,
                'comment_id' => $comment->id
            ]);
            $isLiked = true;
        }

        $likesCount = CommentLike::where('comment_id', $comment->id)->count();

        return response()->json([
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }

    // Hapus komentar
    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin.'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Komentar berhasil dihapus']);
    }
}
