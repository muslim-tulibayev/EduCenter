<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Session\SessionResource;
use App\Models\Branch;
use App\Models\Session;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Session;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('sessions', true);

        $this->middleware(function ($request, $next) {
            $this->Session = Branch::find($this->auth_branch_id)->sessions();

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/session",
     * summary="Session index",
     * description="Get all Sessions data",
     * operationId="indexSession",
     * tags={"Session"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $sessions = $this->Session->orderByDesc('start')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_sessions',
            data: SessionResource::collection($sessions),
            pagination: $sessions
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/session",
     * summary="Session store",
     * description="Set new Session",
     * operationId="storeSession",
     * tags={"Session"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass session credentials",
     *    @OA\JsonContent(
     *       required={"start","end"},
     *       @OA\Property(property="start", type="string", format="text", example="08:30"),
     *       @OA\Property(property="end", type="string", format="text", example="16:30"),
     *       @OA\Property(
     *         property="branches", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
     *    ),
     * ),
     * 
     * @OA\Response(
     *    response=403,
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
            "start" => 'required|date_format:H:i:s',
            "end" => [
                'required',
                'date_format:H:i:s',
                'after:start',
                Rule::unique('sessions')->where(function ($query) use ($request) {
                    return $query->where('start', $request->start);
                }),
            ],
            "branches" => 'array',
            "branches.*" => 'numeric|distinct|exists:branches,id'
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newSession = Session::create([
            "start" => $request->start,
            "end" => $request->end
        ]);

        if ($request->has('branches'))
            $newSession->branches()->attach($request->branches);

        return $this->sendResponse(
            success: true,
            status: 201,
            name: 'session_created',
            data: ["id" => $newSession->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/session/{id}",
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
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function show(string $id)
    {
        $session = $this->Session->find($id);

        if (!$session)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'session_not_found',
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_session',
            data: SessionResource::make($session)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/session/{id}",
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
     *    description="Pass session credentials",
     *    @OA\JsonContent(
     *       required={"start","end"},
     *       @OA\Property(property="start", type="string", format="text", example="08:30"),
     *       @OA\Property(property="end", type="string", format="text", example="16:30"),
     *       @OA\Property(
     *         property="branches", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
     *    ),
     * ),
     * 
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function update(Request $request, string $id)
    {
        $session = $this->Session->find($id);

        if (!$session)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'session_not_found',
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "start" => 'required|date_format:H:i:s',
            "end" => [
                'required',
                'date_format:H:i:s',
                'after:start',
                Rule::unique('sessions')->ignore($id)->where(function ($query) use ($request) {
                    return $query->where('start', $request->start);
                }),
            ],
            "branches" => 'array',
            "branches.*" => 'numeric|distinct|exists:branches,id'
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $session->update([
            "start" => $request->start,
            "end" => $request->end,
        ]);

        if ($request->has('branches'))
            $session->branches()->sync($request->branches);

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'session_updated',
            data: ["id" => $session->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/session/{id}",
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
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function destroy(string $id)
    {
        $session = $this->Session->find($id);

        if (!$session)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'session_not_found',
                data: ["id" => $id]
            );

        $session->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'session_deleted',
            data: ["id" => $session->id]
        );
    }
}
