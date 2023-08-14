<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Branch\BranchResource;
use App\Models\Branch;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy', 'rooms', 'addRooms']]);
    }

    /**
      * @OA\Get(
      * path="/api/branch",
      * summary="Branch index",
      * description="Get all branches data",
      * operationId="index",
      * tags={"Branch"},
      * security={ {"bearerAuth": {} }},
      * @OA\Response(
      *    response=401,
      *    description="Wrong credentials response",
      *    @OA\JsonContent(
      *       @OA\Property(property="message", type="string", example="Unauthorized")
      *        )
      *     )
      * )
      */

    public function index()
    {
        $branches = Branch::orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return BranchResource::collection($branches);

        return BranchResource::collection($branches);
    }

    /**
      * @OA\Post(
      * path="/api/branch",
      * summary="Branch store",
      * description="Set new branch",
      * operationId="store",
      * tags={"Branch"},
      * security={ {"bearerAuth": {} }},
      * @OA\RequestBody(
      *    required=true,
      *    description="Pass user credentials",
      *    @OA\JsonContent(
      *       required={"name","location"},
      *       @OA\Property(property="name", type="string", format="text", example="Bukhara"),
      *       @OA\Property(property="location", type="string", format="text", example="address"),
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

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "name" => 'required|string',
            "location" => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newBranch = Branch::create([
            'name' => $req->name,
            'location' => $req->location,
        ]);

        auth('api')->user()->makeChanges(
            'New branch created',
            'created',
            $newBranch
        );

        return response()->json([
            "message" => "Branch created successfully",
            "branch" => $newBranch->id,
        ]);
    }

    /**
      * @OA\Get(
      * path="/api/branch/{id}",
      * summary="Branch show",
      * description="Get specific branch data",
      * operationId="show",
      * tags={"Branch"},
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

    public function show(string $id)
    {
        $branch = Branch::find($id);

        if ($branch === null)
            return response()->json(["error" => "Not found"]);

        // if (auth('api')->user())
        //     return new BranchResource($branch);

        return new BranchResource($branch);
    }

    /**
      * @OA\Put(
      * path="/api/branch/{id}",
      * summary="Branch update",
      * description="Update specific branch",
      * operationId="update",
      * tags={"Branch"},
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
      *       required={"name","location"},
      *       @OA\Property(property="name", type="string", format="text", example="Bukhara"),
      *       @OA\Property(property="location", type="string", format="text", example="address"),
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
        $branch = Branch::find($id);

        if ($branch === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            "name" => 'required|string',
            "location" => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $branch->update([
            'name' => $req->name,
            'location' => $req->location,
        ]);

        auth('api')->user()->makeChanges(
            'Branch updated from $val1 to $val2',
            '$col-name',
            $branch
        );

        return response()->json([
            "message" => "Branch updated successfully",
            "branch" => $branch->id,
        ]);
    }

    /**
      * @OA\Delete(
      * path="/api/branch/{id}",
      * summary="Branch delete",
      * description="Delete specific branch",
      * operationId="destroy",
      * tags={"Branch"},
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
        $branch = Branch::find($id);

        if ($branch === null)
            return response()->json(["error" => "Not found"]);

        $branch->delete();

        auth('api')->user()->makeChanges(
            'Branch deleted',
            'deleted',
            $branch
        );

        return response()->json([
            "message" => "Branch deleted successfully",
            "branch" => $id,
        ]);        
    }

    /**
      * @OA\Get(
      * path="/api/branch/{id}/rooms",
      * summary="Branch rooms",
      * description="Get specific branch rooms",
      * operationId="rooms",
      * tags={"Branch"},
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

    public function rooms(string $id)
    {
        $branch = Branch::find($id);

        if ($branch === null)
            return response()->json(["error" => "Not found"]);

        $temp = $branch->rooms()->orderBy('name')->paginate();

        return response()->json($temp);
    }

    /**
      * @OA\Post(
      * path="/api/branch/{id}/rooms",
      * summary="Branch addRooms",
      * description="Set rooms the specific branch",
      * operationId="addRooms",
      * tags={"Branch"},
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
      *       required={"rooms"},
      *       @OA\Property(
      *         property="rooms", type="array", collectionFormat="multi",
      *         @OA\Items(type="string", example="hello"),
      *      ),
      *    ),
      * ),
      *
      * @OA\Response(
      *    response=401,
      *    description="Wrong credentials response",
      *    @OA\JsonContent(
      *       @OA\Property(property="message", type="string", example="Unauthorized")
      *     )
      *   )
      * )
      */

    public function addRooms(Request $req, string $id)
    {
        $branch = Branch::find($id);

        if ($branch === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            "rooms" => 'required|array',
            // unique in one specific branch
            // "rooms.*" => 'required|string|distinct|unique:rooms,name', 
            "rooms.*" => 'required|string|distinct',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        foreach ($req->rooms as $room)
            $branch->rooms()->create(["name" => $room]);

        auth('api')->user()->makeChanges(
            'Branch updated from $val1 to $val2',
            '$col-name',
            $branch
        );

        return response()->json([
            "message" => "Rooms has been added successfully"
        ]);
    }
}
