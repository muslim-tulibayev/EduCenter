<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\PaymentMethods;
use App\Http\Resources\AccessForCourse\AccessForCourseResource;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Exam\ExamResourceForStudent;
use App\Http\Resources\Lesson\LessonResourceForStudent;
use App\Http\Resources\Mark\MarkResourceForExam;
use App\Http\Resources\Mark\MarkResourceForLesson;
use App\Http\Resources\Student\StudentResource;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Student;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthParentController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $payment;

    public function __construct()
    {
        $this->middleware('auth:parent');
        $this->payment = new PaymentMethods();
        parent::__construct();
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children",
     * summary="myChildren",
     * description="myChildren",
     * operationId="authMyChildren",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function myChildren()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResource::collection($this->auth_user->students)
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/all-courses",
     * summary="AllCourses",
     * description="AllCourses",
     * operationId="authParentAllCourses",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function allCourses()
    {
        //! write: if ($request->has('branch_filter'))

        $courses = Course::orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: CourseResource::collection($courses),
            pagination: $courses
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children/{student_id}/courses",
     * summary="Get courses",
     * description="Get courses",
     * operationId="getCoursesAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
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

    public function getCourses(string $id)
    {
        $student = $this->auth_user->students()->find($id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ['id' => $id]
            );

        $access_for_courses = $student->accessForCourses()->with('course')->get();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: AccessForCourseResource::collection($access_for_courses)
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children/{student_id}/course/{course_id}/lessons",
     * summary="Get lessons",
     * description="Get lessons",
     * operationId="getLessonsAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="course_id",
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

    public function getLessons(string $student_id, string $course_id)
    {
        $student = $this->auth_user->students()->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ['id' => $student_id]
            );

        $accessForCourse = $student->accessForCourses()->where('course_id', $course_id)->first();

        if (!$accessForCourse)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')])
            );

        $lessons = $accessForCourse->course->lessons()
            ->with(['markable' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: LessonResourceForStudent::collection($lessons),
            pagination: $lessons
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children/{student_id}/course/{course_id}/exams",
     * summary="Get exams",
     * description="Get exams",
     * operationId="getExamsAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="course_id",
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

    public function getExams(string $student_id, string $course_id)
    {
        $student = $this->auth_user->students()->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ['id' => $student_id]
            );

        $accessForCourse = $student->accessForCourses()->where('course_id', $course_id)->first();

        if (!$accessForCourse)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')])
            );

        $exams = $accessForCourse->course->exams()
            ->with(['markable' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            // ->orderByRaw('CAST(SUBSTRING_INDEX(sequence_number, " ", 1) AS UNSIGNED) DESC')
            ->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: ExamResourceForStudent::collection($exams),
            pagination: $exams
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children/{student_id}/get-mark/{lesson_id}/lesson",
     * summary="Get lessons",
     * description="Get lessons",
     * operationId="getMarkForLessonAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
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

    public function getMarkForLesson(string $student_id, string $lesson_id)
    {
        $student = $this->auth_user->students()->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ['id' => $student_id]
            );

        $mark = $student->marks()
            ->with('teacher', 'student', 'markable')
            ->where('markable_type', Lesson::class)
            ->where('markable_id', $lesson_id)
            ->first();

        if (!$mark)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.mark')]),
                data: ["id" => $lesson_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: MarkResourceForLesson::make($mark),
        );
    }

    /**
     * @OA\Get(
     * path="/api/parent/my-children/{student_id}/get-mark/{exam_id}/exams",
     * summary="Get examss",
     * description="Get examss",
     * operationId="getMarkForExamsAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="exam_id",
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

    public function getMarkForExam(string $student_id, string $exam_id)
    {
        $student = $this->auth_user->students()->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ['id' => $student_id]
            );

        $mark = $student->marks()
            ->with('teacher', 'student', 'markable')
            ->where('markable_type', Exam::class)
            ->where('markable_id', $exam_id)
            ->first();

        if (!$mark)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.mark')]),
                data: ["id" => $exam_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: MarkResourceForExam::make($mark),
        );
    }


    /**
     * @OA\Get(
     * path="/api/parent/my-cards",
     * summary="MyCards",
     * description="MyCards",
     * operationId="authParentMyCards",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function myCards()
    {
        return $this->sendResponse(
            success: true,
            status: 200,
            data: CardResource::collection($this->auth_user->cards)
        );
    }

    /**
     * @OA\Post(
     * path="/api/parent/add-card",
     * summary="Add new card",
     * description="Add Card",
     * operationId="addCardAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"card_number", "card_expiration", "card_token"},
     *       @OA\Property(property="card_number", type="string", example="1234567890123456"),
     *       @OA\Property(property="card_expiration", type="string", example="01/25"),
     *       @OA\Property(property="card_token", type="string", example="4345678987653773hgfkdfu34hf3fhiuerifr4345678987653773hgfkdfu34hf3fhiuerifr4345678987653773hgfkdfu34hf3fhiuerifr"),
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

    public function addCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|unique:cards,card_number',
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newCard = $this->auth_user->cards()->create([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.added', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $newCard->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/parent/delete-card/{id}",
     * summary="Delete card",
     * description="Delete card",
     * operationId="deleteCardAuthParent",
     * tags={"AuthParent"},
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

    public function deleteCard(string $id)
    {
        return $this->payment->deleteCard($id);
    }

    /**
     * @OA\Get(
     * path="/api/parent/cashier",
     * summary="Get Cashier",
     * description="GetCashier",
     * operationId="authParentGetCashier",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthenticated")
     *        )
     *     )
     * )
     */

    public function cashierId()
    {
        return $this->payment->cashierId();
    }

    /**
     * @OA\Post(
     * path="/api/parent/pay-for-course",
     * summary="Pay for course",
     * description="Pay for course",
     * operationId="payForCourseAuthParent",
     * tags={"AuthParent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"student_id", "course_id", "card_id"},
     *       @OA\Property(property="student_id", type="numeric", example=1),
     *       @OA\Property(property="course_id", type="numeric", example=1),
     *       @OA\Property(property="card_id", type="numeric", example=1),
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

    public function payForCourse(Request $request)
    {
        return $this->payment->payForParent($request);
    }
}
