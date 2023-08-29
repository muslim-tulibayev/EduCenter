<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Student\StudentResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        // $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy', 'changeStudents']]);

        parent::__construct('groups');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['students'] >= 1))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('students');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['groups'] >= 2))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('changeStudents');
    }

    /**
     * @OA\Get(
     * path="/api/group",
     * summary="Get all groups data",
     * description="Group index",
     * operationId="indexGroup",
     * tags={"Group"},
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
        $groups = Group::orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return GroupResource::collection($groups);

        return GroupResource::collection($groups);
    }

    /**
     * @OA\Post(
     * path="/api/group",
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

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "name" => 'required|string',
            "status" => 'boolean',
            "teacher_id" => 'required|exists:teachers,id',
            "assistant_teacher_id" => 'required|exists:teachers,id',
            "course_id" => 'required|exists:courses,id',
            "students" => 'array',
            'students.*' => 'required|numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newGroup = Group::create([
            "name" => $req->name,
            "status" => $req->status ?? true,
            "completed_lessons" => 0,
            "teacher_id" => $req->teacher_id,
            "assistant_teacher_id" => $req->assistant_teacher_id,
            "course_id" => $req->course_id,
        ]);

        if ($req->has('students'))
            $newGroup->students()->attach($req->students);

        auth('api')->user()->makeChanges(
            'New group created',
            'created',
            $newGroup
        );

        return response()->json([
            "message" => "Group created successfully",
            "group" => $newGroup->id,
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/group/{id}",
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
        $group = Group::find($id);

        if ($group === null)
            return response()->json(["error" => "Not found"]);

        // if (auth('api')->user())
        //     return new GroupResource($group);

        return new GroupResource($group);
    }

    /**
     * @OA\Put(
     * path="/api/group/{id}",
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

    public function update(Request $req, string $id)
    {
        $group = Group::find($id);

        if ($group === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
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
            return response()->json($validator->messages());

        $group->update([
            "name" => $req->name,
            "status" => $req->status,
            "completed_lessons" => $req->completed_lessons,
            "teacher_id" => $req->teacher_id,
            "assistant_teacher_id" => $req->assistant_teacher_id,
            "course_id" => $req->course_id,
        ]);

        if ($req->has('students')) {
            $group->students()->detach();
            $group->students()->attach($req->students);
        }

        auth('api')->user()->makeChanges(
            'Group updated from $val1 to $val2',
            '$col-name',
            $group
        );

        return response()->json([
            "message" => "Group updated successfully",
            "group" => $group->id,
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/group/{id}",
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
        $group = Group::find($id);

        if ($group === null)
            return response()->json(["error" => "Not found"]);

        $group->delete();

        auth('api')->user()->makeChanges(
            'Group deleted',
            'deleted',
            $group
        );

        return response()->json([
            "message" => "Group deleted successfully",
            "group" => $id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/group/{id}/students",
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

    public function students(string $id)
    {
        $group = Group::find($id);

        if ($group === null)
            return response()->json(["error" => "Not found"]);

        $students = $group->students()->orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return StudentResource::collection($students);

        return StudentResource::collection($students);
    }

    /**
     * @OA\Post(
     * path="/api/group/{id}/students",
     * summary="Change group students",
     * description="Group changeStudents",
     * operationId="changeStudentsGroup",
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
     *       required={"students"},
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
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

    public function changeStudents(Request $req, string $id)
    {
        $group = Group::find($id);

        if ($group === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            "students" => 'required|array',
            "students.*" => 'required|numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $group->students()->detach();
        $group->students()->attach($req->students);

        auth('api')->user()->makeChanges(
            'Group updated from $val1 to $val2',
            '$col-name',
            $group
        );

        return response()->json([
            "message" => "Students of the group are changed successfully",
            "group" => $id
        ]);
    }
}
