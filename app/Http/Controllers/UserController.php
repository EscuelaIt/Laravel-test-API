<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

   /**
     * @OA\Get(
     *     path="/api/user",
     *     tags={"user"},
     *     @OA\Parameter(ref="#/components/parameters/acceptJsonHeader"),
     *     @OA\Parameter(ref="#/components/parameters/requestedWith"),
     *     @OA\Response(
     *          response="200", 
     *          description="User Data",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="No autorizado",
     *          @OA\JsonContent(ref="#/components/responses/UnauthenticatedResponse")
     *     ),
     *     security={
     *         {"BearerAuth": {}}
     *     }
     * )
     */

    public function getUser (Request $request) {
        return $request->user();
    }
}
