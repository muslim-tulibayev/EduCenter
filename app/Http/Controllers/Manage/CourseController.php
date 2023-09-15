<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Models\Branch;
use App\Models\Course;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Course;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('courses', true);

        $this->middleware(function ($request, $next) {
            $this->Course = Branch::find($this->auth_branch_id)
                ->courses();

            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['lessons'] >= 1))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('lessons');
    }

    /**
     * @OA\Get(
     * path="/api/manage/course",
     * summary="Get all courses data",
     * description="Course index",
     * operationId="indexCourse",
     * tags={"Course"},
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
        $courses = $this->Course->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_courses',
            data: CourseResource::collection($courses),
            pagination: $courses
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/course",
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
            "name" => 'required|string',
            "price" => 'required|numeric',
            "branches" => 'required|array',
            "branches.*" => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newCourse = Course::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        $newCourse->branches()->attach($request->branches);

        // auth('api')->user()->makeChanges(
        //     'New course created',
        //     'created',
        //     $newCourse
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'course_created',
            message: trans('msg.created', ['attribute' => __('msg.attributes.course')]),
            data: ["id" => $newCourse->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/course/{id}",
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
        $course = $this->Course->find($id);

        if (!$course)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'course_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_course',
            data: CourseResource::make($course)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/course/{id}",
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
        $course = $this->Course->find($id);

        if (!$course)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'course_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "price" => 'required|numeric',
            "branches" => 'required|array',
            "branches.*" => 'numeric|distinct|exists:branches,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $course->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        $course->branches()->sync($request->branches);

        // auth('api')->user()->makeChanges(
        //     'Course updated from $val1 to $val2',
        //     '$col-name',
        //     $course
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'course_updated',
            message: trans('msg.updated', ['attribute' => __('msg.attributes.course')]),
            data: ["id" => $course->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/course/{id}",
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
        $course = $this->Course->find($id);

        if (!$course)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'course_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')]),
                data: ["id" => $id]
            );

        $course->delete();

        // auth('api')->user()->makeChanges(
        //     'Course deleted',
        //     'deleted',
        //     $course
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'course_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.course')]),
            data: ["id" => $course->id]
        );
    }

    public function lessons(string $id)
    {
        $course = $this->Course->find($id);

        if (!$course)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'course_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')]),
                data: ["id" => $id]
            );

        $lessons = $course->lessons()
            ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_lessons',
            data: LessonResource::collection($lessons),
            pagination: $lessons
        );
    }
}

    // /**
    //  * @OA\Get(
    //  * path="/api/manage/course/{id}/lessons",
    //  * summary="Get specific course lessons",
    //  * description="Course lessons",
    //  * operationId="lessonsCourse",
    //  * tags={"Course"},
    //  * security={ {"bearerAuth": {} }},
    //  *
    //  * @OA\Parameter(
    //  *    in="path",
    //  *    name="id",
    //  *    required=true,
    //  *    description="ID to fetch the targeted campaigns.",
    //  *    @OA\Schema(type="string")
    //  * ),
    //  *
    //  * @OA\Response(
    //  *    response=403,
    //  *    description="Wrong credentials response",
    //  *    @OA\JsonContent(
    //  *       @OA\Property(property="message", type="string", example="Unauthorized")
    //  *        )
    //  *     )
    //  * )
    //  */
    // public function lessons(string $id)
    // {
    //     $course = Course::find($id);
    //     if ($course === null)
    //         return response()->json(["error" => "Not found"]);
    //     $lessons = $course->lessons()->orderBy('sequence_number')->paginate();
    //     // if (auth('api')->user())
    //     //     return CourseLessonResourceForAdmin::collection($lessons);
    //     return LessonResource::collection($lessons);
    // }
