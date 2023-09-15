<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Branch\BranchResource;
use App\Models\Branch;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Branch;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('branches', true);

        $this->middleware(function ($request, $next) {
            // $this->Branch = Branch::find($this->auth_branch_id);
            $this->Branch = $this->auth_user->branches();

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/branch",
     * summary="Branch index",
     * description="Get all branches data",
     * operationId="index",
     * tags={"Branch"},
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
        $branches = $this->Branch->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: BranchResource::collection($branches),
            pagination: $branches
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/branch",
     * summary="Branch store",
     * description="Set new branch",
     * operationId="store",
     * tags={"Branch"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name","location"},
     *       @OA\Property(property="name", type="string", format="text", example="Bukhara"),
     *       @OA\Property(property="location", type="string", format="text", example="address"),
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
        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "location" => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newBranch = Branch::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if ($this->auth_type === 'api')
            $newBranch->users()->attach($this->auth_user->id);
        elseif ($this->auth_type === 'teacher')
            $newBranch->teachers()->attach($this->auth_user->id);

        // auth('api')->user()->makeChanges(
        //     'New branch created',
        //     'created',
        //     $newBranch
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.created', ['attribute' => __('msg.attributes.branch')]),
            data: ["id" => $newBranch->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/branch/{id}",
     * summary="Get specific Branch data",
     * description="Branch show",
     * operationId="showBranch",
     * tags={"Branch"},
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
        $branch = $this->Branch->find($id);

        if (!$branch)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.branch')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: BranchResource::make($branch),
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/branch/{id}",
     * summary="Branch update",
     * description="Update specific branch",
     * operationId="update",
     * tags={"Branch"},
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
     *       required={"name","location"},
     *       @OA\Property(property="name", type="string", format="text", example="Bukhara"),
     *       @OA\Property(property="location", type="string", format="text", example="address"),
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
        $branch = $this->Branch->find($id);

        if (!$branch)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.branch')]),
                data: ["id" => $id],
            );

        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "location" => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $branch->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        // auth('api')->user()->makeChanges(
        //     'Branch updated from $val1 to $val2',
        //     '$col-name',
        //     $branch
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.branch')]),
            data: ["id" => $branch->id],
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/branch/{id}",
     * summary="Branch delete",
     * description="Delete specific branch",
     * operationId="destroy",
     * tags={"Branch"},
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
        $branch = $this->Branch->find($id);

        if (!$branch)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.branch')]),
                data: ["id" => $id],
            );

        $branch->delete();

        // auth('api')->user()->makeChanges(
        //     'Branch deleted',
        //     'deleted',
        //     $branch
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.branch')]),
            data: ["id" => $branch->id],
        );
    }
}


    // /**
    //  * @OA\Get(
    //  * path="/api/manage/branch/{id}/rooms",
    //  * summary="Branch rooms",
    //  * description="Get specific branch rooms",
    //  * operationId="rooms",
    //  * tags={"Branch"},
    //  * security={ {"bearerAuth": {} }},
    //  *
    //  * @OA\Parameter(
    //  *    in="path",
    //  *    name="id",
    //  *    required=true,
    //  *    description="ID to fetch the targeted campaigns.",
    //  *    @OA\Schema(type="string")
    //  * ),
    //  *
    //  * @OA\Response(
    //  *    response=403,
    //  *    description="Wrong credentials response",
    //  *    @OA\JsonContent(
    //  *       @OA\Property(property="message", type="string", example="Unauthorized")
    //  *        )
    //  *     )
    //  * )
    //  */
    // public function rooms(string $id)
    // {
    //     $branch = Branch::find($id);
    //     if ($branch === null)
    //         return response()->json(["error" => "Not found"]);
    //     $temp = $branch->rooms()->orderBy('name')->paginate();
    //     return response()->json($temp);
    // }
    // /**
    //  * @OA\Post(
    //  * path="/api/manage/branch/{id}/rooms",
    //  * summary="Branch addRooms",
    //  * description="Set rooms the specific branch",
    //  * operationId="addRooms",
    //  * tags={"Branch"},
    //  * security={ {"bearerAuth": {} }},
    //  *
    //  * @OA\Parameter(
    //  *    in="path",
    //  *    name="id",
    //  *    required=true,
    //  *    description="ID to fetch the targeted campaigns.",
    //  *    @OA\Schema(type="string")
    //  * ),
    //  *
    //  * @OA\RequestBody(
    //  *    required=true,
    //  *    description="Pass user credentials",
    //  *    @OA\JsonContent(
    //  *       required={"rooms"},
    //  *       @OA\Property(
    //  *         property="rooms", type="array", collectionFormat="multi",
    //  *         @OA\Items(type="string", example="hello"),
    //  *      ),
    //  *    ),
    //  * ),
    //  *
    //  * @OA\Response(
    //  *    response=403,
    //  *    description="Wrong credentials response",
    //  *    @OA\JsonContent(
    //  *       @OA\Property(property="message", type="string", example="Unauthorized")
    //  *     )
    //  *   )
    //  * )
    //  */
    // public function addRooms(Request $request, string $id)
    // {
    //     $branch = Branch::find($id);
    //     if ($branch === null)
    //         return response()->json(["error" => "Not found"]);
    //     $validator = Validator::make($request->all(), [
    //         "rooms" => 'required|array',
    //         // unique in one specific branch
    //         // "rooms.*" => 'required|string|distinct|unique:rooms,name', 
    //         "rooms.*" => 'required|string|distinct',
    //     ]);
    //     if ($validator->fails())
    //         return response()->json($validator->messages());
    //     foreach ($request->rooms as $room)
    //         $branch->rooms()->create(["name" => $room]);
    //     auth('api')->user()->makeChanges(
    //         'Branch updated from $val1 to $val2',
    //         '$col-name',
    //         $branch
    //     );
    //     return response()->json([
    //         "message" => "Rooms has been added successfully"
    //     ]);
    // }
    // /**
    //  * @OA\Post(
    //  * path="/api/manage/branch/schedule",
    //  * summary="Branch getSchedules",
    //  * description="Get branch's specific schedules",
    //  * operationId="getSchedules",
    //  * tags={"Branch"},
    //  * security={ {"bearerAuth": {} }},
    //  *
    //  * @OA\RequestBody(
    //  *    required=true,
    //  *    description="Pass user credentials",
    //  *    @OA\JsonContent(
    //  *       required={"branch_id", "weekday_id"},
    //  *       @OA\Property(property="branch_id", type="string", format="text", example="1"),
    //  *       @OA\Property(property="weekday_id", type="string", format="text", example="1"),
    //  *    ),
    //  * ),
    //  *
    //  * @OA\Response(
    //  *    response=403,
    //  *    description="Wrong credentials response",
    //  *    @OA\JsonContent(
    //  *       @OA\Property(property="message", type="string", example="Unauthorized")
    //  *     )
    //  *   )
    //  * )
    //  */
    // public function getSchedule(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         "branch_id" => 'required|exists:branches,id',
    //         "weekday_id" => 'required|exists:weekdays,id',
    //     ]);
    //     if ($validator->fails())
    //         return response()->json($validator->messages());
    //     $schedules = Branch::find($request->branch_id)->schedules()->where('weekday_id', $request->weekday_id)->get();
    //     return ScheduleResourceThroughBranch::collection($schedules);
    // }