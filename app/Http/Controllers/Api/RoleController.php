<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');

        parent::__construct('roles');
    }

    /**
     * @OA\Get(
     * path="/api/role",
     * summary="Get all Roles data",
     * description="Role index",
     * operationId="indexRole",
     * tags={"Role"},
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
        $roles = Role::orderBy('id')->paginate();

        return response()->json([
            "data" => $roles
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/role",
     * summary="Set new Role",
     * description="Role store",
     * operationId="storeRole",
     * tags={"Role"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "roles", "users", "teachers", "courses", "lessons", "groups", "students", "stparents", "sessions", "branches", "rooms", "schedules", "cashiers", "access_for_courses", "student_search", "payment_addcard", "payment_cashier", "payment_pay"},
     *       @OA\Property(property="name", type="string", example="editor"),
     *       @OA\Property(property="roles", type="numeric", example=1),
     *       @OA\Property(property="users", type="numeric", example=1),
     *       @OA\Property(property="teachers", type="numeric", example=1),
     *       @OA\Property(property="courses", type="numeric", example=1),
     *       @OA\Property(property="lessons", type="numeric", example=1),
     *       @OA\Property(property="groups", type="numeric", example=1),
     *       @OA\Property(property="students", type="numeric", example=1),
     *       @OA\Property(property="stparents", type="numeric", example=1),
     *       @OA\Property(property="sessions", type="numeric", example=1),
     *       @OA\Property(property="branches", type="numeric", example=1),
     *       @OA\Property(property="rooms", type="numeric", example=1),
     *       @OA\Property(property="schedules", type="numeric", example=1),
     *       @OA\Property(property="cashiers", type="numeric", example=1),
     *       @OA\Property(property="access_for_courses", type="numeric", example=1),
     *       @OA\Property(property="student_search", type="numeric", example=1),
     *       @OA\Property(property="payment_addcard", type="numeric", example=1),
     *       @OA\Property(property="payment_cashier", type="numeric", example=1),
     *       @OA\Property(property="payment_pay", type="numeric", example=1),
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
            // role name
            'name' => 'required|string|unique:roles,name',

            // access for tables (CRUD)
            'roles' => 'required|numeric|in:0,1,2,3,4',
            'users' => 'required|numeric|in:0,1,2,3,4',
            // 'weekdays',
            'teachers' => 'required|numeric|in:0,1,2,3,4',
            'courses' => 'required|numeric|in:0,1,2,3,4',
            'lessons' => 'required|numeric|in:0,1,2,3,4',
            'groups' => 'required|numeric|in:0,1,2,3,4',
            'students' => 'required|numeric|in:0,1,2,3,4',
            'stparents' => 'required|numeric|in:0,1,2,3,4',
            'sessions' => 'required|numeric|in:0,1,2,3,4',
            'branches' => 'required|numeric|in:0,1,2,3,4',
            'rooms' => 'required|numeric|in:0,1,2,3,4',
            'schedules' => 'required|numeric|in:0,1,2,3,4',
            // 'changes',
            // 'certificates',
            // 'failedsts',
            // 'failedgroups',
            'cashiers' => 'required|numeric|in:0,1,2,3,4',
            'access_for_courses' => 'required|numeric|in:0,1,2,3,4',

            // access for functionalities
            'student_search' => 'required|numeric|in:0,1',
            'payment_addcard' => 'required|numeric|in:0,1',
            'payment_cashier' => 'required|numeric|in:0,1',
            'payment_pay' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $newRole = Role::create($validator->validated());

        return response()->json([
            "message" => "new role created successfuly",
            "id" => $newRole->id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/role/{id}",
     * summary="Get specific Role data",
     * description="Role show",
     * operationId="showRole",
     * tags={"Role"},
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
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                "error" => "not found"
            ], 400);

        return response()->json([
            "data" => $role
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/role/{id}",
     * summary="Update specific Role",
     * description="Role update",
     * operationId="updateRole",
     * tags={"Role"},
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
     *       required={"name", "roles", "users", "teachers", "courses", "lessons", "groups", "students", "stparents", "sessions", "branches", "rooms", "schedules", "cashiers", "access_for_courses", "student_search", "payment_addcard", "payment_cashier", "payment_pay"},
     *       @OA\Property(property="name", type="string", example="editor"),
     *       @OA\Property(property="roles", type="numeric", example=1),
     *       @OA\Property(property="users", type="numeric", example=1),
     *       @OA\Property(property="teachers", type="numeric", example=1),
     *       @OA\Property(property="courses", type="numeric", example=1),
     *       @OA\Property(property="lessons", type="numeric", example=1),
     *       @OA\Property(property="groups", type="numeric", example=1),
     *       @OA\Property(property="students", type="numeric", example=1),
     *       @OA\Property(property="stparents", type="numeric", example=1),
     *       @OA\Property(property="sessions", type="numeric", example=1),
     *       @OA\Property(property="branches", type="numeric", example=1),
     *       @OA\Property(property="rooms", type="numeric", example=1),
     *       @OA\Property(property="schedules", type="numeric", example=1),
     *       @OA\Property(property="cashiers", type="numeric", example=1),
     *       @OA\Property(property="access_for_courses", type="numeric", example=1),
     *       @OA\Property(property="student_search", type="numeric", example=1),
     *       @OA\Property(property="payment_addcard", type="numeric", example=1),
     *       @OA\Property(property="payment_cashier", type="numeric", example=1),
     *       @OA\Property(property="payment_pay", type="numeric", example=1),
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

    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                "error" => "not found"
            ], 400);

        $validator = Validator::make($request->all(), [
            // role name
            'name' => 'required|string',

            // access for tables (CRUD)
            'roles' => 'required|numeric|in:0,1,2,3,4',
            'users' => 'required|numeric|in:0,1,2,3,4',
            // 'weekdays',
            'teachers' => 'required|numeric|in:0,1,2,3,4',
            'courses' => 'required|numeric|in:0,1,2,3,4',
            'lessons' => 'required|numeric|in:0,1,2,3,4',
            'groups' => 'required|numeric|in:0,1,2,3,4',
            'students' => 'required|numeric|in:0,1,2,3,4',
            'stparents' => 'required|numeric|in:0,1,2,3,4',
            'sessions' => 'required|numeric|in:0,1,2,3,4',
            'branches' => 'required|numeric|in:0,1,2,3,4',
            'rooms' => 'required|numeric|in:0,1,2,3,4',
            'schedules' => 'required|numeric|in:0,1,2,3,4',
            // 'changes',
            // 'certificates',
            // 'failedsts',
            // 'failedgroups',
            'cashiers' => 'required|numeric|in:0,1,2,3,4',
            'access_for_courses' => 'required|numeric|in:0,1,2,3,4',

            // access for functionalities
            'student_search' => 'required|numeric|in:0,1',
            'payment_addcard' => 'required|numeric|in:0,1',
            'payment_cashier' => 'required|numeric|in:0,1',
            'payment_pay' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $existRoleName = Role::where('name', $request->name)->first();

        if ($existRoleName)
            return response()->json([
                "name" => "The name has already been taken."
            ], 400);

        $role->update($validator->validated());

        return response()->json([
            "message" => "new role created successfuly",
            "id" => $role->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/role/{id}",
     * summary="Delete specific Role",
     * description="Role delete",
     * operationId="destroyRole",
     * tags={"Role"},
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
        $role = Role::find($id);

        if (!$role)
            return response()->json([
                "error" => "not found"
            ], 400);

        $role->delete();

        return response()->json([
            "message" => "Role deleted successfully",
            "id" => $id
        ]);
    }
}
