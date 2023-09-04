<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnactiveUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');

        parent::__construct('users');
    }

    /**
     * @OA\Get(
     * path="/api/manage/user/unactive",
     * summary="Get all UnactiveUser data",
     * description="UnactiveUser index",
     * operationId="indexUnactiveUser",
     * tags={"UnactiveUser"},
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
        $users = User::where('status', false)->orderByDesc('id')->paginate();

        return response()->json([
            "data" => $users
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/manage/user/unactive/{id}",
     * summary="Get specific UnactiveUser data",
     * description="UnactiveUser show",
     * operationId="showUnactiveUser",
     * tags={"UnactiveUser"},
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
        $user = User::where('status', false)->where('id', $id);

        if ($user === null)
            return response()->json(['error' => 'Not found']);

        return response()->json([
            "data" => $user
        ]);
    }

    /**
     * @OA\Put(
     * path="/api/manage/user/unactive/{id}",
     * summary="Update specific UnactiveUser",
     * description="UnactiveUser update",
     * operationId="updateUnactiveUser",
     * tags={"UnactiveUser"},
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
     *       required={"firstname", "lastname", "contact_no", "email","role","branch_id", "password"},
     *       @OA\Property(property="firstname", type="string", example="address"),
     *       @OA\Property(property="lastname", type="string", example="address"),
     *       @OA\Property(property="contact_no", type="string", example="address"),
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
        $user = User::where('status', false)->where('id', $id)->first();

        if (!$user)
            return response()->json([
                "error" => "not found"
            ]);

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'contact_no' => 'required|string',
            'email' => 'required|email|unique:users,email',
            "role_id" => 'required|exists:roles,id',
            "branch_id" => 'required|exists:branches,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages(), 400);

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'contact_no' => $request->contact_no,
            'email' => $request->email,
            "role_id" => $request->role_id,
            "branch_id" => $request->branch_id,
            'status' => $request->status,
        ]);

        return response()->json([
            "message" => "Unactive user has been created successfully.",
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/manage/user/unactive/{id}",
     * summary="Delete specific UnactiveUser",
     * description="UnactiveUser delete",
     * operationId="destroyUnactiveUser",
     * tags={"UnactiveUser"},
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
        $user = User::where('status', false)->where('id', $id)->first();

        if ($user === null)
            return response()->json(["error" => "Not found"]);

        $user->delete();

        return response()->json([
            "message" => "Unactive user deleted successfully",
            "user" => $user->id
        ]);
    }
}
