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
        $authId = Auth::id();

        // 1. EKSKLUSI user yang sedang login
        $users = User::where('id', '!=', $authId)
            ->select('id', 'username', 'name', 'avatar', 'bio')
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->map(function ($user) use ($authId) {
                $data = $user->toArray();

                // Format avatar
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    $data['avatar'] = asset('storage/' . $user->avatar);
                } else {
                    $data['avatar'] = null;
                }

                // Cek status follow
                $data['is_following'] = Auth::check() ? Auth::user()->isFollowing($user) : false;

                return $data;
            });

        return response()->json($users);
    }

    // Ambil data user yang sedang login
    public function profile()
    {
        $user = Auth::user();

        // Ubah model menjadi array terlebih dahulu
        $userData = $user->toArray();

        // WAJIB: Tambahkan hitungan followers & following secara eksplisit
        // Karena Laravel tidak otomatis mengirim count relasi unless diminta
        $userData['followers_count'] = $user->followers()->count();
        $userData['following_count'] = $user->following()->count();

        // Format URL avatar jika ada
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
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
    // Dapatkan daftar followers user
    // Dapatkan daftar followers user
    public function followers($id)
    {
        $user = User::findOrFail($id);

        // Query eksplisit: cari di tabel 'follows' di mana following_id = $id
        $followers = User::whereIn('id', function ($query) use ($id) {
            $query->select('follower_id')
                ->from('follows')
                ->where('following_id', $id);
        })
            ->select('id', 'username', 'name', 'avatar', 'bio')
            ->latest()
            ->get()
            ->map(function ($user) {
                $data = $user->toArray();
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    $data['avatar'] = asset('storage/' . $user->avatar);
                } else {
                    $data['avatar'] = null;
                }

                // Cek apakah user yang login sedang follow user ini
                $data['is_following'] = Auth::check() && Auth::id() !== $user->id
                    ? Auth::user()->isFollowing($user)
                    : false;

                return $data;
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
            ],
            'followers' => $followers
        ]);
    }

    // Dapatkan daftar following user
    public function following($id)
    {
        $user = User::findOrFail($id);

        // Query eksplisit: cari di tabel 'follows' di mana follower_id = $id
        $following = User::whereIn('id', function ($query) use ($id) {
            $query->select('following_id')
                ->from('follows')
                ->where('follower_id', $id);
        })
            ->select('id', 'username', 'name', 'avatar', 'bio')
            ->latest()
            ->get()
            ->map(function ($user) {
                $data = $user->toArray();
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    $data['avatar'] = asset('storage/' . $user->avatar);
                } else {
                    $data['avatar'] = null;
                }

                // Cek apakah user yang login sedang follow user ini
                $data['is_following'] = Auth::check() && Auth::id() !== $user->id
                    ? Auth::user()->isFollowing($user)
                    : false;

                return $data;
            });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
            ],
            'following' => $following
        ]);
    }
}
