<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\Course;
use App\Models\Student;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Throwable;
use DateTime;

class PaymentMethods extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $paymentUrl = 'https://checkout.test.paycom.uz/api';
    private $cashier;

    public function __construct()
    {
        parent::__construct();

        $this->cashier = Cashier::find(1);
    }


    public function cashierId()
    {
        if (!$this->cashier)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'cashier_not_found',
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_cashier_id',
            data: [
                "cashier_id" => $this->cashier->cashier_id
            ]
        );
    }



    public function payForAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'card_id' => 'required|exists:cards,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $student = Student::find($request->student_id);
        $card = $student->cards()->find($request->card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'card_not_found',
            );

        $course = Course::find($request->course_id);
        $rec_create = $this->receiptsCreate($course);

        if (isset($rec_create->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_create_has_error',
                data: $rec_create->error
            );

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            $card->card_token
        );

        if (isset($rec_pay->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_pay_has_error',
                data: $rec_pay->error
            );

        $this->savePayment($student, $course->id, 'card', $course->price);

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'paid_by_admin'
        );
    }

    public function payForParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'card_id' => 'required|exists:cards,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $card = $this->auth_user->cards()->find($request->card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'card_not_found',
            );

        $course = Course::find($request->course_id);

        $rec_create = $this->receiptsCreate($course);

        if (isset($rec_create->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_create_has_error',
                data: $rec_create->error
            );

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            $card->card_token
        );

        if (isset($rec_pay->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_pay_has_error',
                data: $rec_pay->error
            );

        $this->savePayment(
            Student::find($request->student_id),
            $course->id,
            'card',
            $course->price
        );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'paid_by_parent'
        );
    }

    public function payForStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'card_id' => 'required|exists:cards,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $card = $this->auth_user->cards()->find($request->card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'card_not_found',
            );

        $course = Course::find($request->course_id);

        $rec_create = $this->receiptsCreate($course);

        if (isset($rec_create->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_create_has_error',
                data: $rec_create->error
            );

        $rec_pay = $this->receiptsPay(
            $rec_create->result->receipt->_id,
            $card->card_token
        );

        if (isset($rec_pay->error))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'receipts_pay_has_error',
                data: $rec_pay->error
            );

        $this->savePayment($this->auth_user, $course->id, 'card', $course->price);

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'paid_by_student'
        );
    }


    /* Privates */

    private function receiptsCreate($course)
    {
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
            return (object) [
                "error" => $th->getMessage()
            ];
        }
    }

    private function savePayment($student, $course_id, $type, $amount)
    {
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
        } else {
            $student->accessForCourses()
                ->create([
                    'course_id' => $course_id,
                    'pay_time' => $current_time->format('Y-m-d H:i:s'),
                    'expire_time' => $current_time->modify('+1 month')->format('Y-m-d H:i:s'),
                ]);
        }

        $this->auth_user->paymentable()->create([
            "student_id" => $student_id,
            "type" => $type,
            "amount" => $amount,
        ]);
    }
}


    // public function myCards()
    // {
    //     return $this->sendResponse(
    //         success: true,
    //         status: 200,
    //         name: 'get_' . $this->auth_type . '_cards',
    //         data: CardResource::collection($this->auth_user->cards)
    //     );
    // }



    // public function addCard(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'card_number' => 'required|string|min:16|unique:cards,card_number',
    //         'card_expiration' => 'required|string',
    //         'card_token' => 'required|string',
    //     ]);

    //     if ($validator->fails())
    //         return $this->sendValidatorMessages($validator);

    //     $newCard = $this->auth_user->cards()->create([
    //         'card_number' => $request->card_number,
    //         'card_expiration' => $request->card_expiration,
    //         'card_token' => $request->card_token,
    //     ]);

    //     return $this->sendResponse(
    //         success: true,
    //         status: 200,
    //         name: $this->auth_type . '_card_added',
    //         data: ["id" => $newCard->id]
    //     );
    // }



    // public function addCardForAdmin(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'type' => 'required|in:student,parent',
    //         'id' => 'required|numeric|integer',
    //         'card_number' => 'required|string|min:16|unique:cards,card_number',
    //         'card_expiration' => 'required|string',
    //         'card_token' => 'required|string',
    //     ]);

    //     if ($validator->fails())
    //         return $this->sendValidatorMessages($validator);

    //     if ($request->type === 'student')
    //         $user = Student::find($request->id);
    //     elseif ($request->type === 'parent')
    //         $user = Stparent::find($request->id);

    //     if (!$user)
    //         return $this->sendResponse(
    //             success: false,
    //             status: 404,
    //             name: $request->type . '_not_found',
    //         );

    //     $newCard = $user->cards()->create([
    //         'card_number' => $request->card_number,
    //         'card_expiration' => $request->card_expiration,
    //         'card_token' => $request->card_token,
    //     ]);

    //     return $this->sendResponse(
    //         success: true,
    //         status: 200,
    //         name: $request->type . '_card_added',
    //         data: ["id" => $newCard->id]
    //     );
    // }



    // public function deleteCard(string $id)
    // {
    //     $card = $this->auth_user->cards()->find($id);

    //     if (!$card)
    //         return $this->sendResponse(
    //             success: false,
    //             status: 404,
    //             name: 'card_not_found',
    //             data: ["id" => $id]
    //         );

    //     $card->delete();

    //     return $this->sendResponse(
    //         success: true,
    //         status: 200,
    //         name: $this->auth_type . '_card_deleted',
    //         data: ["id" => $id]
    //     );
    // }


    // public function deleteCardForAdmin(string $id)
    // {
    //     $card = $this->auth_user->cards()->find($id);

    //     if (!$card)
    //         return $this->sendResponse(
    //             success: false,
    //             status: 404,
    //             name: 'card_not_found',
    //             data: ["id" => $id]
    //         );

    //     $card->delete();

    //     return $this->sendResponse(
    //         success: true,
    //         status: 200,
    //         name: $this->auth_type . '_card_deleted',
    //         data: ["id" => $id]
    //     );
    // }