<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');
        parent::__construct('roles');
    }

    /**
     * @OA\Get(
     * path="/api/manage/role",
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

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_roles',
            data: RoleResource::collection($roles),
            pagination: $roles
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/role",
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
            // 'student_search' => 'required|numeric|in:0,1',
            // 'payment_addcard' => 'required|numeric|in:0,1',
            // 'payment_cashier' => 'required|numeric|in:0,1',
            // 'payment_pay' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newRole = Role::create($validator->validated());

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'role_created',
            message: trans('msg.created', ['attribute' => __('msg.attributes.role')]),
            data: ["id" => $newRole->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/role/{id}",
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
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'role_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.role')]),
                data: ["id" => $id],
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_role',
            data: RoleResource::make($role),
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/role/{id}",
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
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'role_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.role')]),
                data: ["id" => $id],
            );

        $validator = Validator::make($request->all(), [
            // role name
            'name' => 'required|string|unique:roles,name,' . $id,

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
            // 'student_search' => 'required|numeric|in:0,1',
            // 'payment_addcard' => 'required|numeric|in:0,1',
            // 'payment_cashier' => 'required|numeric|in:0,1',
            // 'payment_pay' => 'required|numeric|in:0,1',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $role->update($validator->validated());

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'role_updated',
            message: trans('msg.updated', ['attribute' => __('msg.attributes.role')]),
            data: ["id" => $role->id],
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/role/{id}",
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
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'role_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.role')]),
                data: ["id" => $id],
            );

        $role->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'role_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.role')]),
            data: ["id" => $role->id],
        );
    }
}
