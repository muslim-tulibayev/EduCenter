<?php

namespace App\Traits;

trait SendValidatorMessagesTrait
{
    public function sendValidatorMessages($validator)
    {
        $res = [];

        //! fix this
        foreach ($validator->messages()->toArray() as $key => $value)
            return response()->json([
                "success" => false,
                "status" => 400,
                "message" => $value[0],
                "data" => null,
                "pagination" => null,
            ]);
    }
}
