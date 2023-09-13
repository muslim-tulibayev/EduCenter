<?php

namespace App\Http\Resources\Card;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'card_number' => $this->card_number,
            'card_expiration' =>$this->card_expiration,

            // "id" => $this->id,
            // "cardable_type" => $this->cardable_type,
            // "cardable_id" => $this->cardable_id,
            // "card_number" => $this->card_number,
            // "card_expiration" => $this->card_expiration
        ];
    }
}
