<?php

namespace App\Traits;

trait SendValidatorMessagesTrait
{
    public function sendValidatorMessages($validator)
    {
        $res = [];

        foreach ($validator->messages()->toArray() as $key => $value)
            // $res[] = [
            //     "field" => $key,
            //     "message" => $value[0],
            // ];

        return response()->json([
            "success" => false,
            "status" => 400,
            // "name" => 'validation_error',
            "message" => $value[0],
            "data" => null,
            "pagination" => null,
        ]);
    }
}
