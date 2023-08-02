<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stparent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student', ["except" => ['login']]);
        $this->middleware('auth:api,parent', ["only" => ['update']]);
        $this->middleware('auth:api', ["only" => ['store', 'destroy']]);
        $this->middleware('auth:parent', ["only" => ['logout']]);
    }

    public function index()
    {
        // 
    }

    public function store(Request $req)
    {
        // 
    }

    public function show(string $id)
    {
        //
    }

    /**
     * @OA\Put(
     * path="/api/parent/{id}",
     * summary="Update specific parent",
     * description="Parent update",
     * operationId="updateParent",
     * tags={"Parent"},
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
        $parent = Stparent::find($id);
        if ($parent === null)
            return response()->json(["error" => "Not found"]);

        if (auth('parent')->user() !== null) {
            if (auth('parent')->user()->id != $id) {
                return response()->json(["error" => "Unauthorized"], 401);
            }
        }

        $validator = Validator::make($req->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'confirmed|string|min:8',
            'contact_no' => 'required|string'
        ]);

        if ($parent->email !== $req->email) {
            $found = Stparent::where('email', '=', $req->email)->first();
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

        if ($req->has('password') && (auth('parent')->user() !== null)) {
            $parent->password = Hash::make($req->password);
            $parent->save();
        }

        $parent->update([
            'firstname' => $req->firstname,
            'lastname' => $req->lastname,
            'email' => $req->email,
            'contact_no' => $req->contact_no,
        ]);

        if (auth('api')->user() !== null)
            auth('api')->user()->makeChanges(
                'Parent updated from $val1 to $val2',
                '$col-name',
                $parent
            );

        if (auth('parent')->user() !== null)
            auth('parent')->user()->makeChanges(
                'Parent updated from $val1 to $val2',
                '$col-name',
                $parent
            );

        return response()->json([
            "message" => "Parent updated successfully",
            "parent" => $parent->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/parent/{id}",
     * summary="Delete specific parent",
     * description="Parent delete",
     * operationId="destroyParent",
     * tags={"Parent"},
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
        $parent = Stparent::find($id);
        if ($parent === null)
            return response()->json(["error" => "Not found"]);

        auth('api')->user()->makeChanges(
            'Parent deleted',
            'deleted',
            $parent
        );

        $parent->delete();

        return response()->json([
            "message" => "parent deleted successfully",
            "parent" => $id
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/parent/login",
     * summary="Login",
     * description="Login by email, password",
     * operationId="parentLogin",
     * tags={"Parent"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Parent credentials",
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

        $token = auth('parent')->setTTL(60 * 12)->attempt($validator->validated());

        if (!$token)
            return response()->json(['error' => 'Unauthorized'], 401);

        return response(['token' => $token]);
    }

    /**
     * @OA\Get(
     * path="/api/parent/logout",
     * summary="Logout",
     * description="Logout",
     * operationId="parentLogout",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Parent logged out")
     *        )
     *     )
     * )
     */

    public function logout()
    {
        auth('parent')->logout();
        return response()->json(['message' => 'Parent logged out'], 201);
    }
}
