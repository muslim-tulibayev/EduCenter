<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Student\StudentResourceForSearch;
use App\Models\Stparent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        // $this->middleware('auth:api,student', ["only" => ['update']]);
        // $this->middleware('auth:api', ["only" => ['store', 'destroy']]);

        parent::__construct('students');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['student_search'] >= 1))
                return response()->json([
                    "error" => "Unauthorized"
                ], 403);

            return $next($request);
        })->only('search');
    }

    /**
     * @OA\Get(
     * path="/api/student",
     * summary="Get all students data",
     * description="Student index",
     * operationId="indexStudent",
     * tags={"Student"},
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
        $students = Student::with('groups')->with('stparents')->orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return StudentResourceForAdmin::collection($students);

        // if (auth('teacher')->user())
        //     return StudentResourceForTeacher::collection($students);

        // if (auth('parent')->user())
        //     return StudentResourceForParent::collection($students);

        // if (auth('student')->user())
        //     return StudentResourceForStudent::collection($students);

        return StudentResource::collection($students);
    }

    /**
     * @OA\Post(
     * path="/api/student",
     * summary="Set new student",
     * description="Student store",
     * operationId="storeStudent",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact_no"},
     *       @OA\Property(property="firstname", type="string", example="address"),
     *       @OA\Property(property="lastname", type="string", example="address"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="address"),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="group_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="parents", type="array", collectionFormat="multi",
     *         @OA\Items(type="object", example={
     *              "firstname": "John",
     *              "lastname": "Doe",
     *              "email": "user@gmail.com",
     *              "contact_no": "+998 98 545 46 78",
     *         }),
     *      ),
     *       @OA\Property(
     *         property="exist_parents", type="array", collectionFormat="multi",
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:students,email',
            // 'password' => 'required|confirmed|string|min:8',
            'contact_no' => 'required|string',
            // 'is_paid' => 'boolean',
            'status' => 'boolean',
            'group_id' => 'numeric|exists:groups,id',
            'parents' => 'array',
            'parents.*.firstname' => 'required|string',
            'parents.*.lastname' => 'required|string',
            'parents.*.email' => 'required|email|unique:stparents,email',
            'parents.*.contact_no' => 'required|string',
            'exist_parents' => 'array',
            'exist_parents.*' => 'required|numeric|exists:stparents,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newStudent = Student::create([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'email' => $req->email,
            'password' => Hash::make('12345678'),
            'contact_no' => $req->contact_no,
            // 'is_paid' => $req->is_paid ?? false,
            'status' => $req->status ?? false,
            'created_by' => auth('api')->user()->id,
            'created_at' => date('Y-m-d h:i:s')
        ]);

        if ($req->has('group_id'))
            $newStudent->groups()->attach($req->group_id);

        $attaches = [];

        if ($req->has('parents')) {
            foreach ($req->parents as $parent) {
                $newParent = Stparent::create([
                    "firstname" => $parent['firstname'],
                    "lastname" => $parent['lastname'],
                    "email" => $parent['email'],
                    "password" => Hash::make('12345678'),
                    "contact_no" => $parent['contact_no']
                ]);
                array_push($attaches, $newParent->id);
            }
        }

        if ($req->has('exist_parents'))
            array_merge($attaches, $req->exist_parents);

        $newStudent->stparents()->attach($attaches);

        if (auth('api')->user() !== null)
            auth('api')->user()->makeChanges(
                'New student created',
                'created',
                $newStudent
            );

        return response()->json([
            "message" => "Student created successfully",
            "student" => $newStudent->id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/student/{id}",
     * summary="Get specific student data",
     * description="Student show",
     * operationId="showStudent",
     * tags={"Student"},
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
        $student = Student::with('stparents')->with('groups')->find($id);

        if ($student === null)
            return response()->json(['error' => 'Not found']);

        // if (auth('api')->user())
        //     return new StudentResourceForAdmin($student);

        // if (auth('teacher')->user())
        //     return new StudentResourceForTeacher($student);

        // if (auth('parent')->user())
        //     return new StudentResourceForParent($student);

        // if (auth('student')->user())
        //     return new StudentResourceForStudent($student);

        return new StudentResource($student);
    }

    /**
     * @OA\Put(
     * path="/api/student/{id}",
     * summary="Update specific student",
     * description="Student update",
     * operationId="updateStudent",
     * tags={"Student"},
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
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="contact_no", type="string", example="+998 92 894 83 21"),
     *       @OA\Property(property="status", type="boolean", example=false),
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
        $student = Student::find($id);
        if ($student === null)
            return response()->json(["error" => "Not found"]);

        if (auth('student')->user() !== null) {
            if (auth('student')->user()->id != $id) {
                return response()->json(["error" => "Unauthorized"], 403);
            }
        }

        $validator = Validator::make($req->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'confirmed|string|min:8',
            'contact_no' => 'required|string',
            // 'is_paid' => 'boolean',
            'status' => 'boolean',
            // 'group_id' => 'numeric|exists:groups,id',
            // 'parents' => 'array',
            // 'parents.*.firstname' => 'required|string',
            // 'parents.*.lastname' => 'required|string',
            // 'parents.*.email' => 'required|email|unique:stparents,email',
            // 'parents.*.contact_no' => 'required|string',
            // 'exist_parents' => 'array',
            // 'exist_parents.*' => 'required|numeric|exists:stparents,id',
        ]);

        if ($student->email !== $req->email) {
            $found = Student::where('email', '=', $req->email)->first();
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

        if ($req->has('password') && (auth('student')->user() !== null)) {
            $student->password = Hash::make($req->password);
            $student->save();
        }

        // if ($req->has('is_paid') && (auth('api')->user() !== null)) {
        //     $student->is_paid = $req->is_paid;
        //     $student->save();
        // }

        if ($req->has('status') && (auth('api')->user() !== null)) {
            $student->status = $req->status;
            $student->save();
        }

        $student->update([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'email' => $req->email,
            'contact_no' => $req->contact_no,
        ]);

        if (auth('api')->user() !== null)
            auth('api')->user()->makeChanges(
                'Student updated from $val1 to $val2',
                '$col-name',
                $student
            );

        if (auth('student')->user() !== null)
            auth('student')->user()->makeChanges(
                'Student updated from $val1 to $val2',
                '$col-name',
                $student
            );

        return response()->json([
            "message" => "Student updated successfully",
            "student" => $student->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/student/{id}",
     * summary="Delete specific student",
     * description="Student delete",
     * operationId="destroyStudent",
     * tags={"Student"},
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

    public function destroy($id)
    {
        $student = Student::find($id);
        if ($student === null)
            return response()->json(["error" => "Not found"]);

        // foreach ($student->stparents as $parent)
        //     $parent->delete();

        auth('api')->user()->makeChanges(
            'Student deleted',
            'deleted',
            $student
        );

        $student->delete();

        return response()->json([
            "message" => "Student deleted successfully",
            "student" => $student->id
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/student/search",
     * summary="Search",
     * description="Search by student firstname or lastname",
     * operationId="studentSearch",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass student credentials",
     *    @OA\JsonContent(
     *       required={"data"},
     *       @OA\Property(property="data", type="string", format="text", example="Luka")
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function search(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "data" => 'required|string'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $students = Student::where('firstname', 'LIKE', "%$req->data%")
            ->orWhere('lastname', 'LIKE', "%$req->data%")
            ->take(10)
            ->get();

        return StudentResourceForSearch::collection($students);
    }

    // public function certificates(string $id)
    // {
    //     $student = Student::find($id);

    //     if (!$student)
    //         return response()->json(["error" => "Not found"]);

    //     return response()->json([
    //         "data" => $student->certificates
    //     ]);
    // }
}
