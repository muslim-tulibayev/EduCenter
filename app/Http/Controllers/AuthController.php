<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\CheckEmailUniqueness;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use CheckEmailUniqueness;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student')->except(['register', 'login']);

        parent::__construct(); // Call parent constructor
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Registration",
     * description="Register by email, password",
     * operationId="authRegister",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password", "firstname", "lastname", "contact_no", "password_confirmation"},
     *       @OA\Property(property="firstname", type="string", format="text", example="John"),
     *       @OA\Property(property="lastname", type="string", format="text", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", format="phone number", example="+998 93 819 88 43"),
     *       @OA\Property(property="password", type="string", format="password, min:8", example="12345678"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="12345678"),
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

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'contact_no' => 'required|string',
            'email' => 'required|email|unique:users,email',
            "role_id" => 'required|exists:roles,id',
            // "branch_id" => 'required|exists:branches,id',
            'password' => 'required|confirmed|string|min:8',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $newUser = User::create([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'contact_no' => $req->contact_no,
            'email' => $req->email,
            "role_id" => $req->role_id,
            // "branch_id" => $req->branch_id,
            'password' => Hash::make($req->password),
        ]);

        return response()->json([
            "message" => "Inactive user has been created successfully.",
        ]);
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

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 422);

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
            $token = null;
            $auth_type = null;
        }

        if (!$token)
            return response()->json(['error' => 'Unauthenticated'], 401);

        switch ($auth_type) {
            case 'api':
                $show = [
                    [],
                ];
                break;
            case 'teacher':
                $show = [
                    [
                        "name" => "my-groups",
                        "window" => "default",
                    ],
                ];
                break;
            case 'parent':
                $show = [
                    [
                        "name" => "my-children",
                        "window" => "default",
                    ],
                    [
                        "name" => "my-cards",
                        "window" => null,
                    ],
                    [
                        "name" => "all-courses",
                        "window" => null,
                    ],
                ];
                break;
            case 'student':
                $show = [
                    [
                        "name" => "my-courses",
                        "window" => "default",
                    ],
                    [
                        "name" => "my-cards",
                        "window" => null,
                    ],
                    [
                        "name" => "all-courses",
                        "window" => null,
                    ],
                ];
                break;
        }

        return response()->json([
            'token' => $token,
            'role' => auth($auth_type)->user()->role,
            'show' => $show
        ]);
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
        return response()->json([
            'message' => 'User logged out'
        ], 201);
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
        return response()->json([
            "data" => $this->auth_user
        ]);
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
     *       required={"firstname", "lastname", "email", "contact_no", "password", "password_confirmation"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="+998 98 887 65 43 45"),
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
        return response()->json([
            "data" => $this->auth_user->branches
        ]);
    }

    /* Privates :) */


    private function updateUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'email',
            'contact_no' => 'required|string',
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->contact_no = $request->contact_no;

        if (isset($request->email)) {
            if ($request->email !== $this->auth_user->email)
                $check = $this->checkForEmailUniqueness($request->email);

            if (!$check)
                return response()->json([
                    "error" => "This email address has already been taken."
                ], 400);

            $this->auth_user->email = $request->email;
        }

        if (isset($request->password))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return response()->json([
            "message" => "User successfully updated"
        ]);
    }

    private function updateTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            "email" => 'email',
            "contact_no" => 'required|string',
            'password' => 'string|min:8|confirmed',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->contact_no = $request->contact_no;

        if (isset($request->email)) {
            if ($request->email !== $this->auth_user->email)
                $check = $this->checkForEmailUniqueness($request->email);

            if (!$check)
                return response()->json([
                    "error" => "This email address has already been taken."
                ], 400);

            $this->auth_user->email = $request->email;
        }

        if (isset($request->password))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return response()->json([
            "message" => "User successfully updated"
        ]);
    }

    private function updateParent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            "email" => 'email',
            "contact_no" => 'required|string',
            'password' => 'string|min:8|confirmed',
            // 'payment_token',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->contact_no = $request->contact_no;

        if (isset($request->email)) {
            if ($request->email !== $this->auth_user->email)
                $check = $this->checkForEmailUniqueness($request->email);

            if (!$check)
                return response()->json([
                    "error" => "This email address has already been taken."
                ], 400);

            $this->auth_user->email = $request->email;
        }

        if (isset($request->password))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return response()->json([
            "message" => "User successfully updated"
        ]);
    }

    private function updateStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            "email" => 'email',
            "contact_no" => 'required|string',
            'password' => 'string|min:8|confirmed',
            // 'payment_token',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $this->auth_user->firstname = $request->firstname;
        $this->auth_user->lastname = $request->lastname;
        $this->auth_user->contact_no = $request->contact_no;

        if (isset($request->email)) {
            if ($request->email !== $this->auth_user->email)
                $check = $this->checkForEmailUniqueness($request->email);

            if (!$check)
                return response()->json([
                    "error" => "This email address has already been taken."
                ], 400);

            $this->auth_user->email = $request->email;
        }

        if (isset($request->password))
            $this->auth_user->password = Hash::make($request->password);

        $this->auth_user->save();

        return response()->json([
            "message" => "User successfully updated"
        ]);
    }
}
