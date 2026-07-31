<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    // Follow / Unfollow User
    public function toggleFollow(User $user)
    {
        $authUser = Auth::user();

        // Tidak bisa follow diri sendiri
        if ($authUser->id === $user->id) {
            return response()->json(['message' => 'Anda tidak bisa mengikuti diri sendiri.'], 400);
        }

        $isFollowing = $authUser->isFollowing($user);

        if ($isFollowing) {
            // Unfollow
            $authUser->following()->detach($user->id);
            $status = false;
        } else {
            // Follow
            $authUser->following()->attach($user->id);
            $status = true;
        }

        return response()->json([
            'is_following' => $status,
            'followers_count' => $user->followers()->count()
        ]);
    }

    // Cek status follow
    public function checkStatus(User $user)
    {
        return response()->json([
            'is_following' => Auth::user()->isFollowing($user)
        ]);
    }
}
