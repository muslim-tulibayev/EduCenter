<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\PaymentMethods;
use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    private $payment;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->payment = new PaymentMethods();
        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/user/statistics",
     * summary="statistics",
     * description="statistics",
     * operationId="authUserStatistics",
     * tags={"AuthUser"},
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

    public function statistics()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_statistics',
            data: "this_statistics_route"
        );
    }

    /**
     * @OA\Get(
     * path="/api/user/cashier",
     * summary="Get Cashier",
     * description="GetCashier",
     * operationId="getCashierAuthUser",
     * tags={"AuthUser"},
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
     * path="/api/user/pay-for-course",
     * summary="Pay for course",
     * description="Pay for course",
     * operationId="payForCourseAuthUser",
     * tags={"AuthUser"},
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
        return $this->payment->payForAdmin($request);
    }
}
