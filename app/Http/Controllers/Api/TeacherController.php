<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Teacher\TeacherResource;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
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
      * path="/api/teacher",
      * summary="Get all teachers data",
      * description="Teacher index",
      * operationId="indexTeacher",
      * tags={"Teacher"},
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
        $teachers = Teacher::orderByDesc('id')->paginate();

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

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
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
