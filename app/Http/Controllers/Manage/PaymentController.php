<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Payment;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;

class PaymentController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $Payment;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');
        parent::__construct('payments', true);

        $this->middleware(function ($request, $next) {
            $this->Payment = Payment::whereHas('student.groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            });

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/payment",
     * summary="Get all Payments data",
     * description="Payment index",
     * operationId="indexPayment",
     * tags={"Payment"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $payments = $this->Payment->with('student', 'paymentable')->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_payments',
            data: PaymentResource::collection($payments),
            pagination: $payments
        );
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [

    //     ]);
    // }

    /**
     * @OA\Get(
     * path="/api/manage/payment/{id}",
     * summary="Get specific Payment data",
     * description="Payment show",
     * operationId="showPayment",
     * tags={"Payment"},
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

    public function show(string $id)
    {
        $payment = $this->Payment->with('student', 'paymentable')->find($id);

        if (!$payment)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'payment_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.payment')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_payment',
            data: PaymentResource::make($payment)
        );
    }

    // public function update(Request $request, string $id)
    // {
    // 
    // }

    /**
     * @OA\Delete(
     * path="/api/manage/payment/{id}",
     * summary="Delete specific Payment",
     * description="Payment delete",
     * operationId="destroyPayment",
     * tags={"Payment"},
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

    public function destroy(string $id)
    {
        $payment = $this->Payment->find($id);

        if (!$payment)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'payment_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.payment')]),
                data: ["id" => $id]
            );

        $payment->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'payment_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.payment')]),
            data: ["id" => $id]
        );
    }
}
