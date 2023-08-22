<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy']]);
    }

    /**
     * @OA\Get(
     * path="/api/session",
     * summary="Session index",
     * description="Get all Sessions data",
     * operationId="indexSession",
     * tags={"Session"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        return response()->json(Session::all());
    }

    /**
     * @OA\Post(
     * path="/api/session",
     * summary="Session store",
     * description="Set new Session",
     * operationId="storeSession",
     * tags={"Session"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass session credentials",
     *    @OA\JsonContent(
     *       required={"from","to"},
     *       @OA\Property(property="from", type="string", format="text", example="08:30"),
     *       @OA\Property(property="to", type="string", format="text", example="16:30"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "from" => 'required|date_format:H:i',
            "to" => 'required|date_format:H:i|after:from',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        // check duration for uniqueness

        $newSession = Session::create([
            "duration" => $request->from . ' - ' . $request->to
        ]);

        return response()->json([
            "message" => "New session has been created successfully",
            "session_id" => $newSession->id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/session/{id}",
     * summary="Session show",
     * description="Get specific session data",
     * operationId="showSession",
     * tags={"Session"},
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
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function show(string $id)
    {
        $session = Session::find($id);

        if (!$session)
            return response()->json([
                "error" => "not found"
            ]);

        return response()->json([
            "data" => $session
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/session/{id}",
     * summary="Session update",
     * description="Update specific session",
     * operationId="updateSession",
     * tags={"Session"},
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
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"from","to"},
     *       @OA\Property(property="from", type="string", format="text", example="08:30"),
     *       @OA\Property(property="to", type="string", format="text", example="16:30"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        $session = Session::find($id);

        if (!$session)
            return response()->json([
                "error" => "not found"
            ]);

        $validator = Validator::make($request->all(), [
            "from" => 'required|date_format:H:i',
            "to" => 'required|date_format:H:i|after:from',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        // check duration for uniqueness

        $session->update([
            "duration" => $request->from . ' - ' . $request->to
        ]);

        return response()->json([
            "message" => "New session has been updated successfully",
            "session_id" => $session->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/session/{id}",
     * summary="Session delete",
     * description="Delete specific session",
     * operationId="destroySession",
     * tags={"Session"},
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
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function destroy(string $id)
    {
        $session = Session::find($id);

        if (!$session)
            return response()->json([
                "error" => "not found"
            ]);

        $session->delete();

        return response()->json([
            "message" => "Session has been deleted successfully",
            "session_id" =>$id
        ]);
    }
}
