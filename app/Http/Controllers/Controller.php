<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Api sructure",
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
 *      description = "User",
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

    protected $auth_type;
    protected $auth_user;
    protected $auth_role;
    private $column;

    public function __construct($column = null)
    {
        $this->column = $column;

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

        if ($this->auth_user)
            $this->auth_role = $this->auth_user->role;

        if ($this->auth_role && $this->column) {
            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 1))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);

                return $next($request);
            })->only('index');

            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 1))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);
                return $next($request);
            })->only('index');

            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 1))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);
                return $next($request);
            })->only('show');

            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 2))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);
                return $next($request);
            })->only('update');

            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 3))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);
                return $next($request);
            })->only('store');

            $this->middleware(function ($request, $next) {
                if (!($this->auth_role[$this->column] >= 4))
                    return response()->json([
                        "error" => "Unauthorized"
                    ], 403);
                return $next($request);
            })->only('destroy');
        }
    }
}
