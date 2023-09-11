<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Schedule\ScheduleResource;
use App\Models\Schedule;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Schedule;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');

        parent::__construct('schedules', true);

        $this->middleware(function ($request, $next) {
            $this->Schedule = Schedule::whereHas('room', function ($query) {
                $query->where('branch_id', $this->auth_branch_id);
            });

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/schedule",
     * summary="Get all schedules data",
     * description="Schedule index",
     * operationId="indexSchedule",
     * tags={"Schedule"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        // $schedules = Schedule::with('group', 'weekday', 'session', 'room.branch')->orderByDesc('id')->paginate();
        $schedules = $this->Schedule->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_schedules',
            data: ScheduleResource::collection($schedules),
            pagination: $schedules
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/schedule",
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
        // Check for group_id, session_id, room_id for specific branch
        $validator = Validator::make($request->all(), [
            "weekday_id" => 'required|numeric|exists:weekdays,id',
            "group_id" => 'required|numeric|exists:groups,id',
            "session_id" => 'required|numeric|exists:sessions,id',
            "room_id" => 'required|numeric|exists:rooms,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newSchedule = Schedule::create([
            "weekday_id" => $request->weekday_id,
            "group_id" => $request->group_id,
            "session_id" => $request->session_id,
            "room_id" => $request->room_id,
        ]);

        // auth('api')->user()->makeChanges(
        //     'New schedule created',
        //     'created',
        //     $newSchedule
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'schedule_created',
            data: ["id" => $newSchedule->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/schedule/{id}",
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
        // $schedule = Schedule::with('group', 'weekday', 'session', 'room.branch')->find($id);
        $schedule = $this->Schedule->find($id);

        if (!$schedule)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'schedule_not_found',
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_schedule',
            data: ScheduleResource::make($schedule),
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/schedule/{id}",
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
        $schedule = $this->Schedule->find($id);

        if (!$schedule)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'schedule_not_found',
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "weekday_id" => 'required|numeric|exists:weekdays,id',
            "group_id" => 'required|numeric|exists:groups,id',
            "session_id" => 'required|numeric|exists:sessions,id',
            "room_id" => 'required|numeric|exists:rooms,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newSchedule = Schedule::create([
            "weekday_id" => $request->weekday_id,
            "group_id" => $request->group_id,
            "session_id" => $request->session_id,
            "room_id" => $request->room_id,
        ]);

        // auth('api')->user()->makeChanges(
        //     'Schedule updated from $val1 to $val2',
        //     '$col-name',
        //     $schedule
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'schedule_updated',
            data: ["id" => $newSchedule->id],
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/schedule/{id}",
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
        $schedule = $this->Schedule->find($id);

        if (!$schedule)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'schedule_not_found',
                data: ["id" => $id]
            );

        $schedule->delete();

        // auth('api')->user()->makeChanges(
        //     'Schedule deleted',
        //     'deleted',
        //     $schedule
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'schedule_deleted',
            data: ["id" => $id],
        );
    }
}
