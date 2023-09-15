<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Group\GroupResource;
use App\Http\Resources\Lesson\LessonResource;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;

class AuthTeacherController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    // private $MyGroups;

    public function __construct()
    {
        $this->middleware('auth:teacher');

        parent::__construct();

        // $this->middleware(function ($request, $next) {
        //     $this->MyGroups = $this->auth_user->groups();

        //     if (!count($this->MyGroups))
        //         return $this->sendResponse(
        //             success: false,
        //             status: 404,
        //             name: 'teacher_has_no_groups',
        //         );

        //     return $next($request);
        // });
    }

    /**
     * @OA\Get(
     * path="/api/teacher/my-groups",
     * summary="myGroups",
     * description="myGroups",
     * operationId="authMyGroups",
     * tags={"AuthTeacher"},
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

    public function groups()
    {
        $groups = $this->auth_user->groups()->with('teacher', 'assistant_teacher', 'course')->get();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_my_groups',
            data: GroupResource::collection($groups)
        );
    }

    public function course(string $id)
    {
        $group = $this->auth_user->groups()->where('course_id', $id)->first();

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'course_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.course')])
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_teacher_course',
            data: CourseResource::make($group->course)
        );
    }

    public function lessons(string $id)
    {
        $group = $this->auth_user->groups()->where('course_id', $id)->first();

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'lessons_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.lesson')])
            );

        $lessons = $group->course->lessons()
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
