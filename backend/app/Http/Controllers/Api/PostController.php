<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\CommentLike; // Tambahkan ini untuk fitur like komentar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // 1. Ambil semua postingan (untuk Feed)
    // 1. Ambil semua postingan (untuk Feed)
    public function index()
    {
        $posts = Post::with([
            'user:id,name,username,avatar',
            'likes',
            // Hanya ambil komentar utama, tapi load juga replies-nya
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user:id,name,username,avatar', 'likes', 'replies.user:id,name,username,avatar', 'replies.likes']);
            }
        ])
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->likes_count = $post->likes->count();
                $post->is_liked = $post->likes->contains('user_id', Auth::id());

                // Format Komentar Utama
                if ($post->comments) {
                    $post->comments->each(function ($comment) {
                        $this->formatCommentData($comment);

                        // Format Balasan (Replies)
                        if ($comment->replies) {
                            $comment->replies_count = $comment->replies->count();
                            $comment->replies->each(function ($reply) {
                                $this->formatCommentData($reply);
                            });
                        } else {
                            $comment->replies_count = 0;
                        }
                    });
                }

                $post->comments_count = $post->comments->sum(function ($c) {
                    return 1 + ($c->replies_count ?? 0);
                });

                // ... (kode format image & avatar user tetap sama seperti sebelumnya) ...
                if ($post->image && !str_starts_with($post->image, 'http')) {
                    $post->image = asset('storage/' . $post->image);
                }
                if ($post->user && $post->user->avatar && !str_starts_with($post->user->avatar, 'http')) {
                    $post->user->avatar = asset('storage/' . $post->user->avatar);
                }

                return $post;
            });

        return response()->json($posts);
    }

    // Helper function untuk format data komentar (agar tidak duplikat kode)
    private function formatCommentData($comment)
    {
        if ($comment->user && $comment->user->avatar && !str_starts_with($comment->user->avatar, 'http')) {
            $comment->user->avatar = asset('storage/' . $comment->user->avatar);
        }
        $comment->time_ago = $comment->created_at->diffForHumans(['short' => true, 'locale' => 'id']);
        $comment->likes_count = $comment->likes->count();
        $comment->is_liked = $comment->likes->contains('user_id', Auth::id());

        // Tambahkan properti UI untuk toggle
        $comment->show_replies = false;
    }

    // 2. Ambil detail 1 postingan (untuk Modal) - Gunakan logika yang sama
    public function show(Post $post)
    {
        $post->load([
            'user:id,name,username,avatar',
            'likes',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user:id,name,username,avatar', 'likes', 'replies.user:id,name,username,avatar', 'replies.likes']);
            }
        ]);

        $post->likes_count = $post->likes->count();
        $post->is_liked = $post->likes->contains('user_id', Auth::id());

        if ($post->image && !str_starts_with($post->image, 'http')) {
            $post->image = asset('storage/' . $post->image);
        }
        if ($post->user && $post->user->avatar && !str_starts_with($post->user->avatar, 'http')) {
            $post->user->avatar = asset('storage/' . $post->user->avatar);
        }

        if ($post->comments) {
            $post->comments->each(function ($comment) {
                $this->formatCommentData($comment);
                if ($comment->replies) {
                    $comment->replies_count = $comment->replies->count();
                    $comment->replies->each(function ($reply) {
                        $this->formatCommentData($reply);
                    });
                } else {
                    $comment->replies_count = 0;
                }
            });
        }

        $post->comments_count = $post->comments->sum(function ($c) {
            return 1 + ($c->replies_count ?? 0);
        });

        return response()->json($post);
    }

    // 3. Buat postingan baru
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'caption' => 'nullable|string|max:2200',
        ]);

        $imagePath = $request->file('image')->store('posts', 'public');

        $post = Post::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'caption' => $request->caption,
        ]);

        // Load user dengan username
        $post->load('user:id,name,username,avatar');

        if ($post->image && !str_starts_with($post->image, 'http')) {
            $post->image = asset('storage/' . $post->image);
        }

        if ($post->user && $post->user->avatar && !str_starts_with($post->user->avatar, 'http')) {
            $post->user->avatar = asset('storage/' . $post->user->avatar);
        }

        $post->likes_count = 0;
        $post->is_liked = false;
        $post->comments_count = 0;

        return response()->json([
            'message' => 'Postingan berhasil dibuat',
            'post' => $post
        ], 201);
    }

    // 4. Hapus postingan
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin.'], 403);
        }

        // Hapus file gambar dari storage
        if ($post->image && !str_starts_with($post->image, 'http')) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Postingan berhasil dihapus']);
    }
}
