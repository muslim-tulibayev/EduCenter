<?php

namespace App\Traits;

use App\Http\Resources\Card\CardResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait PaymentTrait
{
    public function myCards()
    {
        return response()->json([
            "data" => CardResource::collection($this->auth_user->cards)
        ]);
    }



    public function addCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|unique:cards,card_number',
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $card = $this->auth_user->cards()->create([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return response()->json([
            "message" => "Card has been successfully added",
            "card_id" => $card->id
        ]);
    }



    public function deleteCard(string $id)
    {
        $card = $this->auth_user->cards()->find($id);

        if (!$card)
            return response()->json([
                "error" => "not found"
            ], 422);

        $card->delete();

        return response()->json([
            "message" => "Card has been successfully deleted"
        ]);
    }
}
