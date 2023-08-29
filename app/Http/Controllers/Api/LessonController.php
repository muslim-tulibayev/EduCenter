<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        // $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy']]);

        parent::__construct('lessons');
    }

    public function index()
    {
        //
    }

    /**
     * @OA\Post(
     * path="/api/lesson",
     * summary="Set new lesson",
     * description="Lesson store",
     * operationId="storeLesson",
     * tags={"Lesson"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "course_id"},
     *       @OA\Property(property="sequence_number", type="string", example="1.5"),
     *       @OA\Property(property="name", type="string", example="New Lesson"),
     *       @OA\Property(property="course_id", type="numeric", example=1),
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

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            "sequence_number" => 'required|string',
            // |unique:',
            "name" => 'required|string',
            "course_id" => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newLesson = Lesson::create([
            'sequence_number' => $req->sequence_number,
            'name' => $req->name,
            'course_id' => $req->course_id,
        ]);

        return response()->json([
            "message" => "Lesson created successfully",
            "lesson" => $newLesson->id,
        ]);
    }

    public function show(string $id)
    {
        //
    }

    /**
     * @OA\Put(
     * path="/api/lesson/{id}",
     * summary="Update specific lesson",
     * description="Lesson update",
     * operationId="updateLesson",
     * tags={"Lesson"},
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
     *       required={"name", "course_id"},
     *       @OA\Property(property="sequence_number", type="string", example="1.5"),
     *       @OA\Property(property="name", type="string", example="New Lesson"),
     *       @OA\Property(property="course_id", type="numeric", example=1),
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
        $lesson = Lesson::find($id);

        if ($lesson === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            "sequence_number" => 'required|string',
            // |unique:',
            "name" => 'required|string',
            "course_id" => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $lesson->update([
            'sequence_number' => $req->sequence_number,
            'name' => $req->name,
            'course_id' => $req->course_id,
        ]);

        return response()->json([
            "message" => "Lesson updated successfully",
            "lesson" => $lesson->id,
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/lesson/{id}",
     * summary="Delete specific lesson",
     * description="Lesson delete",
     * operationId="destroyLesson",
     * tags={"Lesson"},
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
        $lesson = Lesson::find($id);
        if ($lesson === null)
            return response()->json(["error" => "Not found"]);

        $lesson->delete();

        return response()->json([
            "message" => "lesson deleted successfully",
            "lesson" => $id
        ]);
    }
}
