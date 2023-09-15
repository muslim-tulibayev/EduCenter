<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\PaymentMethods;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Student\StudentResource;
use App\Models\Course;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthParentController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $payment;

    public function __construct()
    {
        $this->middleware('auth:parent');
        $this->payment = new PaymentMethods();
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
            data: CourseResource::collection($courses),
            pagination: $courses
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-cards",
     * summary="MyCards",
     * description="MyCards",
     * operationId="authParentMyCards",
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

    public function myCards()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            data: CardResource::collection($this->auth_user->cards)
        );
    }

    /**
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
            message: trans('msg.added', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $newCard->id]
        );
    }

    /**
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

    public function deleteCard(string $id)
    {
        return $this->payment->deleteCard($id);
    }

    /**
     * @OA\Get(
     * path="/api/parent/cashier",
     * summary="Get Cashier",
     * description="GetCashier",
     * operationId="authParentGetCashier",
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

    public function cashierId()
    {
        return $this->payment->cashierId();
    }

    /**
     * @OA\Post(
     * path="/api/parent/pay-for-course",
     * summary="Pay for course",
     * description="Pay for course",
     * operationId="payForCourseAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"student_id", "course_id", "card_id"},
     *       @OA\Property(property="student_id", type="numeric", example=1),
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
        return $this->payment->payForParent($request);
    }
}
