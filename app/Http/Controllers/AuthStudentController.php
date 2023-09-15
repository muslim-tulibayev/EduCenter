<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\PaymentMethods;
use App\Http\Resources\AccessForCourse\AccessForCourseResource;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Models\Course;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthStudentController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $payment;

    public function __construct()
    {
        $this->middleware('auth:student');
        $this->payment = new PaymentMethods();
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
        $access_for_courses = $this->auth_user->accessForCourses()->with('course')->get();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_my_courses',
            data: AccessForCourseResource::collection($access_for_courses)
        );
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
        // if ($request->has('branch_filter'))

        $courses = Course::orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_all_courses',
            data: CourseResource::collection($courses),
            pagination: $courses
        );
    }

    /**
     * @OA\Get(
     * path="/api/student/course/{id}/lessons",
     * summary="CourseLessons",
     * description="CourseLessons",
     * operationId="authCourseLessons",
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
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function lessons(string $id)
    {
        $accessForCourse = $this->auth_user->accessForCourses()->where('course_id', $id)->first();

        if (!$accessForCourse)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'lessons_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')])
            );

        $lessons = $accessForCourse->course->lessons()
            // ->with('course')
            ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_lessons',
            data: LessonResource::collection($lessons),
            pagination: $lessons
        );
    }

    /**
     * @OA\Get(
     * path="/api/student/my-cards",
     * summary="MyCards",
     * description="MyCards",
     * operationId="authStudentMyCards",
     * tags={"AuthStudent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function myCards()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_' . $this->auth_type . '_cards',
            data: CardResource::collection($this->auth_user->cards)
        );
    }

    /**
     * @OA\Post(
     * path="/api/student/add-card",
     * summary="Add new card",
     * description="Add Card",
     * operationId="addCardAuthStudent",
     * tags={"AuthStudent"},
     * security={ {"bearerAuth": {} }},
     * 
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

    public function addCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|unique:cards,card_number',
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newCard = $this->auth_user->cards()->create([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: $this->auth_type . '_card_added',
            message: trans('msg.added', ['attribute' => __('msg.attributes.student')]),
            data: ["id" => $newCard->id]
        );
    }

    /**
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

    public function deleteCard(string $id)
    {
        return $this->payment->deleteCard($id);
    }

    /**
     * @OA\Get(
     * path="/api/student/cashier",
     * summary="Get Cashier",
     * description="GetCashier",
     * operationId="authStudentGetCashier",
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

    public function cashierId()
    {
        return $this->payment->cashierId();
    }

    /**
     * @OA\Post(
     * path="/api/student/pay-for-course",
     * summary="Pay for course",
     * description="Pay for course",
     * operationId="payForCourseAuthStudent",
     * tags={"AuthStudent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"course_id", "card_id"},
     *       @OA\Property(property="course_id", type="numeric", example=1),
     *       @OA\Property(property="card_id", type="numeric", example=1),
     *    ),
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

    public function payForCourse(Request $request)
    {
        return $this->payment->payForStudent($request);
    }
}
