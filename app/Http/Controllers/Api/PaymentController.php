<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\Course;
use App\Models\Stparent;
use App\Models\Student;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PaymentController extends Controller
{
    private $paymentUrl = 'https://checkout.test.paycom.uz/api';
    private $cashier;

    public function __construct()
    {
        $this->middleware('auth:api,parent,student');
        // $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy', 'changeStudents']]);
        parent::__construct();

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['payment_addcard'] >= 1))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('addCard');

        // Change this (Create CashierContoller)
        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['payment_cashier'] >= 1))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('cashierId');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['payment_pay'] >= 1))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('pay');

        $this->cashier = Cashier::find(1);
    }

    /**
     * @OA\Post(
     * path="/api/payment/addcard",
     * summary="Add new card for parent or student",
     * description="Payment store",
     * operationId="addCardPayment",
     * tags={"Payment"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass payment credentials",
     *    @OA\JsonContent(
     *       required={"payment_token"},
     *       @OA\Property(property="type", type="string", example="student"),
     *       @OA\Property(property="id", type="numeric", example=1),
     *       @OA\Property(property="payment_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiyJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjEzNzo4MDAwL2FwaS91c2VyL2VtYWlsdmVyaWZpY2F0aW9uIiwiaWF0IjoxNjkyOTYyMTI5LCJleHAiOjE2OTMwMDUzMj"),
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
        if (auth('api')->user())
            return $this->addCardForAdmin($request);
        else
            return $this->addCardForOthers($request);
    }

    /**
     * @OA\Get(
     * path="/api/payment/cashier",
     * summary="Get cashier's id",
     * description="Cashier id",
     * operationId="cashierId",
     * tags={"Payment"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function cashierId()
    {
        return response()->json([
            'cashier_id' => $this->cashier->cashier_id
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/payment/pay",
     * summary="Buy new courses or extend their expire date",
     * description="Payment pay",
     * operationId="payPayment",
     * tags={"Payment"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass payment credentials",
     *    @OA\JsonContent(
     *       required={"course_id"},
     *       @OA\Property(property="student_id", type="numeric", example=1),
     *       @OA\Property(property="course_id", type="numeric", example=1),
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

    public function pay(Request $request)
    {
        if (auth('api')->user())
            return $this->payForAdmin($request);
        else if (auth('parent')->user())
            return $this->payForParent($request);
        else
            return $this->payForStudent($request);
    }

    private function payForAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        if (!$student = Student::find($request->student_id))
            return response()->json([
                "error" => "card not found"
            ], 400);

        $rec_create = $this->receiptsCreate($request->course_id);

        if (isset($rec_create->error))
            return response()->json([
                "error" => $rec_create->error
            ]);

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            $student->payment_token
        );

        if (isset($rec_pay->error))
            return response()->json([
                "error" => $rec_pay->error
            ]);

        return response()->json([
            "message" => $this->savePayment($request->student_id, $request->course_id)
        ]);
    }

    private function payForParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        if (!auth('parent')->user()->payment_token)
            return response()->json([
                "error" => "card not found"
            ], 400);

        $rec_create = $this->receiptsCreate($request->course_id);

        if (isset($rec_create->error))
            return response()->json([
                "error" => $rec_create->error
            ]);

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            auth('parent')->user()->payment_token
        );

        if (isset($rec_pay->error))
            return response()->json([
                "error" => $rec_pay->error
            ]);

        return response()->json([
            "message" => $this->savePayment($request->student_id, $request->course_id)
        ]);
    }

    private function payForStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        if (!auth('student')->user()->payment_token)
            return response()->json([
                "error" => "card not found"
            ], 400);

        $rec_create = $this->receiptsCreate($request->course_id);

        if (isset($rec_create->error))
            return response()->json([
                "error" => $rec_create->error
            ]);

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            auth('student')->user()->payment_token
        );

        if (isset($rec_pay->error))
            return response()->json([
                "error" => $rec_pay->error
            ]);

        return response()->json([
            "message" => $this->savePayment(auth('student')->user()->id, $request->course_id)
        ]);
    }

    private function savePayment($student_id, $course_id)
    {
        $student = Student::find($student_id);
        $current_time = new DateTime();
        $access = $student->accessForCourses()
            ->where('course_id', $course_id)
            ->first();

        if ($access) {
            $expire_time = new DateTime($access->expire_time);
            $access->update([
                'pay_time' => $current_time->format('Y-m-d H:i:s'),
                'expire_time' => $expire_time->modify('+1 month')->format('Y-m-d H:i:s'),
            ]);
            return 'paid';
        } else {
            $student->accessForCourses()
                ->create([
                    'course_id' => $course_id,
                    'pay_time' => $current_time->format('Y-m-d H:i:s'),
                    'expire_time' => $current_time->modify('+1 month')->format('Y-m-d H:i:s'),
                ]);
            return 'bought';
        }
    }

    private function receiptsCreate($course_id)
    {
        $course = Course::find($course_id);

        $headers = [
            'X-Auth' => $this->cashier->cashier_id,
            'Content-Type' => 'application/json',
        ];

        $data = [
            "id" => 4,
            "method" => "receipts.create",
            "params" => [
                "amount" => $course->price,
                // "amount" => 1000000,
                "account" => [
                    "order_id" => "test"
                ],
                "detail" => [
                    "receipt_type" => 0,
                    "items" => [
                        [
                            // "discount" => 10000,
                            "title" => $course->name,
                            "price" => $course->price,
                            "count" => 1,
                            "code" => "00702001001000001",
                            "vat_percent" => 15,
                            "package_code" => "123456"
                        ]
                    ]
                ]
            ]
        ];

        try {
            $res = Http::withHeaders($headers)
                ->post($this->paymentUrl, $data)
                ->body();

            return json_decode($res);
        } catch (Throwable $th) {
            return (object)[
                "error" => $th->getMessage()
            ];
        }
    }

    private function receiptsPay($receipt_id, $payment_token)
    {
        $headers = [
            'X-Auth' => $this->cashier->cashier_id . ':' . $this->cashier->cashier_key,
            'Content-Type' => 'application/json',
        ];

        $data = [
            "id" => 123,
            "method" => "receipts.pay",
            "params" => [
                "id" => $receipt_id,
                "token" => $payment_token,
                // "payer" => [
                //     "phone" => "998901304527"
                // ]
            ]
        ];

        try {
            $res = Http::withHeaders($headers)
                ->post($this->paymentUrl, $data)
                ->body();

            return json_decode($res);
        } catch (Throwable $th) {
            return (object)[
                "error" => $th->getMessage()
            ];
        }
    }

    private function addCardForAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:student,parent',
            'id' => 'required|numeric|integer',
            'payment_token' => 'required|string'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 422);

        if ($request->type === 'student') {
            $student = Student::find($request->id);

            if (!$student)
                return response()->json([
                    'error' => $request->type . ' not found'
                ], 422);

            $student->update([
                'payment_token' => $request->payment_token
            ]);
        } elseif ($request->type === 'parent') {
            $parent = Stparent::find($request->id);

            if (!$parent)
                return response()->json([
                    'error' => $request->type . ' not found'
                ], 422);

            $parent->update([
                'payment_token' => $request->payment_token
            ]);
        }

        return response()->json([
            'message' => $request->type . '\'s card added successfully'
        ]);
    }

    private function addCardForOthers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_token' => 'required|string'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 422);

        auth()->user()->update([
            'payment_token' => $request->payment_token
        ]);

        return response()->json([
            'message' => 'Card added successfully'
        ]);
    }
}
