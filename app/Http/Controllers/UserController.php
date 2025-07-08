<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil direset',
        ]);
    }
}