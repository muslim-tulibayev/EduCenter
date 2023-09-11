<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Branch;
use App\Models\Teacher;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $Teacher;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');

        parent::__construct('teachers', true);

        $this->middleware(function ($request, $next) {
            $this->Teacher = Branch::find($this->auth_branch_id)
                ->teachers()
                ->where('is_assistant', false);

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/teacher",
     * summary="Get all teachers data",
     * description="Teacher index",
     * operationId="indexTeacher",
     * tags={"Teacher"},
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
        $teachers = $this->Teacher->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: "get_teachers",
            data: TeacherResource::collection($teachers),
            pagination: $teachers
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/teacher",
     * summary="Set new teacher",
     * description="Teacher store",
     * operationId="storeTeacher",
     * tags={"Teacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact_no", "is_assistant", },
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="+998 56 789 09 87"),
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
            'contact_no' => 'required|string',
            "role_id" => 'required|exists:roles,id',
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
            'contact_no' => $request->contact_no,
            'is_assistant' => false,
            'role_id' => $request->role_id,
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
            name: "teacher_created",
            data: ["id" => $newTeacher->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/teacher/{id}",
     * summary="Get specific teacher data",
     * description="Teacher show",
     * operationId="showTeacher",
     * tags={"Teacher"},
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
        $teacher = $this->Teacher->find($id);

        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: "teacher_not_found",
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: "teacher_found",
            data: TeacherResource::make($teacher)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/teacher/{id}",
     * summary="Update specific Teacher",
     * description="Teacher update",
     * operationId="updateTeacher",
     * tags={"Teacher"},
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
     *       required={"firstname", "lastname", "email", "contact_no"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="+998 56 789 09 87"),
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
        $teacher = $this->Teacher->find($id);
        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: "teacher_not_found",
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
            'contact_no' => 'required|string',
            'is_assistant' => 'boolean',
            "role_id" => 'required|exists:roles,id',
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
            'contact_no' => $request->contact_no,
            'is_assistant' => $request->is_assistant ?? false,
            'role_id' => $request->role_id,
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
            name: "teacher_updated",
            data: ["id" => $id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/teacher/{id}",
     * summary="Delete specific Teacher",
     * description="Teacher delete",
     * operationId="destroyTeacher",
     * tags={"Teacher"},
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
        $teacher = $this->Teacher->find($id);

        if (!$teacher)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: "teacher_not_found",
                data: ["id" => $id]
            );

        if (count($teacher->groups))
            return $this->sendResponse(
                success: false,
                status: 400,
                name: "teacher_has_groups",
                data: GroupResource::collection($teacher->groups)
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
            name: "teacher_deleted",
            data: ["id" => $id]
        );
    }
}
