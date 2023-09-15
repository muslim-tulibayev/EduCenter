<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Permission\ParentPermissionResource;
use App\Http\Resources\Permission\StudentPermissionResource;
use App\Http\Resources\Permission\TeacherPermissionResource;
use App\Http\Resources\Permission\UserPermissionResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student')->except(['register', 'login']);

        parent::__construct();
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Registration",
     * description="Register by email, password",
     * operationId="authRegister",
     * tags={"Auth"},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password", "firstname", "lastname", "contact", "password_confirmation", "role_id"},
     *       @OA\Property(property="firstname", type="string", format="text", example="John"),
     *       @OA\Property(property="lastname", type="string", format="text", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", format="phone number", example="+998 93 819 88 43"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(property="password", type="string", format="password, min:8", example="12345678"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="12345678"),
     *       @OA\Property(
     *         property="branches", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
     *    ),
     * ),
     * 
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'contact' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'password' => 'required|confirmed|string|min:8',
            "role_id" => 'required|exists:roles,id',
            "branches" => 'array',
            "branches.*" => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newUser = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact' => $request->contact,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            "role_id" => $request->role_id,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.registered', ['attribute' => __('msg.attributes.user')])
        );
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Login",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"Auth"},
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
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        if (auth('api')->validate($validator->validated())) {
            $token = auth('api')->setTTL(60 * 12)->attempt($validator->validated());
            $auth_type = 'api';
        } elseif (auth('teacher')->validate($validator->validated())) {
            $token = auth('teacher')->setTTL(60 * 12)->attempt($validator->validated());
            $auth_type = 'teacher';
        } elseif (auth('parent')->validate($validator->validated())) {
            $token = auth('parent')->setTTL(60 * 12)->attempt($validator->validated());
            $auth_type = 'parent';
        } elseif (auth('student')->validate($validator->validated())) {
            $token = auth('student')->setTTL(60 * 12)->attempt($validator->validated());
            $auth_type = 'student';
        } else {
            return $this->sendResponse(
                success: false,
                status: 422,
                message: trans('msg.wrong_credentials')
            );
        }

        if (!auth($auth_type)->user()->status)
            return $this->sendResponse(
                success: false,
                status: 422,
                message: trans('msg.inactive_user')
            );

        switch ($auth_type) {
            case 'api':
                $permissions = UserPermissionResource::make(auth($auth_type)->user()->role);
                break;
            case 'teacher':
                $permissions = TeacherPermissionResource::make(auth($auth_type)->user()->role);
                break;
            case 'parent':
                $permissions = ParentPermissionResource::make(auth($auth_type)->user()->role);
                break;
            case 'student':
                $permissions = StudentPermissionResource::make(auth($auth_type)->user()->role);
                break;
        }

        return $this->sendResponse(
            success: true,
            status: 200,
            data: [
                "token" => $token,
                "name" => auth($auth_type)->user()->role->name,
                "permissions" => $permissions
            ]
        );
    }

    /**
     * @OA\Get(
     * path="/api/auth/logout",
     * summary="Logout",
     * description="Logout",
     * operationId="authLogout",
     * tags={"Auth"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     )
     * )
     */

    public function logout()
    {
        auth($this->auth_type)->logout();

        return $this->sendResponse(
            success: true,
            status: 200,
        );
    }

    /**
     * @OA\Get(
     * path="/api/auth/me",
     * summary="get the user data",
     * description="Me",
     * operationId="authMe",
     * tags={"Auth"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     )
     * )
     */

    public function me()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            data: [
                // "id": 1,
                "firstname" => $this->auth_user->firstname,
                "lastname" => $this->auth_user->lastname,
                "email" => $this->auth_user->email,
                "contact" => $this->auth_user->contact,
                // "role_id" =>$this->auth_user->firstname,
                "status" => $this->auth_user->status,
                // "created_by" =>$this->auth_user->firstname,
                // "created_at" => $this->auth_user->firstname,
            ]
        );
    }

    /**
     * @OA\Put(
     * path="/api/auth/update",
     * summary="Update profile",
     * description="Update profile",
     * operationId="authUpdate",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact", "password", "password_confirmation"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="+998 98 887 65 43 45"),
     *       @OA\Property(property="password", type="string", example="12345678"),
     *       @OA\Property(property="password_confirmation", type="string", example="12345678"),
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

    public function update(Request $request)
    {
        switch ($this->auth_type) {
            case 'api':
                return $this->updateUser($request);
                break;
            case 'teacher':
                return $this->updateTeacher($request);
                break;
            case 'parent':
                return $this->updateParent($request);
                break;
            case 'student':
                return $this->updateStudent($request);
                break;
        }
    }

    /**
     * @OA\Get(
     * path="/api/auth/branches",
     * summary="get the user data",
     * description="branches",
     * operationId="authBranches",
     * tags={"Auth"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User logged out")
     *        )
     *     )
     * )
     */

    public function branches()
    {
        //! now for only users
        if (!count($this->auth_user->branches) || $this->auth_type !== 'api')
            return $this->sendResponse(
                success: false,
                status: 404,
            );

        $data = [];

        foreach ($this->auth_user->branches as $branch) {
            $encoded_data = base64_encode(json_encode([
                "id" => $branch->id,
                "time" => time(),
            ]));

            $data[] = [
                "branch_name" => $branch->name,
                "branch" => $encoded_data,
            ];
        }

        return $this->sendResponse(
            success: true,
            status: 200,
            data: $data
        );
    }

    /* Privates :) */


    private function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email,' . $this->auth_user->id
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'contact' => 'required|string',
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->email = $request->email;
        $this->auth_user->contact = $request->contact;

        if ($request->has('password'))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.user')])
        );
    }

    private function updateTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email,' . $this->auth_user->id
                . '|unique:stparents,email'
                . '|unique:students,email',
            "contact" => 'required|string',
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->email = $request->email;
        $this->auth_user->contact = $request->contact;

        if ($request->has('password'))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.teacher')])
        );
    }

    private function updateParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email,' . $this->auth_user->id
                . '|unique:students,email',
            "contact" => 'required|string',
            'password' => 'string|min:8|confirmed',
            // 'payment_token',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->email = $request->email;
        $this->auth_user->contact = $request->contact;

        if ($request->has('password'))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.parent')])
        );
    }

    private function updateStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email,'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email,' . $this->auth_user->id,
            "contact" => 'required|string',
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->email = $request->email;
        $this->auth_user->contact = $request->contact;

        if ($request->has('password'))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.student')])
        );
    }
}
