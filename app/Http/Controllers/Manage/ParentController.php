<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Card\CardResource;
use App\Http\Resources\Parent\ParentResource;
use App\Http\Resources\Student\StudentResource;
use App\Models\Stparent;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    use SendValidatorMessagesTrait, SendResponseTrait;

    private $Stparent;

    public function __construct()
    {
        $this->middleware('auth:api,teacher');

        parent::__construct('stparents', true);

        $this->middleware(function ($request, $next) {
            $this->Stparent = Stparent::whereHas('students.groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            });

            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 1))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('getCards');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 1))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('getCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 2))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('updateCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 3))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('storeCard');

        $this->middleware(function ($request, $next) {
            if (!($this->auth_role['cards'] >= 4))
                return $this->sendResponse(
                    success: false,
                    status: 403,
                    message: trans('msg.unauthorized'),
                );

            return $next($request);
        })->only('destroyCard');
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent",
     * summary="Get all parents data",
     * description="Parent index",
     * operationId="indexParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *    response=403,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Unauthorized")
     *        )
     *     )
     * )
     */

    public function index()
    {
        $parents = $this->Stparent->orderByDesc('id')->paginate();

        return $this->sendResponse(
            success: true,
            status: 200,
            data: ParentResource::collection($parents),
            pagination: $parents
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/parent",
     * summary="Set new Parent",
     * description="Parent store",
     * operationId="storeParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"firstname", "lastname", "email", "contact", "role_id", "students"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="+998 56 789 09 87"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email'
                . '|unique:students,email',
            "contact" => 'required|string',
            "role_id" => 'required|exists:roles,id',
            'students' => 'required|array',
            'students.*' => 'numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newParent = Stparent::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact' => $request->contact,
            'role_id' => $request->role_id,
        ]);

        $newParent->students()->attach($request->students);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.created', ['attribute' => __('msg.attributes.parent')]),
            data: ["id" => $newParent->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent/{id}",
     * summary="Get specific parent data",
     * description="Parent show",
     * operationId="showParent",
     * tags={"Parent"},
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
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["id" => $id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: ParentResource::make($parent)
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/parent/{id}",
     * summary="Update specific parent",
     * description="Parent update",
     * operationId="updateParent",
     * tags={"Parent"},
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
     *       required={"firstname", "lastname", "email", "contact", "role_id", "students"},
     *       @OA\Property(property="firstname", type="string", example="John"),
     *       @OA\Property(property="lastname", type="string", example="Doe"),
     *       @OA\Property(property="email", type="string", example="user@gmail.com"),
     *       @OA\Property(property="contact", type="string", example="+998 56 789 09 87"),
     *       @OA\Property(property="role_id", type="numeric", example=1),
     *       @OA\Property(
     *         property="students", type="array", collectionFormat="multi",
     *         @OA\Items(type="integer", example=1)
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
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["id" => $id]
            );

        $validator = Validator::make($request->all(), [
            "firstname" => 'required|string',
            "lastname" => 'required|string',
            'email' => 'required|email'
                . '|unique:users,email'
                . '|unique:teachers,email'
                . '|unique:stparents,email,' . $id
                . '|unique:students,email',
            "contact" => 'required|string',
            "role_id" => 'required|exists:roles,id',

            'students' => 'array',
            'students.*' => 'numeric|distinct|exists:students,id',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $parent->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'contact' => $request->contact,
            'role_id' => $request->role_id,
        ]);

        if ($request->has('students'))
            $parent->students()->sync($request->students);

        // if (auth('parent')->user() !== null)
        //     auth('parent')->user()->makeChanges(
        //         'Parent updated from $val1 to $val2',
        //         '$col-name',
        //         $parent
        //     );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.updated', ['attribute' => __('msg.attributes.parent')]),
            data: ["id" => $parent->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/parent/{id}",
     * summary="Delete specific parent",
     * description="Parent delete",
     * operationId="destroyParent",
     * tags={"Parent"},
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
        $parent = $this->Stparent->find($id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["id" => $id]
            );

        $parent->delete();

        // auth('api')->user()->makeChanges(
        //     'Parent deleted',
        //     'deleted',
        //     $parent
        // );

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.parent')]),
            data: ["id" => $parent->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent/{parent_id}/card",
     * summary="Get parent's all cards data",
     * description="Parent get cards",
     * operationId="getCardsParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function getCards(string $parent_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["parent_id" => $parent_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: CardResource::collection($parent->cards)
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent/{parent_id}/card/{card_id}",
     * summary="Get parent's card data",
     * description="Parent get card",
     * operationId="getCardParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function getCard(string $parent_id, string $card_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["parent_id" => $parent_id]
            );

        $card = $parent->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.card')]),
                data: ["card_id" => $card_id]
            );

        return $this->sendResponse(
            success: true,
            status: 200,
            data: CardResource::make($card)
        );
    }

    /**
     * @OA\Post(
     * path="/api/manage/parent/{parent_id}/card",
     * summary="Set new Parent card",
     * description="Parent new card store",
     * operationId="storeCardParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function storeCard(Request $request, string $parent_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["parent_id" => $parent_id]
            );

        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string|min:16|unique:cards,card_number',
            'card_expiration' => 'required|string',
            'card_token' => 'required|string',
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $newCard = $parent->cards()->create([
            'card_number' => $request->card_number,
            'card_expiration' => $request->card_expiration,
            'card_token' => $request->card_token,
        ]);

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.created', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $newCard->id]
        );
    }

    /**
     * @OA\Put(
     * path="/api/manage/parent/{parent_id}/card/{card_id}",
     * summary="Update parent's specific card",
     * description="Parent card update",
     * operationId="updateCardParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function updateCard(Request $request, string $parent_id, string $card_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["parent_id" => $parent_id]
            );

        $card = $parent->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
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
            message: trans('msg.updated', ['attribute' => __('msg.attributes.parent')]),
            data: ["id" => $card->id]
        );
    }

    /**
     * @OA\Delete(
     * path="/api/manage/parent/{parent_id}/card/{card_id}",
     * summary="Delete parent's specific card",
     * description="Parent card delete",
     * operationId="destroyCardParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     *
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function destroyCard(string $parent_id, string $card_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["parent_id" => $parent_id]
            );

        $card = $parent->cards()->find($card_id);

        if (!$card)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.card')]),
                data: ["card_id" => $card_id]
            );

        $card->delete();

        return $this->sendResponse(
            success: true,
            status: 200,
            message: trans('msg.deleted', ['attribute' => __('msg.attributes.card')]),
            data: ["id" => $card->id]
        );
    }

    /**
     * @OA\Get(
     * path="/api/manage/parent/{parent_id}/students",
     * summary="Get students of a parent",
     * description="Get students of a parent",
     * operationId="getStudentsParent",
     * tags={"Parent"},
     * security={ {"bearerAuth": {} }},
     * 
     * @OA\Parameter(
     *    in="path",
     *    name="parent_id",
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

    public function getStudents(string $parent_id)
    {
        $parent = $this->Stparent->find($parent_id);

        if (!$parent)
            return $this->sendResponse(
                success: false,
                status: 404,
                message: trans('msg.not_found', ['attribute' => __('msg.attributes.parent')]),
                data: ["id" => $parent_id]
            );

        $students = $parent->students;

        return $this->sendResponse(
            success: true,
            status: 200,
            data: StudentResource::collection($students)
        );
    }
}
