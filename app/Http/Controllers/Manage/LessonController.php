<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Lesson\LessonResource;
use App\Models\Lesson;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');
        parent::__construct('lessons', true);
    }

    /**
     * @OA\Post(
     * path="/api/manage/lesson",
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
            "course_id" => 'required|integer|exists:courses,id',
            "sequence_number" => 'required|string',
            "name" => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newLesson = Lesson::create([
            'course_id' => $request->course_id,
            'sequence_number' => $request->sequence_number,
            'name' => $request->name,
        ]);

        return $this->sendResponse(
            success: true,
            status: 201,
            // name: 'lesson_created',
            message: trans('msg.created', ['attribute' => __('msg.attributes.lesson')]),
            data: ["id" => $newLesson->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/lesson/{id}",
     * summary="Get specific Lesson data",
     * description="Lesson show",
     * operationId="showLesson",
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

    public function show(string $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'lesson_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_lesson',
            data: LessonResource::make($lesson)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/lesson/{id}",
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
        $lesson = Lesson::find($id);

        if (!$lesson)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'lesson_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "course_id" => 'required|integer|exists:courses,id',
            "sequence_number" => 'required|string',
            "name" => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $lesson->update([
            'course_id' => $request->course_id,
            'sequence_number' => $request->sequence_number,
            'name' => $request->name,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'lesson_updated',
            message: trans('msg.updated', ['attribute' => __('msg.attributes.lesson')]),
            data: ["id" => $lesson->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/lesson/{id}",
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

        if (!$lesson)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'lesson_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')]),
                data: ["id" => $id]
            );

        $lesson->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'lesson_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.lesson')]),
            data: ["id" => $lesson->id]
        );
    }
}


/**
 * @OA\Get(
 * path="/api/manage/lesson",
 * summary="Get all Lessons data",
 * description="Lesson index",
 * operationId="indexLesson",
 * tags={"Lesson"},
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

// public function index()
// {
//     // $lessons = $this->Lesson->orderByDesc('sequence_number')->paginate();
//     $lessons = $this->Lesson
//         ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
//         ->paginate();
//     return $this->sendResponse(
//         success: true,
//         status: 200,
//         name: 'get_lessons',
//         data: LessonResource::collection($lessons),
//         pagination: $lessons
//     );
// }