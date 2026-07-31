<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // 1. Ambil semua postingan (untuk Feed)
    public function index()
    {
        // Ambil post terbaru, beserta data user, jumlah like, dan jumlah komentar
        $posts = Post::with(['user:id,name,avatar', 'likes', 'comments'])
            ->latest()
            ->get()
            ->map(function ($post) {
                // Tambahkan custom attribute agar frontend mudah membaca
                $post->likes_count = $post->likes->count();
                $post->is_liked = $post->likes->contains('user_id', Auth::id());
                $post->comments_count = $post->comments->count();

                // Format URL gambar agar bisa diakses browser
                $post->image = asset('storage/' . $post->image);

                return $post;
            });

        return response()->json($posts);
    }

    // 2. Buat postingan baru (dengan upload gambar)
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            'caption' => 'nullable|string|max:2200',
        ]);

        // Simpan gambar ke storage/app/public/posts
        $imagePath = $request->file('image')->store('posts', 'public');

        // Buat record post baru
        $post = Post::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'caption' => $request->caption,
        ]);

        // Load relasi user agar langsung terkirim ke frontend
        $post->load('user:id,name,avatar');
        $post->image = asset('storage/' . $post->image);
        $post->likes_count = 0;
        $post->is_liked = false;
        $post->comments_count = 0;

        return response()->json([
            'message' => 'Postingan berhasil dibuat',
            'post' => $post
        ], 201);
    }

    // 3. Hapus postingan (Opsional, tapi bagus untuk hak akses)
    public function destroy(Post $post)
    {
        // Cek hak akses: hanya pemilik post yang bisa hapus
        if ($post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin untuk menghapus postingan ini.'], 403);
        }

        // Hapus gambar dari storage
        Storage::disk('public')->delete($post->image);

        // Hapus record dari database
        $post->delete();

        return response()->json(['message' => 'Postingan berhasil dihapus']);
    }
    // Tambahkan method ini di dalam class PostController
    public function show(Post $post)
    {
        $post->load(['user:id,name,avatar', 'comments.user:id,name,avatar', 'likes']);

        $post->likes_count = $post->likes->count();
        $post->is_liked = $post->likes->contains('user_id', Auth::id());
        $post->comments_count = $post->comments->count();
        $post->image = asset('storage/' . $post->image);

        return response()->json($post);
    }
}

