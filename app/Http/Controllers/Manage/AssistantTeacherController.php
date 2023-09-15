<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Teacher;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AssistantTeacherController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $AssistantTeacher;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('teachers', true);

        $this->middleware(function ($request, $next) {
            $this->AssistantTeacher = Branch::find($this->auth_branch_id)
                ->teachers()
                ->where('is_assistant', true);

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/teacher/assistant",
     * summary="Get all assistant teachers data",
     * description="AssistantTeacher index",
     * operationId="indexAssistantTeacher",
     * tags={"AssistantTeacher"},
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
        $teachers = $this->AssistantTeacher->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: TeacherResource::collection($teachers),
            pagination: $teachers
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/teacher/assistant",
     * summary="Set new assistant teacher",
     * description="AssistantTeacher store",
     * operationId="storeAssistantTeacher",
     * tags={"AssistantTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact", "is_assistant", },
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="+998 56 789 09 87"),
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'contact' => 'required|string',
            'status' => 'required|boolean',
            "role_id" => 'exists:roles,id',

            'groups' => 'array',
            'groups.*' => 'numeric|distinct|exists:groups,id',
            'branches' => 'required|array',
            'branches.*' => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newTeacher = Teacher::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make('12345678'),
            'contact' => $request->contact,
            'status' => $request->status,
            'is_assistant' => true,
            'role_id' => $request->role_id ?? Role::where('name', 'teacher')->first()->id,
        ]);

        if ($request->has('groups'))
            $newTeacher->groups()->attach($request->groups);

        $newTeacher->branches()->attach($request->branches);

        // auth('api')->user()->makeChanges(
        //     'New teacher created',
        //     'created',
        //     $newTeacher
        // );

        return $this->sendResponse(
            success: true,
            status: 201,
            message: trans('msg.created', ['attribute' => __('msg.attributes.assistant_teacher')]),
            data: ["id" => $newTeacher->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/teacher/assistant/{id}",
     * summary="Get specific teacher data",
     * description="AssistantTeacher show",
     * operationId="showAssistantTeacher",
     * tags={"AssistantTeacher"},
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
        $teacher = $this->AssistantTeacher->find($id);

        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.assistant_teacher')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: TeacherResource::make($teacher)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/teacher/assistant/{id}",
     * summary="Update specific AssistantTeacher",
     * description="AssistantTeacher update",
     * operationId="updateAssistantTeacher",
     * tags={"AssistantTeacher"},
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
     *       required={"firstname", "lastname", "email", "contact"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="+998 56 789 09 87"),
     *       @OA\Property(property="is_assistant", type="boolean", example=false),
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
        $teacher = $this->AssistantTeacher->find($id);
        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.assistant_teacher')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email,' . $id
                . '|unique:stparents,email'
                . '|unique:students,email',
            'contact' => 'required|string',
            'status' => 'required|boolean',
            'is_assistant' => 'boolean',
            "role_id" => 'exists:roles,id',

            'groups' => 'array',
            'groups.*' => 'numeric|distinct|exists:groups,id',
            'branches' => 'required|array',
            'branches.*' => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $teacher->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status,
            'is_assistant' => $request->is_assistant ?? true,
            'role_id' => $request->role_id ?? Role::where('name', 'teacher')->first()->id,
        ]);

        if ($request->has('groups'))
            $teacher->groups()->sync($request->groups);

        $teacher->branches()->sync($request->branches);

        // if (auth('teacher')->user() !== null)
        //     auth('teacher')->user()->makeChanges(
        //         'teacher updated from $val1 to $val2',
        //         '$col-name',
        //         $teacher
        //     );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.assistant_teacher')]),
            data: ["id" => $id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/teacher/assistant/{id}",
     * summary="Delete specific AssistantTeacher",
     * description="AssistantTeacher delete",
     * operationId="destroyAssistantTeacher",
     * tags={"AssistantTeacher"},
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
        $teacher = $this->AssistantTeacher->find($id);

        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.assistant_teacher')]),
                data: ["id" => $id]
            );

        if (count($teacher->groups))
            return $this->sendResponse(
                success: false,
                status: 400,
                message: trans('msg.has_groups', ['attribute' => __('msg.attributes.assistant_teacher')]),
                data: GroupResource::collection($teacher->groups()->with('teacher', 'assistant_teacher', 'course')->get())
            );

        $teacher->delete();

        // auth('api')->user()->makeChanges(
        //     'teacher deleted',
        //     'deleted',
        //     $teacher
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.assistant_teacher')]),
            data: ["id" => $id]
        );
    }
}
