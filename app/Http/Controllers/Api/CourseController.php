<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,teacher,parent,student');
        $this->middleware('auth:api', ["only" => ['update', 'store', 'destroy']]);
    }

    /**
     * @OA\Get(
     * path="/api/course",
     * summary="Get all courses data",
     * description="Course index",
     * operationId="indexCourse",
     * tags={"Course"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $courses = Course::orderByDesc('id')->paginate();

        // if (auth('api')->user())
        //     return CourseResource::collection($courses);

        return CourseResource::collection($courses);
    }

    /**
     * @OA\Post(
     * path="/api/course",
     * summary="Set new course",
     * description="Course store",
     * operationId="storeCourse",
     * tags={"Course"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "price"},
     *       @OA\Property(property="name", type="string", example="New Course"),
     *       @OA\Property(property="price", type="numeric", example=1000000),
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
            "price" => 'required|numeric',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $newCourse = Course::create([
            'name' => $req->name,
            'price' => $req->price
        ]);

        auth('api')->user()->makeChanges(
            'New course created',
            'created',
            $newCourse
        );

        return response()->json([
            "message" => "Course created successfully",
            "course" => $newCourse->id,
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/course/{id}",
     * summary="Get specific course data",
     * description="Course show",
     * operationId="showCourse",
     * tags={"Course"},
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
        $course = Course::find($id);

        if ($course === null)
            return response()->json(["error" => "Not found"]);

        // if (auth('api')->user())
        //     return new CourseResource($course);

        return new CourseResource($course);
    }

    /**
     * @OA\Put(
     * path="/api/course/{id}",
     * summary="Update specific course",
     * description="Course update",
     * operationId="updateCourse",
     * tags={"Course"},
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
     *       required={"name", "price"},
     *       @OA\Property(property="name", type="string", example="New Course"),
     *       @OA\Property(property="price", type="numeirc", example=1000000),
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
        $course = Course::find($id);

        if ($course === null)
            return response()->json(["error" => "Not found"]);

        $validator = Validator::make($req->all(), [
            "name" => 'required|string',
            "price" => 'required|numeric',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $course->update([
            'name' => $req->name,
            'price' => $req->price,
        ]);

        auth('api')->user()->makeChanges(
            'Course updated from $val1 to $val2',
            '$col-name',
            $course
        );

        return response()->json([
            "message" => "Course updated successfully",
            "course" => $course->id,
        ]);
    }

    /**
     * @OA\Delete(
     * path="/api/course/{id}",
     * summary="Delete specific course",
     * description="Course delete",
     * operationId="destroyCourse",
     * tags={"Course"},
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
        $course = Course::find($id);
        if ($course === null)
            return response()->json(["error" => "Not found"]);

        auth('api')->user()->makeChanges(
            'Course deleted',
            'deleted',
            $course
        );

        $course->delete();

        return response()->json([
            "message" => "Course deleted successfully",
            "course" => $id
        ]);
    }

    /**
     * @OA\Get(
     * path="/api/course/{id}/lessons",
     * summary="Get specific course lessons",
     * description="Course lessons",
     * operationId="lessonsCourse",
     * tags={"Course"},
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

    public function lessons(string $id)
    {
        $course = Course::find($id);
        if ($course === null)
            return response()->json(["error" => "Not found"]);

        $lessons = $course->lessons()->orderBy('sequence_number')->paginate();

        // if (auth('api')->user())
        //     return CourseLessonResourceForAdmin::collection($lessons);

        return LessonResource::collection($lessons);
    }
}
