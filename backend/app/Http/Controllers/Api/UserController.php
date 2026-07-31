<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // Ambil semua user KECUALI user yang sedang login (untuk suggested)
    public function suggested()
    {
        $users = User::where('id', '!=', Auth::id())
            // PERUBAHAN 1: Tambahkan 'username' di sini
            ->select('id', 'username', 'name', 'email', 'avatar', 'bio')
            ->get();

        // Format URL avatar
        $users->transform(function ($user) {
            if ($user->avatar) {
                $user->avatar = asset('storage/' . $user->avatar);
            }
            return $user;
        });

        return response()->json($users);
    }

    // Ambil data user yang sedang login
    public function profile()
    {
        $user = Auth::user();
        $userData = $user->toArray();

        if ($user->avatar) {
            $userData['avatar'] = asset('storage/' . $user->avatar);
        }

        return response()->json($userData);
    }

    // Ambil postingan milik user yang sedang login
    public function myPosts()
    {
        $user = Auth::user();

        $posts = Post::where('user_id', $user->id)
            ->with('likes', 'comments')
            ->latest()
            ->get()
            ->map(function ($post) {
                $post->likes_count = $post->likes->count();
                $post->comments_count = $post->comments->count();
                $post->image = asset('storage/' . $post->image);
                return $post;
            });

        return response()->json($posts);
    }

    // Update profil user (nama, username, bio, avatar)
    public function update(Request $request)
    {
        $user = Auth::user();

        // PERUBAHAN 2: Tambahkan validasi untuk username
        $request->validate([
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string|max:160',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama
            if ($user->avatar && !str_contains($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        $userData = $user->toArray();
        if ($user->avatar) {
            $userData['avatar'] = asset('storage/' . $user->avatar);
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => $userData
        ]);
    }
}
