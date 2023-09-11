<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResource;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;

class AuthTeacherController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

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
        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_my_groups',
            data: GroupResource::collection($this->auth_user->groups)
        );
    }
}
