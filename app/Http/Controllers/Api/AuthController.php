<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller
{
    protected $register_validation = [
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'contact_no' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|string|min:8',
    ];

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student', ['only' => ['logout']]);
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
        $validator = Validator::make($req->all(), $this->register_validation);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $verification_number = rand(100000, 999999);

        $temp = array_merge(
            $validator->validated(),
            [
                "verification_number" => $verification_number,
                "password" => Hash::make($req->password),
            ]
        );

        Cache::put($req->email, json_encode($temp), 600);

        // return $this->sendEmail($req->email, $verification_number);

        return response()->json([
            "message" => "Email sent",
            "verification_number" => $verification_number
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/auth/emailverification",
     * summary="Email Verification",
     * description="Verificate by email, verification_number",
     * operationId="authEmailVerification",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","verification_number"},
     *       @OA\Property(property="email", type="string", format="email", example="user@gmail.com"),
     *       @OA\Property(property="verification_number", type="string", format="numeric", example="123456")
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="User created successfully"),
     *       @OA\Property(property="token", type="string", example="hfbluwgyp3rfb24rewubfp3iy4gfp34febqiyhk")
     *        )
     *     ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Something went wrong")
     *        )
     *     )
     * )
     */

    public function emailverification(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'verification_number' => 'required|string'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $val = Cache::pull($req->email);

        if ($val === null)
            return response()->json(["error" => "Email or verification number is wrong"]);

        $val = json_decode($val);

        if ($val->verification_number != $req->verification_number)
            return response()->json(["error" => "Something went wrong!"]);

        $user = User::create([
            "firstname" => $val->firstname,
            "lastname" => $val->lastname,
            "contact_no" => $val->contact_no,
            "email" => $val->email,
            "password" => $val->password,
            "role_id" => 1
        ]);

        $token = auth()->setTTL(60 * 12)->login($user);

        return response()->json([
            'message' => 'User successfully registered',
            'token' => $token,
            'role' => $user->role
        ], 201);
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

        return response()->json([
            'token' => $token,
            'role' => auth($auth_type)->user()->role,
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

    protected function sendEmail($email, $verification_number)
    {
        try {
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $mail->Port = env('MAIL_PORT');

            //Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS'));
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Verification number';
            $mail->Body = "Your verification number is $verification_number";
            $mail->send();

            return response()->json(['message' => 'Email sent']);
        } catch (Exception $e) {
            return response()->json(['message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    }
}
