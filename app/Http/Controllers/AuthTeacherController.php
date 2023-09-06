<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthTeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher');

        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups",
     * summary="myGroups",
     * description="myGroups",
     * operationId="authMyGroups",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function myGroups()
    {
        return response()->json([
            "data" => $this->auth_user->groups
        ]);
    }
}
