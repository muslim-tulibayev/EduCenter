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
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
