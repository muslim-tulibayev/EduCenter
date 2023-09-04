<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Stparent;
use App\Traits\CheckEmailUniqueness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    use CheckEmailUniqueness;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        
        parent::__construct('stparents');
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
     * path="/api/manage/parent/{id}",
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
        $parent = Stparent::find($id);
        if ($parent === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email',
            'password' => 'confirmed|string|min:8',
            'contact_no' => 'required|string'
        ]);

        if ($parent->email !== $req->email) {
            $check = $this->checkForEmailUniqueness($req->email);

            if (!$check) {
                return response([
                    "email" => [
                        "The email has already been taken."
                    ]
                ]);
            }
        }

        if ($validator->fails())
            return response()->json($validator->messages());

        $parent->update($validator->validated());

        // if (auth('api')->user() !== null)
        //     auth('api')->user()->makeChanges(
        //         'Parent updated from $val1 to $val2',
        //         '$col-name',
        //         $parent
        //     );

        // if (auth('parent')->user() !== null)
        //     auth('parent')->user()->makeChanges(
        //         'Parent updated from $val1 to $val2',
        //         '$col-name',
        //         $parent
        //     );

        return response()->json([
            "message" => "Parent updated successfully",
            "parent" => $parent->id
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/manage/parent/{id}",
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
        $parent = Stparent::find($id);

        if ($parent === null)
            return response()->json(["error" => "Not found"]);

        // auth('api')->user()->makeChanges(
        //     'Parent deleted',
        //     'deleted',
        //     $parent
        // );

        $parent->delete();

        return response()->json([
            "message" => "parent deleted successfully",
            "parent" => $id
        ]);
    }
}
