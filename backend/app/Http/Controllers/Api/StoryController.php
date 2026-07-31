<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoryController extends Controller
{
    // Ambil semua story aktif (untuk stories bar)
    public function index()
    {
        $stories = Story::active()
            ->with('user:id,username,name,avatar')
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(function ($userStories) {
                $user = $userStories->first()->user;

                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    $user->avatar = asset('storage/' . $user->avatar);
                }

                $userStories->transform(function ($story) {
                    if (!str_starts_with($story->image, 'http')) {
                        $story->image = asset('storage/' . $story->image);
                    }
                    $story->time_ago = $story->created_at->diffForHumans(['short' => true, 'locale' => 'id']);
                    return $story;
                });

                return [
                    'user' => $user,
                    'stories' => $userStories->values(),
                    'count' => $userStories->count()
                ];
            })
            ->values();

        return response()->json($stories);
    }

    // Upload story baru
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'caption' => 'nullable|string|max:200',
        ]);

        $imagePath = $request->file('image')->store('stories', 'public');

        $story = Story::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'caption' => $request->caption,
            'expires_at' => now()->addHours(24), // Story aktif 24 jam
        ]);

        $story->load('user:id,username,name,avatar');

        if (!str_starts_with($story->image, 'http')) {
            $story->image = asset('storage/' . $story->image);
        }

        if ($story->user && $story->user->avatar && !str_starts_with($story->user->avatar, 'http')) {
            $story->user->avatar = asset('storage/' . $story->user->avatar);
        }

        return response()->json([
            'message' => 'Story berhasil ditambahkan',
            'story' => $story
        ], 201);
    }

    // Hapus story (hanya pemilik)
    public function destroy(Story $story)
    {
        if ($story->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak memiliki izin.'], 403);
        }

        if (!str_starts_with($story->image, 'http')) {
            Storage::disk('public')->delete($story->image);
        }

        $story->delete();

        return response()->json(['message' => 'Story berhasil dihapus']);
    }

    // Hapus story yang sudah expired (bisa dipanggil via scheduler/cron)
    public function cleanupExpired()
    {
        $expiredStories = Story::where('expires_at', '<=', now())->get();

        foreach ($expiredStories as $story) {
            if (!str_starts_with($story->image, 'http')) {
                Storage::disk('public')->delete($story->image);
            }
            $story->delete();
        }

        return response()->json(['message' => 'Story expired berhasil dibersihkan']);
    }
}
