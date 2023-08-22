<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\GroupResourceMin;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Group;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student', ["except" => ['login']]);
        $this->middleware('auth:api', ["only" => ['store', 'update', 'destroy']]);
        $this->middleware('auth:teacher', ["only" => ['logout']]);
    }

    /**
     * @OA\Get(
     * path="/api/teacher?role={role}",
     * summary="Get all teachers data",
     * description="Teacher index",
     * operationId="indexTeacher",
     * tags={"Teacher"},
     * security={ {"bearerAuth": {} }},
     *      
     * @OA\Parameter(
     *    in="path",
     *    name="role",
     *    required=false,
     *    description="Role to fetch the targeted campaigns.",
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

    public function index(Request $req)
    {
        if (isset($req->role)) {
            if ($req->role === 'assistant')
                $teachers = Teacher::where('is_assistant', true)->orderByDesc('id')->paginate();
            elseif ($req->role === 'main')
                $teachers = Teacher::where('is_assistant', false)->orderByDesc('id')->paginate();
            else
                $teachers = Teacher::orderByDesc('id')->paginate();
        } else {
            $teachers = Teacher::orderByDesc('id')->paginate();
        }

        // if (auth('api')->user())
        //     return TeacherResourceForAdmin::collection($teachers);

        return TeacherResource::collection($teachers);
    }

    /**
     * @OA\Post(
     * path="/api/teacher",
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:teachers,email',
            'contact_no' => 'required|string',
            'is_assistant' => 'required|boolean',
            // 'group_id' => 'numeric|exists:groups,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newTeacher = Teacher::create([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'email' => $req->email,
            'password' => Hash::make('12345678'),
            'contact_no' => $req->contact_no,
            'is_assistant' => $req->is_assistant
        ]);

        // if ($req->has('group_id'))
        //     $newTeacher->groups()->attach($req->group_id);

        auth('api')->user()->makeChanges(
            'New teacher created',
            'created',
            $newTeacher
        );

        return response()->json([
            "message" => "Teacher created successfully",
            "teacher" => $newTeacher->id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/teacher/{id}",
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
        $teacher = Teacher::find($id);

        if ($teacher === null)
            return response()->json(["error" => "Not found"]);

        // if (auth('api')->user())
        //     return new TeacherResource($teacher);

        return new TeacherResource($teacher);
    }

    /**
     * @OA\Put(
     * path="/api/teacher/{id}",
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
        $teacher = Teacher::find($id);
        if ($teacher === null)
            return response()->json(["error" => "Not found"]);

        if (auth('teacher')->user() !== null) {
            if (auth('teacher')->user()->id != $id) {
                return response()->json(["error" => "Unauthorized"], 401);
            }
        }

        $validator = Validator::make($req->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:teachers,email',
            'contact_no' => 'required|string',
            'is_assistant' => 'boolean',
            // 'group_id' => 'numeric|exists:groups,id',
        ]);

        if ($teacher->email !== $req->email) {
            $found = Teacher::where('email', '=', $req->email)->first();
            if ($found !== null) {
                return response([
                    "email" => [
                        "The email has already been taken."
                    ]
                ]);
            }
        }

        if ($validator->fails())
            return response()->json($validator->messages());

        if ($req->has('password') && (auth('teacher')->user() !== null)) {
            $teacher->password = Hash::make($req->password);
            $teacher->save();
        }

        if ($req->has('is_assistant') && (auth('api')->user() !== null)) {
            $teacher->is_assistant = $req->is_assistant;
            $teacher->save();
        }

        $teacher->update([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'email' => $req->email,
            'contact_no' => $req->contact_no,
        ]);

        if (auth('api')->user() !== null)
            auth('api')->user()->makeChanges(
                'teacher updated from $val1 to $val2',
                '$col-name',
                $teacher
            );

        if (auth('teacher')->user() !== null)
            auth('teacher')->user()->makeChanges(
                'teacher updated from $val1 to $val2',
                '$col-name',
                $teacher
            );

        return response()->json([
            "message" => "Teacher updated successfully",
            "teacher" => $teacher->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/teacher/{id}",
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
        $teacher = Teacher::find($id);
        if ($teacher === null)
            return response()->json(["error" => "Not found"]);

        // foreach ($teacher->stparents as $parent)
        //     $parent->delete();

        auth('api')->user()->makeChanges(
            'teacher deleted',
            'deleted',
            $teacher
        );

        $groups = Group::where('teacher_id', $id)
            ->orWhere('assistant_teacher_id', $id)
            ->get();

        if (count($groups) !== 0) {
            return response()->json([
                'data' => GroupResourceMin::collection($groups),
                'status' => 400
            ]);
        }

        $teacher->delete();

        return response()->json([
            "message" => "success",
            "teacher" => $teacher->id,
            "status" => 200,
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/teacher/login",
     * summary="Login",
     * description="Login by email, password",
     * operationId="teacherLogin",
     * tags={"Teacher"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="12345678")
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 422);

        $token = auth('teacher')->setTTL(60 * 12)->attempt($validator->validated());

        if (!$token)
            return response()->json(['error' => 'Unauthorized'], 401);

        return response(['token' => $token]);
    }

    /**
     * @OA\Get(
     * path="/api/teacher/logout",
     * summary="Logout",
     * description="Logout",
     * operationId="teacherLogout",
     * tags={"Teacher"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Teacher logged out")
     *        )
     *     )
     * )
     */

    public function logout()
    {
        auth('teacher')->logout();
        return response()->json(['message' => 'Teacher logged out'], 201);
    }
}