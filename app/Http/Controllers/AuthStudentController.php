<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccessForCourse\AccessForCourseResource;
use App\Models\Course;
use App\Traits\PaymentTrait;

class AuthStudentController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/student/my-cards",
     * summary="MyCards",
     * description="MyCards",
     * operationId="myCardsAuthStudent",
     * tags={"AuthStudent"},
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
     * path="/api/student/add-card",
     * summary="Add new card",
     * description="Add Card",
     * operationId="addCardAuthStudent",
     * tags={"AuthStudent"},
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
     * path="/api/student/delete-card/{id}",
     * summary="Delete card",
     * description="Delete card",
     * operationId="deleteCardAuthStudent",
     * tags={"AuthStudent"},
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

    public function __construct()
    {
        $this->middleware('auth:student');

        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/student/my-courses",
     * summary="MyCourses",
     * description="MyCourses",
     * operationId="authMyCourses",
     * tags={"AuthStudent"},
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

    public function myCourses()
    {
        return response()->json([
            "data" => AccessForCourseResource::collection($this->auth_user->accessForCourses)
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/student/all-courses",
     * summary="AllCourses",
     * description="AllCourses",
     * operationId="authAllCourses",
     * tags={"AuthStudent"},
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
        $courses = Course::orderByDesc('id')->paginate();

        return response()->json([
            "data" => $courses
        ]);
    }
}
