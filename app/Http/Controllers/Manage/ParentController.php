<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Parent\ParentResource;
use App\Models\Stparent;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $Stparent;

    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');

        parent::__construct('stparents', true);

        $this->middleware(function ($request, $next) {
            $this->Stparent = Stparent::whereHas('students.groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            });

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent",
     * summary="Get all parents data",
     * description="Parent index",
     * operationId="indexParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $parents = $this->Stparent->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_parents',
            data: ParentResource::collection($parents),
            pagination: $parents
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/parent",
     * summary="Set new Parent",
     * description="Parent store",
     * operationId="storeParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact_no", "role_id", "students"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="+998 56 789 09 87"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            "contact_no" => 'required|string',
            "role_id" => 'required|exists:roles,id',
            'students' => 'required|array',
            'students.*' => 'numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newParent = Stparent::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'role_id' => $request->role_id,
        ]);

        $newParent->students()->attach($request->students);

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'parent_created',
            data: ["id" => $newParent->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent/{id}",
     * summary="Get specific parent data",
     * description="Parent show",
     * operationId="showParent",
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

    public function show(string $id)
    {
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'parent_not_found',
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'get_parent',
            data: ParentResource::make($parent)
        );
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
     *       required={"firstname", "lastname", "email", "contact_no", "role_id", "students"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact_no", type="string", example="+998 56 789 09 87"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
     *      ),
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
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'parent_not_found',
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email,' . $id
                . '|unique:students,email',
            "contact_no" => 'required|string',
            "role_id" => 'required|exists:roles,id',
            'students' => 'required|array',
            'students.*' => 'numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $parent->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'role_id' => $request->role_id,
        ]);

        $parent->students()->sync($request->students);

        // if (auth('parent')->user() !== null)
        //     auth('parent')->user()->makeChanges(
        //         'Parent updated from $val1 to $val2',
        //         '$col-name',
        //         $parent
        //     );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'parent_updated',
            data: ["id" => $parent->id]
        );
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
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'parent_not_found',
                data: ["id" => $id]
            );

        $parent->delete();

        // auth('api')->user()->makeChanges(
        //     'Parent deleted',
        //     'deleted',
        //     $parent
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'parent_deleted',
            data: ["id" => $parent->id]
        );
    }
}
