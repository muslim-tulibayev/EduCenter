<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Models\Branch;
use App\Models\User;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InactiveUserController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $InactiveUser;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('users', true);

        $this->middleware(function ($request, $next) {
            $this->InactiveUser = Branch::find($this->auth_branch_id)
                ->users()
                ->where('status', false);
            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/user/inactive",
     * summary="Get all InactiveUser data",
     * description="InactiveUser index",
     * operationId="indexInactiveUser",
     * tags={"InactiveUser"},
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
        $users = $this->InactiveUser->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "get_users",
            data: UserResource::collection($users->items()),
            pagination: $users
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/user/inactive",
     * summary="Set new user",
     * description="InactiveUser store",
     * operationId="storeInactiveUser",
     * tags={"InactiveUser"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "contact", "email", "role_id", "status", "branches"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="contact", type="string", example="+998 98 765 56 78"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(
     *         property="branches", type="array", collectionFormat="multi",
     *         @OA\Items(type="numeric", example=1)
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
            "firstname" => "required|string",
            "lastname" => "required|string",
            "contact" => "required|string",
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            "role_id" => "required|exists:roles,id",
            "status" => "required|boolean",
            "branches" => "required|array",
            "branches.*" => "numeric|distinct|exists:branches,id",
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newUser = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact' => $request->contact,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'password' => Hash::make('12345678'),
        ]);

        $newUser->branches()->attach($request->branches);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'user_created',
            message: trans('msg.created', ['attribute' => __('msg.inactive_user')]),
            data: ["id" => $newUser->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/user/inactive/{id}",
     * summary="Get specific InactiveUser data",
     * description="InactiveUser show",
     * operationId="showInactiveUser",
     * tags={"InactiveUser"},
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
        $user = $this->InactiveUser->find($id);

        if (!$user)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'user_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.inactive_user')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'user_found',
            data: UserResource::make($user)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/user/inactive/{id}",
     * summary="Update specific InactiveUser",
     * description="InactiveUser update",
     * operationId="updateInactiveUser",
     * tags={"InactiveUser"},
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
     *       required={"firstname", "lastname", "contact", "email","role","branch_id", "password"},
     *       @OA\Property(property="firstname", type="string", example="address"),
     *       @OA\Property(property="lastname", type="string", example="address"),
     *       @OA\Property(property="contact", type="string", example="address"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="role_id", type="number", example=1),
     *       @OA\Property(property="branch_id", type="number", example=1),
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
        $user = $this->InactiveUser->find($id);

        if (!$user)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'user_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.inactive_user')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'contact' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email,' . $id
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            "role_id" => 'required|exists:roles,id',
            'status' => 'required|boolean',
            "branches" => 'required|array',
            "branches.*" => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact' => $request->contact,
            'email' => $request->email,
            "role_id" => $request->role_id,
            'status' => $request->status,
        ]);

        $user->branches()->sync($request->branches);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'user_updated',
            message: trans('msg.updated', ['attribute' => __('msg.inactive_user')]),
            data: ["id" => $user->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/user/inactive/{id}",
     * summary="Delete specific InactiveUser",
     * description="InactiveUser delete",
     * operationId="destroyInactiveUser",
     * tags={"InactiveUser"},
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
        $user = $this->InactiveUser->find($id);

        if (!$user)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'user_not_found',
                message: trans('msg.not_found', __('msg.inactive_user')),
                data: ["id" => $id]
            );

        $user->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'user_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.inactive_user')]),
            data: ["id" => $user->id]
        );
    }
}
