<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Schedule\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy']]);
    }

    /**
     * @OA\Get(
     * path="/api/schedule",
     * summary="Get all schedules data",
     * description="Schedule index",
     * operationId="indexSchedule",
     * tags={"Schedule"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $schedules = Schedule::with('group', 'weekday', 'session', 'room.branch')->orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return ScheduleResource::collection($schedules);

        return ScheduleResource::collection($schedules);

        // return response()->json($schedules);
    }

    /**
     * @OA\Post(
     * path="/api/schedule",
     * summary="Set new schedule",
     * description="Schedule store",
     * operationId="storeSchedule",
     * tags={"Schedule"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"group_id", "weekday_id", "session_id", "room_id"},
     *       @OA\Property(property="group_id", type="numeric", example=1),
     *       @OA\Property(property="weekday_id", type="numeric", example=1),
     *       @OA\Property(property="session_id", type="numeric", example=1),
     *       @OA\Property(property="room_id", type="numeric", example=1),
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

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "group_id" => 'required|numeric|exists:groups,id',
            "weekday_id" => 'required|numeric|exists:weekdays,id',
            "session_id" => 'required|numeric|exists:sessions,id',
            "room_id" => 'required|numeric|exists:rooms,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newSchedule = Schedule::create(
            $validator->validated()
        );

        auth('api')->user()->makeChanges(
            'New schedule created',
            'created',
            $newSchedule
        );

        return response()->json([
            "message" => "Schedule created successfully",
            "schedule" => $newSchedule->id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/schedule/{id}",
     * summary="Get specific schedule data",
     * description="Schedule show",
     * operationId="showSchedule",
     * tags={"Schedule"},
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
        // $schedule = Schedule::find($id);
        $schedule = Schedule::with('group', 'weekday', 'session', 'room.branch')->find($id);
        // Schedule::with('group', 'weekday', 'session', 'room.branch')->orderByDesc('id')->paginate();

        if ($schedule === null)
            return response()->json(["error" => "Not found"]);

        // if (auth('api')->user())
        //     return new ScheduleResource($schedule);

        return new ScheduleResource($schedule);
    }

    /**
     * @OA\Put(
     * path="/api/schedule/{id}",
     * summary="Update specific schedule",
     * description="Schedule update",
     * operationId="updateSchedule",
     * tags={"Schedule"},
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
     *       required={"group_id", "weekday_id", "session_id", "room_id"},
     *       @OA\Property(property="group_id", type="numeric", example=1),
     *       @OA\Property(property="weekday_id", type="numeric", example=1),
     *       @OA\Property(property="session_id", type="numeric", example=1),
     *       @OA\Property(property="room_id", type="numeric", example=1),
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

    public function update(Request $req, string $id)
    {
        $schedule = Schedule::find($id);

        if ($schedule === null)
            return response()->json(["error" => "Not found"], 422);

        $validator = Validator::make($req->all(), [
            "group_id" => 'required|numeric|exists:groups,id',
            "weekday_id" => 'required|numeric|exists:weekdays,id',
            "session_id" => 'required|numeric|exists:sessions,id',
            "room_id" => 'required|numeric|exists:rooms,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newSchedule = Schedule::create(
            $validator->validated()
        );

        auth('api')->user()->makeChanges(
            'Schedule updated from $val1 to $val2',
            '$col-name',
            $schedule
        );

        return response()->json([
            "message" => "Schedule updated successfully",
            "schedule" => $newSchedule->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/schedule/{id}",
     * summary="Schedule delete",
     * description="Delete specific Schedule",
     * operationId="destroySchedule",
     * tags={"Schedule"},
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
        $schedule = Schedule::find($id);

        if ($schedule === null)
            return response()->json(["error" => "Not found"]);

        $schedule->delete();

        auth('api')->user()->makeChanges(
            'Schedule deleted',
            'deleted',
            $schedule
        );

        return response()->json([
            "message" => "Schedule deleted successfully",
            "schedule" => $id,
        ]);
    }
}
