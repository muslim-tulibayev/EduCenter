<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Student\StudentResource;
use App\Models\Branch;
use App\Models\Group;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Group;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');
        parent::__construct('groups', true);

        $this->middleware(function ($request, $next) {
            $this->Group = Branch::find($this->auth_branch_id)->groups();

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/group",
     * summary="Get all groups data",
     * description="Group index",
     * operationId="indexGroup",
     * tags={"Group"},
     * security={ {"bearerAuth": {} }},
     * 
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
        $groups = $this->Group->with('teacher', 'assistant_teacher', 'course')->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: GroupResource::collection($groups),
            pagination: $groups
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/group",
     * summary="Set new group",
     * description="Group store",
     * operationId="storeGroup",
     * tags={"Group"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "teacher_id", "assistant_teacher_id", "course_id"},
     *       @OA\Property(property="name", type="string", example="user@gmail.com"),
     *       @OA\Property(property="status", type="boolean", example=true),
     *       @OA\Property(property="teacher_id", type="numeric", example=1),
     *       @OA\Property(property="assistant_teacher_id", type="numeric", example=1),
     *       @OA\Property(property="course_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
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
            "status" => 'boolean',
            "teacher_id" => 'required|exists:teachers,id',
            "assistant_teacher_id" => 'required|exists:teachers,id',
            "course_id" => 'required|exists:courses,id',

            "students" => 'array',
            'students.*' => 'required|numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newGroup = Group::create([
            "name" => $request->name,
            "status" => $request->status ?? true,
            "completed_lessons" => 0,
            "teacher_id" => $request->teacher_id,
            "assistant_teacher_id" => $request->assistant_teacher_id,
            "course_id" => $request->course_id,
            "branch_id" => $this->auth_branch_id,
        ]);

        if ($request->has('students'))
            $newGroup->students()->attach($request->students);

        // auth('api')->user()->makeChanges(
        //     'New group created',
        //     'created',
        //     $newGroup
        // );

        return $this->sendResponse(
            success: true,
            status: 201,
            message: trans('msg.created', ['attribute' => __('msg.attributes.group')]),
            data: ["id" => $newGroup->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/group/{id}",
     * summary="Get specific group data",
     * description="Group show",
     * operationId="showGroup",
     * tags={"Group"},
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
        $group = $this->Group->with('teacher', 'assistant_teacher', 'course')->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: GroupResource::make($group)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/group/{id}",
     * summary="Update specific group",
     * description="Group update",
     * operationId="updateGroup",
     * tags={"Group"},
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
     *       @OA\Property(property="name", type="string", example="New Lesson"),
     *       @OA\Property(property="status", type="string", example=true),
     *       @OA\Property(property="completed_lessons", type="numeric", example=1),
     *       @OA\Property(property="teacher_id", type="numeric", example=1),
     *       @OA\Property(property="assistant_teacher_id", type="numeric", example=1),
     *       @OA\Property(property="course_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
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
        $group = $this->Group->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "status" => 'required|boolean',
            "completed_lessons" => 'required|numeric',
            "teacher_id" => 'required|exists:teachers,id',
            "assistant_teacher_id" => 'required|exists:teachers,id',
            "course_id" => 'required|exists:courses,id',

            "students" => 'array',
            'students.*' => 'required|numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $group->update([
            "name" => $request->name,
            "status" => $request->status,
            "completed_lessons" => $request->completed_lessons,
            "teacher_id" => $request->teacher_id,
            "assistant_teacher_id" => $request->assistant_teacher_id,
            "course_id" => $request->course_id,
        ]);

        if ($request->has('students'))
            $group->students()->sync($request->students);

        // auth('api')->user()->makeChanges(
        //     'Group updated from $val1 to $val2',
        //     '$col-name',
        //     $group
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.group')]),
            data: ["id" => $group->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/group/{id}",
     * summary="Delete specific group",
     * description="Group delete",
     * operationId="destroyGroup",
     * tags={"Group"},
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
        $group = $this->Group->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $id]
            );

        $group->delete();

        // auth('api')->user()->makeChanges(
        //     'Group deleted',
        //     'deleted',
        //     $group
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.group')]),
            data: ["id" => $group->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/group/{id}/students",
     * summary="Get specific group students data",
     * description="Group students",
     * operationId="studentsGroup",
     * tags={"Group"},
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

    public function getStudents(string $group_id)
    {
        $group = $this->Group->find($group_id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $group_id]
            );

        $students = $group->students()->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResource::collection($students),
            pagination: $students
        );
    }
}
