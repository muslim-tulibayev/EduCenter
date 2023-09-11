<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Student\StudentResource;
use App\Models\Course;
use App\Traits\PaymentTrait;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;

class AuthParentController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/parent/my-cards",
     * summary="MyCards",
     * description="MyCards",
     * operationId="authMyCards",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * ),
     * 
     * @OA\Post(
     * path="/api/parent/add-card",
     * summary="Add new card",
     * description="Add Card",
     * operationId="addCardAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"card_number", "card_expiration", "card_token"},
     *       @OA\Property(property="card_number", type="string", example="1234567890123456"),
     *       @OA\Property(property="card_expiration", type="string", example="01/25"),
     *       @OA\Property(property="card_token", type="string", example="4345678987653773hgfkdfu34hf3fhiuerifr4345678987653773hgfkdfu34hf3fhiuerifr4345678987653773hgfkdfu34hf3fhiuerifr"),
     *    ),
     * ),
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * ),
     * 
     * @OA\Delete(
     * path="/api/parent/delete-card/{id}",
     * summary="Delete card",
     * description="Delete card",
     * operationId="deleteCardAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Parameter(
     *    in="path",
     *    name="id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     *
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    use PaymentTrait;
    use SendResponseTrait, SendValidatorMessagesTrait;

    public function __construct()
    {
        $this->middleware('auth:parent');

        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children",
     * summary="myChildren",
     * description="myChildren",
     * operationId="authMyChildren",
     * tags={"AuthParent"},
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

    public function myChildren()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_my_childrens',
            data: StudentResource::collection($this->auth_user->students)
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/all-courses",
     * summary="AllCourses",
     * description="AllCourses",
     * operationId="authParentAllCourses",
     * tags={"AuthParent"},
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

    public function allCourses()
    {
        // if ($request->has('branch_filter'))

        $courses = Course::orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_all_courses',
            data: CourseResource::collection($courses),
            pagination: $courses
        );
    }
}
