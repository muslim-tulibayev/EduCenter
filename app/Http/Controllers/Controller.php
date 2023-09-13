<?php

namespace App\Http\Controllers;

use App\Traits\SendResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Api structure",
 *    version="1.0.0",
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 * 
 *  @OA\Tag(
 *      name = "Auth",
 *      description = "Auth",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "AuthUser",
 *      description = "AuthUser",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "AuthTeacher",
 *      description = "AuthTeacher",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "AuthParent",
 *      description = "AuthParent",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "AuthStudent",
 *      description = "AuthStudent",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "User",
 *      description = "User",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "InactiveUser",
 *      description = "InactiveUser",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Branch",
 *      description = "Branch",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Course",
 *      description = "Course",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Group",
 *      description = "Group",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Lesson",
 *      description = "Lesson",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Parent",
 *      description = "Parent",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Schedule",
 *      description = "Schedule",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Student",
 *      description = "Student",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Teacher",
 *      description = "Teacher",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "AssistantTeacher",
 *      description = "AssistantTeacher",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Session",
 *      description = "Session",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Payment",
 *      description = "Payment",
 *  ),
 * 
 *  @OA\Tag(
 *      name = "Role",
 *      description = "Role",
 *  ),
 * 
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    use SendResponseTrait;

    protected $auth_type;
    protected $auth_user;
    protected $auth_role;
    protected $auth_branch_id;

    public function __construct(string $column = null, bool $checkBranch = false)
    {
        // define user and its type
        if (auth('api')->user()) {
            $this->auth_type = 'api';
            $this->auth_user = auth('api')->user();
        } elseif (auth('teacher')->user()) {
            $this->auth_type = 'teacher';
            $this->auth_user = auth('teacher')->user();
        } elseif (auth('parent')->user()) {
            $this->auth_type = 'parent';
            $this->auth_user = auth('parent')->user();
        } elseif (auth('student')->user()) {
            $this->auth_type = 'student';
            $this->auth_user = auth('student')->user();
        }

        // define user's role
        if ($this->auth_user)
            $this->auth_role = $this->auth_user->role;

        // define user's branch
        if ($checkBranch)
            $this->middleware(function ($request, $next) {
                $branch_header = json_decode(base64_decode($request->header('Branch-Id')));

                // if there is no header, check user has any branch
                if (!$branch_header) {
                    if ($branch = $this->auth_user->branches()->first() ?? null)
                        $this->auth_branch_id = $branch->id;
                    else
                        return $this->sendResponse(
                            success: false,
                            status: 404,
                            name: 'user_has_no_branch',
                        );
                } else {
                    $this->auth_branch_id = $branch_header->id;
                }

                if (!$this->auth_user->branches()->find($this->auth_branch_id))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            });

        // set gates for CRUD methods
        if ($this->auth_role && $column) {
            $this->middleware(function ($request, $next) use ($column) {
                if (!($this->auth_role[$column] >= 1))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            })->only('index');

            $this->middleware(function ($request, $next) use ($column) {
                if (!($this->auth_role[$column] >= 1))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            })->only('show');

            $this->middleware(function ($request, $next) use ($column) {
                if (!($this->auth_role[$column] >= 2))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            })->only('update');

            $this->middleware(function ($request, $next) use ($column) {
                if (!($this->auth_role[$column] >= 3))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            })->only('store');

            $this->middleware(function ($request, $next) use ($column) {
                if (!($this->auth_role[$column] >= 4))
                    return $this->sendResponse(
                        success: false,
                        status: 403,
                        name: 'unauthorized',
                    );

                return $next($request);
            })->only('destroy');
        }
    }
}
