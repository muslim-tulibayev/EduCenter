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

    public function index()
    {
        $payments = $this->Payment->with('paymentable')->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_payments',
            data: PaymentResource::collection($payments),
            pagination: $payments
        );
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [

    //     ]);
    // }

    public function show(string $id)
    {
        $payment = $this->Payment->with('paymentable')->find($id);

        if (!$payment)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'payment_not_found',
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_payment',
            data: PaymentResource::make($payment)
        );
    }

    // public function update(Request $request, string $id)
    // {
    //     $payment = $this->Payment->find($id);

    //     if (!$payment)
    //         return $this->sendResponse(
    //             success: false,
    //             status: 404,
    //             name: 'payment_not_found',
    //             data: ["id" => $id]
    //         );

    //     $validator = Validator::make($request->all(), [
    //         $table->foreignId('student_id')->constrained()->cascadeOnDelete();
    //         $table->enum('type', ['card', 'cash']);
    //         $table->unsignedBigInteger('amount');
    //         // $table->morphs('paymentable');
    //     ]);

    //     // $payment->update([

    //     // ]);
    // }

    public function destroy(string $id)
    {
        $payment = $this->Payment->find($id);

        if (!$payment)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'payment_not_found',
                data: ["id" => $id]
            );

        $payment->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'payment_deleted',
            data: ["id" => $id]
        );
    }
}
