<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\PaymentMethods;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Stparent;
use App\Models\Student;
use App\Traits\SendResponseTrait;
use App\Traits\SendValidatorMessagesTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthUserController extends Controller
{
    use SendResponseTrait, SendValidatorMessagesTrait;

    private $payment;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->payment = new PaymentMethods();
        parent::__construct(checkBranch: true);
    }

    /**
     * @OA\Get(
     * path="/api/user/statistics",
     * summary="statistics",
     * description="statistics /api/user/statistics/?filter=day|week|month|year",
     * operationId="authUserStatistics",
     * tags={"AuthUser"},
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

    public function statistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "filter" => 'string|in:day,week,month,year'
        ]);

        if ($validator->fails())
            return $this->sendValidatorMessages($validator);

        $statistics = [];

        $branch = Branch::find($this->auth_branch_id);

        if ($this->auth_role->roles >= 1)
            $statistics["roles"] = Role::count();

        // if ($this->auth_role->branches >= 1)
        //     $statistics["branches"] = Branch::count();

        if ($this->auth_role->users >= 1)
            $statistics["users"] = $branch->users()->where('status', true)->count();

        if ($this->auth_role->inactive_users >= 1)
            $statistics["inactive_users"] = $branch->users()->where('status', false)->count();

        if ($this->auth_role->teachers >= 1)
            $statistics["teachers"] = $branch->teachers()->where('is_assistant', false)->count();

        if ($this->auth_role->assistant_teachers >= 1)
            $statistics["assistant_teachers"] = $branch->teachers()->where('is_assistant', true)->count();

        if ($this->auth_role->courses >= 1)
            $statistics["courses"] = $branch->courses()->count();

        if ($this->auth_role->groups >= 1)
            $statistics["groups"] = $branch->groups()->count();

        if ($this->auth_role->students >= 1)
            $statistics["students"] = Student::whereHas('groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            })->count();

        if ($this->auth_role->stparents >= 1)
            $statistics["stparents"] = Stparent::whereHas('students.groups.branch', function ($query) {
                $query->where('id', $this->auth_branch_id);
            })->count();

        if ($this->auth_role->sessions >= 1)
            $statistics["sessions"] = $branch->sessions()->count();

        if ($this->auth_role->rooms >= 1)
            $statistics["rooms"] = $branch->rooms()->count();

        switch ($request->filter) {
            case 'day':
                $oneMonthAgo = new DateTime('-1 day');
                break;
            case 'week':
                $oneMonthAgo = new DateTime('-1 week');
                break;
            case 'month':
                $oneMonthAgo = new DateTime('-1 month');
                break;
            case 'year':
                $oneMonthAgo = new DateTime('-1 year');
                break;
            default:
                $oneMonthAgo = new DateTime('-1 month');
                break;
        }

        if ($this->auth_role->payments >= 1)
            $statistics["payments"] = $branch->payments()
                ->where('created_at', '>=', $oneMonthAgo)
                ->sum('amount');

        return $this->sendResponse(
            success: true,
            status: 200,
            data: [
                "branch" => Branch::find($this->auth_branch_id),
                "statistics" => $statistics,
            ]
        );
    }

    /**
     * @OA\Get(
     * path="/api/user/cashier",
     * summary="Get Cashier",
     * description="GetCashier",
     * operationId="getCashierAuthUser",
     * tags={"AuthUser"},
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
     * path="/api/user/pay-for-course",
     * summary="Pay for course",
     * description="Pay for course",
     * operationId="payForCourseAuthUser",
     * tags={"AuthUser"},
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
        return $this->payment->payForAdmin($request);
    }
}
