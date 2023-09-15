<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Student\StudentResourceForSearch;
use App\Models\Role;
use App\Models\Stparent;
use App\Models\Student;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $Student;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('students', true);

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['student_search'] >= 1))
                $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('search');

        $this->middleware(function ($request, $next) {
            $this->Student = Student::whereHas('groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            });

            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 1))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('getCards');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 1))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('getCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 2))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('updateCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 3))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('storeCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 4))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    // name: 'unauthorized',
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('destroyCard');
    }

    /**
     * @OA\Get(
     * path="/api/manage/student",
     * summary="Get all students data",
     * description="Student index",
     * operationId="indexStudent",
     * tags={"Student"},
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
        $students = $this->Student->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "all_students",
            data: StudentResource::collection($students),
            pagination: $students
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/student",
     * summary="Set new student",
     * description="Student store",
     * operationId="storeStudent",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact"},
     *       @OA\Property(property="firstname", type="string", example="address"),
     *       @OA\Property(property="lastname", type="string", example="address"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="address"),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(
     *         property="groups", type="array", collectionFormat="multi",
     *         @OA\Items(type="numeric", example=1)
     *      ),
     *       @OA\Property(property="parents", type="array",
     *             @OA\Items( type="object",
     *                 @OA\Property(property="firstname", type="string", example="John"),
     *                 @OA\Property(property="lastname", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="number", example="user@gmail.com"),
     *                 @OA\Property(property="contact", type="string", example="+998 65 445 67 89"),
     *             ),
     *       ),
     *       @OA\Property(
     *         property="exist_parents", type="array", collectionFormat="multi",
     *         @OA\Items(type="numeric", example=1)
     *      ),
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'contact' => 'required|string',
            'status' => 'required|boolean',

            'groups' => 'array',
            'groups.*' => 'numeric|distinct|exists:groups,id',

            'parents' => 'array',
            'parents.*.firstname' => 'required|string',
            'parents.*.lastname' => 'required|string',
            'parents.*.email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'parents.*.contact' => 'required|string',

            'exist_parents' => 'array',
            'exist_parents.*' => 'numeric|distinct|exists:stparents,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        if ($request->has('parents')) {
            $emails = [$request->email];

            foreach ($request->parents as $parent)
                $emails[] = $parent['email'];

            if (count($emails) !== count(array_unique($emails)))
                return $this->sendResponse(
                    success: false,
                    status: 400,
                    // name: 'validation_error',
                    message: trans('msg.val_distinct', ['attribute' => __('msg.attributes.email')])
                );
        }

        $newStudent = Student::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make('12345678'),
            'contact' => $request->contact,
            'status' => $request->status,
            'role_id' => Role::where('name', 'student')->first()->id,
            //! morph
            'created_by' => $this->auth_user->id,
            'created_at' => date('Y-m-d h:i:s')
        ]);

        if ($request->has('groups'))
            $newStudent->groups()->attach($request->groups);

        if ($request->has('parents'))
            foreach ($request->parents as $parent) {
                $newParent = Stparent::create([
                    "firstname" => $parent['firstname'],
                    "lastname" => $parent['lastname'],
                    "email" => $parent['email'],
                    "password" => Hash::make('12345678'),
                    "contact" => $parent['contact'],
                    "status" => true,
                    "role_id" => Role::where('name', 'parent')->first()->id,
                ]);

                $newStudent->stparents()->attach($newParent->id);
            }

        if ($request->has('exist_parents'))
            $newStudent->stparents()->attach($request->exist_parents);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "student_created",
            message: trans('msg.created', ['attribute' => __('msg.attributes.student')]),
            data: ["id" => $newStudent->id],
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/student/{id}",
     * summary="Get specific student data",
     * description="Student show",
     * operationId="showStudent",
     * tags={"Student"},
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
        $student = $this->Student->find($id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: "student_not_found",
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "student_found",
            data: StudentResource::make($student)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/student/{id}",
     * summary="Update specific student",
     * description="Student update",
     * operationId="updateStudent",
     * tags={"Student"},
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
     *       required={"firstname", "lastname", "email", "contact"},
     *       @OA\Property(property="firstname", type="string", example="address"),
     *       @OA\Property(property="lastname", type="string", example="address"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="address"),
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(
     *         property="groups", type="array", collectionFormat="multi",
     *         @OA\Items(type="numeric", example=1)
     *      ),
     *       @OA\Property(property="parents", type="array",
     *             @OA\Items( type="object",
     *                 @OA\Property(property="firstname", type="string", example="John"),
     *                 @OA\Property(property="lastname", type="string", example="Doe"),
     *                 @OA\Property(property="email", type="number", example="user@gmail.com"),
     *                 @OA\Property(property="contact", type="string", example="+998 65 445 67 89"),
     *             ),
     *       ),
     *       @OA\Property(
     *         property="exist_parents", type="array", collectionFormat="multi",
     *         @OA\Items(type="numeric", example=1)
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
        $student = $this->Student->find($id);
        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: "student_not_found",
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email,' . $id,
            'contact' => 'required|string',
            'status' => 'required|boolean',

            'groups' => 'array',
            'groups.*' => 'numeric|distinct|exists:groups,id',

            'parents' => 'array',
            'parents.*.firstname' => 'required|string',
            'parents.*.lastname' => 'required|string',
            'parents.*.email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            'parents.*.contact' => 'required|string',

            'exist_parents' => 'array',
            'exist_parents.*' => 'numeric|distinct|exists:stparents,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        if ($request->has('groups'))
            $student->groups()->sync($request->groups);

        if ($request->has('parents')) {
            $emails = [$request->email];

            foreach ($request->parents as $parent)
                $emails[] = $parent['email'];

            if (count($emails) !== count(array_unique($emails)))
                return $this->sendResponse(
                    success: false,
                    status: 400,
                    // name: 'validation_error',
                    message: trans('msg.val_distinct', ['attribute' => __('msg.attributes.email')])
                );
        }

        $student->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact' => $request->contact,
            'status' => $request->status,
        ]);

        if ($request->has('parents'))
            foreach ($request->parents as $parent) {
                $newParent = Stparent::create([
                    "firstname" => $parent['firstname'],
                    "lastname" => $parent['lastname'],
                    "email" => $parent['email'],
                    "password" => Hash::make('12345678'),
                    "contact" => $parent['contact'],
                    "status" => true,
                    "role_id" => Role::where('name', 'parent')->first()->id,
                ]);

                $student->stparents()->attach($newParent->id);
            }

        if ($request->has('exist_parents'))
            $student->stparents()->sync($request->exist_parents);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "student_updated",
            message: trans('msg.updated', ['attribute' => __('msg.attributes.student')]),
            data: ["id" => $id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/student/{id}",
     * summary="Delete specific student",
     * description="Student delete",
     * operationId="destroyStudent",
     * tags={"Student"},
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

    public function destroy($id)
    {
        $student = $this->Student->find($id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: "student_not_found",
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["id" => $id]
            );

        $student->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "student_deleted",
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.student')]),
            data: ["id" => $id]
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/student/search",
     * summary="Search",
     * description="Search by student firstname or lastname",
     * operationId="studentSearch",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass student credentials",
     *    @OA\JsonContent(
     *       required={"data"},
     *       @OA\Property(property="data", type="string", format="text", example="Luka")
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     */

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "data" => 'required|string'
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $students = $this->Student::where('firstname', 'LIKE', "%$request->data%")
            ->orWhere('lastname', 'LIKE', "%$request->data%")
            ->take(15)
            ->get();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: "student_search",
            data: StudentResourceForSearch::collection($students)
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/student/{student_id}/card",
     * summary="Get Student's all cards data",
     * description="Student get cards",
     * operationId="getCardsStudent",
     * tags={"Student"},
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
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function getCards(string $student_id)
    {
        $student = $this->Student->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'student_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["student_id" => $student_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_student_cards',
            data: CardResource::collection($student->cards)
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/student/{student_id}/card/{card_id}",
     * summary="Get Student's card data",
     * description="Student get card",
     * operationId="SetCardstudent",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *    in="path",
     *    name="card_id",
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

    public function getCard(string $student_id, string $card_id)
    {
        $student = $this->Student->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'student_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["student_id" => $student_id]
            );

        $card = $student->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'card_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.card')]),
                data: ["card_id" => $card_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'get_student_card',
            data: CardResource::make($card)
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/student/{student_id}/card",
     * summary="Set new Student card",
     * description="Student new card store",
     * operationId="StoreCardstudent",
     * tags={"Student"},
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
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"card_number", "card_expiration", "card_token",},
     *       @OA\Property(property="card_number", type="string", example="4567876543456789"),
     *       @OA\Property(property="card_expiration", type="string", example="09/23"),
     *       @OA\Property(property="card_token", type="string", example="ergerhguweghweirfwerrgerhfbwehfbjewhth"),
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

    public function storeCard(Request $request, string $student_id)
    {
        $student = $this->Student->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'student_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["student_id" => $student_id]
            );

        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|unique:cards,card_number',
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newCard = $student->cards()->create([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'student_card_created',
            message: trans('msg.created', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $newCard->id]
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/student/{student_id}/card/{card_id}",
     * summary="Update Student's specific card",
     * description="Student card update",
     * operationId="updateCardStudent",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *    in="path",
     *    name="card_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"card_number", "card_expiration", "card_token",},
     *       @OA\Property(property="card_number", type="string", example="4567876543456789"),
     *       @OA\Property(property="card_expiration", type="string", example="09/23"),
     *       @OA\Property(property="card_token", type="string", example="ergerhguweghweirfwerrgerhfbwehfbjewhth"),
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

    public function updateCard(Request $request, string $student_id, string $card_id)
    {
        $student = $this->Student->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'student_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["student_id" => $student_id]
            );

        $card = $student->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'card_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.card')]),
                data: ["card_id" => $card_id]
            );

        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|max:20|unique:cards,card_number,' . $card_id,
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $card->update([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'student_card_updated',
            message: trans('msg.updated', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $card->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/Student/{Student_id}/card/{card_id}",
     * summary="Delete Student's specific card",
     * description="Student card delete",
     * operationId="destroyCardStudent",
     * tags={"Student"},
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Parameter(
     *    in="path",
     *    name="student_id",
     *    required=true,
     *    description="ID to fetch the targeted campaigns.",
     *    @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *    in="path",
     *    name="card_id",
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

    public function destroyCard(string $student_id, string $card_id)
    {
        $student = $this->Student->find($student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'student_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.student')]),
                data: ["student_id" => $student_id]
            );

        $card = $student->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                // name: 'card_not_found',
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.card')]),
                data: ["card_id" => $card_id]
            );

        $card->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            // name: 'student_card_deleted',
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $card->id]
        );
    }
}

/*
    public function addGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer',
            'group_id' => 'required|integer',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $student = $this->Student->find($request->student_id);

        if (!$student)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'student_not_found',
                data: ["student_id" => $request->student_id]
            );

        $group = Group::whereHas('branch', function ($query) {
            $query->where('id', $this->auth_branch_id);
        })
            ->find($request->group_id);

        if (!$group)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'group_not_found',
                data: ["group_id" => $request->group_id]
            );

        $access = $student->accessForCourses()->find($group->course->id);

        if (!$access)
            return $this->sendResponse(
                success: false,
                status: 404,
                name: 'student_has_no_access',
            );

        $currentTime = new DateTime('1983-10-12 20:31:32');
        $expireTime = new DateTime($access->expire_time);

        if ($currentTime >= $expireTime)
            return $this->sendResponse(
                success: false,
                status: 400,
                name: 'student_access_expired',
            );

        $student->groups()->attach($request->group_id);

        return $this->sendResponse(
            success: true,
            status: 200,
            name: 'student_added_group',
        );
    } 
 */
