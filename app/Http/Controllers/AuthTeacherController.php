<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Http\Resources\Mark\MarkResourceForExam;
use App\Http\Resources\Mark\MarkResourceForLesson;
use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Student\StudentResourceForMarks;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Mark;
use App\Models\Student;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthTeacherController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $MyGroups;

    public function __construct()
    {
        $this->middleware('auth:teacher');
        parent::__construct();

        $this->middleware(function ($request, $next) {
            $this->MyGroups = $this->auth_user
                ->groups()
                ->with('teacher', 'assistant_teacher', 'course');

            return $next($request);
        });
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups",
     * summary="myGroups",
     * description="myGroups",
     * operationId="authMyGroups",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function groups()
    {
        $groups = $this->MyGroups->orderBydesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: GroupResource::collection($groups),
            pagination: $groups
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups/{group_id}/lessons",
     * summary="Get lessons",
     * description="Get lessons",
     * operationId="lessonsAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="group_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function lessons(string $id)
    {
        $group = $this->MyGroups->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')])
            );

        $lessons = $group->course->lessons()
            ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: LessonResource::collection($lessons),
            pagination: $lessons
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups/{group_id}/exams",
     * summary="Get exams",
     * description="Get exams",
     * operationId="examsAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="group_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function exams(string $id)
    {
        $group = $this->MyGroups->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')])
            );

        $exams = $group->course->exams()
            ->orderByDesc('id')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: ExamResource::collection($exams),
            pagination: $exams
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups/{group_id}/students",
     * summary="Get students",
     * description="Get students",
     * operationId="studentsAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="group_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function students(string $id)
    {
        $group = $this->MyGroups->find($id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')])
            );

        $students = $group->students()
            ->orderByDesc('id')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResource::collection($students),
            pagination: $students
        );
    }

    /**
     * @OA\Post(
     * path="/api/teacher/set-mark",
     * summary="Set mark",
     * description="Set mark",
     * operationId="setMarkAuthTeacher",
     * tags={"AuthTeacher"},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"value", "student_id", "type", "id", "comment"},
     *       @OA\Property(property="value", type="integer", example=1),
     *       @OA\Property(property="student_id", type="integer", example=1),
     *       @OA\Property(property="type", type="string", example="lesson|exam"),
     *       @OA\Property(property="id", type="integer", example=1),
     *       @OA\Property(property="comment", type="string", example="There is some bugs"),
     *    ),
     * ),
     * 
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function setMark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "value" => 'required|integer|in:0,1,2,3,4,5',
            "student_id" => 'required|integer|exists:students,id',
            "type" => 'required|in:lesson,exam',
            "id" => 'required|integer',
            "comment" => 'required|string',
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        if ($request->type === 'lesson')
            $markable = Lesson::find($request->id);
        else
            $markable = Exam::find($request->id);

        if (!$markable)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __($request->type)])
            );

        $student = Student::find($request->student_id);

        $newMark = $student->marks()->create([
            "value" => $request->value,
            "comment" => $request->comment,
            "teacher_id" => $this->auth_user->id,
            "markable_type" => get_class($markable),
            "markable_id" => $markable->id,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.st_marked'),
            data: ["id" => $newMark->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups/{group_id}/lesson/{lesson_id}/get-mark",
     * summary="Get marks for lessons",
     * description="Get marks",
     * operationId="getMarksForLessonAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="group_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="lesson_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function getMarksForLesson(string $group_id, string $lesson_id)
    {
        $group = $this->MyGroups->find($group_id);
        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $group_id]
            );

        $lesson = $group->course->lessons()->find($lesson_id);
        if (!$lesson)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')]),
                data: ["id" => $lesson_id]
            );

        $students = $group->students()
            ->with(['marks' => function ($query) use ($lesson_id) {
                $query
                    ->where('markable_type', Lesson::class)
                    ->where('markable_id', $lesson_id);
            }])
            ->orderByDesc('id')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResourceForMarks::collection($students),
            pagination: $students
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups/{group_id}/exam/{exam_id}/get-mark",
     * summary="Get marks for exams",
     * description="Get marks",
     * operationId="getMarksForExamAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="group_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="lesson_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function getMarksForExam(string $group_id, string $exam_id)
    {
        $group = $this->MyGroups->find($group_id);
        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.group')]),
                data: ["id" => $group_id]
            );

        $exam = $group->course->exams()->find($exam_id);
        if (!$exam)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.exam')]),
                data: ["id" => $exam_id]
            );

        $students = $group->students()
            ->with(['marks' => function ($query) use ($exam_id) {
                $query
                    ->where('markable_type', Exam::class)
                    ->where('markable_id', $exam_id);
            }])
            ->orderByDesc('id')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResourceForMarks::collection($students),
            pagination: $students
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/get-mark/{mark_id}/lesson",
     * summary="Get mark for lesson",
     * description="Get marks",
     * operationId="getMarkForLessonAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="mark_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function getMarkForLesson(string $id)
    {
        $groupIds = $this->MyGroups->pluck('id');

        $mark = Mark::with('teacher', 'student', 'markable')
            ->where('markable_type', Lesson::class)
            ->where('id', $id)
            ->whereHas('student.groups', function ($query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds);
            })
            ->first();

        if (!$mark)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.mark')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: MarkResourceForLesson::make($mark)
        );
    }

    /**
     * @OA\Get(
     * path="/api/teacher/get-mark/{mark_id}/exam",
     * summary="Get mark for exam",
     * description="Get marks",
     * operationId="getMarkForExamAuthTeacher",
     * tags={"AuthTeacher"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="mark_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function getMarkforExam(string $id)
    {
        $groupIds = $this->MyGroups->pluck('id');

        $mark = Mark::with('teacher', 'student', 'markable')
            ->where('markable_type', Exam::class)
            ->where('id', $id)
            ->whereHas('student.groups', function ($query) use ($groupIds) {
                $query->whereIn('groups.id', $groupIds);
            })
            ->first();

        if (!$mark)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.mark')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: MarkResourceForExam::make($mark)
        );
    }
}
