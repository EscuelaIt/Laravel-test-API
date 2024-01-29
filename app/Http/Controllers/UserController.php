<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/user",
     *     @OA\Response(response="200", description="User Data"),
     *     tags={"user"}
     * )
     */

    public function getUser (Request $request) {
        return $request->user();
    }
}
