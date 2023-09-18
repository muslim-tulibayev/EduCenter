<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Exam\ExamResource;
use App\Models\Exam;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');
        parent::__construct('exams', true);
    }

    // public function index()
    // {
    //     //
    // }

    /**
     * @OA\Post(
     * path="/api/manage/exam",
     * summary="Set new Exam",
     * description="Exam store",
     * operationId="storeExam",
     * tags={"Exam"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "course_id"},
     *       @OA\Property(property="name", type="string", example="New Exam"),
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "course_id" => 'required|exists:courses,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newExam = Exam::create([
            "name" => $request->name,
            "course_id" => $request->course_id,
        ]);

        return $this->sendResponse(
            success: true,
            status: 201,
            message: trans('msg.created', ['attribute' => __('msg.attributes.exam')]),
            data: ["id" => $newExam->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/exam/{id}",
     * summary="Get specific Exam data",
     * description="Exam show",
     * operationId="showExam",
     * tags={"Exam"},
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
        $exam = Exam::with('course')->find($id);

        if (!$exam)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.exam')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: ExamResource::make($exam)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/exam/{id}",
     * summary="Update specific Exam",
     * description="Exam update",
     * operationId="updateExam",
     * tags={"Exam"},
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
     *       @OA\Property(property="name", type="string", example="New exam"),
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

    public function update(Request $request, string $id)
    {
        $exam = Exam::find($id);

        if (!$exam)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.exam')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "name" => 'required|string',
            "course_id" => 'required|integer|exists:courses,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $exam->update([
            'name' => $request->name,
            'course_id' => $request->course_id,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.exam')]),
            data: ["id" => $exam->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/exam/{id}",
     * summary="Delete specific exam",
     * description="Exam delete",
     * operationId="destroyExam",
     * tags={"Exam"},
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
        $exam = Exam::find($id);

        if (!$exam)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.exam')]),
                data: ["id" => $id]
            );

        $exam->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.exam')]),
            data: ["id" => $exam->id]
        );
    }
}
