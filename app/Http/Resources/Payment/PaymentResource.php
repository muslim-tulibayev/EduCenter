<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "student_id" => $this->student_id,
            "type" => $this->type,
            "amount" => $this->amount,
            "created_at" => $this->created_at,
            "payer" => $this->whenLoaded('paymentable', function () {
                return [
                    "id" => $this->paymentable->id,
                    "type" => str_replace('App\\Models\\', '', $this->paymentable_type),
                    "firstname" => $this->paymentable->firstname,
                    "lastname" => $this->paymentable->lastname,
                    "email" => $this->paymentable->email,
                    "contact" => $this->paymentable->contact,
                ];
            }),
        ];
    }
}
