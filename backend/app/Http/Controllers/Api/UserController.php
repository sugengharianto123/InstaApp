<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Ambil semua user KECUALI user yang sedang login (untuk suggested)
    public function suggested()
    {
        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email', 'avatar', 'bio')
            ->get();

        return response()->json($users);
    }

    // Ambil data user yang sedang login (lengkap)
    public function profile()
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
