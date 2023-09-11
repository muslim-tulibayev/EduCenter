<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/user/statistics",
     * summary="statistics",
     * description="statistics",
     * operationId="authUserStatistics",
     * tags={"AuthUser"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function statistics()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_statistics',
            data: "this_statistics_route"
        );
    }
}
