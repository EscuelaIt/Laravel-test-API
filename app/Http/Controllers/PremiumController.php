<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PremiumController extends Controller
{
    public function access() {
        $user = Auth::user();
        if ($user->tokenCan('premium')) {
            return response()->json([
                'message' => 'ok',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Lo sentimos, no eres premium',
            ], 403);
        }
    }

    public function setPremium() {
        $user = Auth::user();
        $user->is_premium = true;
        $user->save();
        return response()->json([
            'message' => 'Ahora eres premium',
            'token' => $user->createToken("API ACCESS TOKEN", ['premium'])->plainTextToken
        ], 200);
    }
}
