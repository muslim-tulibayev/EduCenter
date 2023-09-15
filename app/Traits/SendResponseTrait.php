<?php

namespace App\Traits;

trait SendResponseTrait
{
    public function sendResponse(bool $success, int $status, string $message = null, $data = null, object $pagination = null)
    {
        $res = [
            "success" => $success,
            "status" => $status,
            // "name" => $name,
            "message" => $message,
            "data" => $data,
            "pagination" => null,
        ];

        if ($pagination)
            $res["pagination"] = [
                "current_page" => $pagination->currentPage(),
                "last_page" => $pagination->lastPage(),
                "per_page" => $pagination->perPage(),
                "total" => $pagination->total(),
            ];

        return response()->json($res);
    }
}
