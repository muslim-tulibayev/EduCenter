<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/emailverification', [AuthController::class, 'emailverification']);
});

Route::apiResource('/teacher', TeacherController::class);

Route::apiResource('/parent', ParentController::class);

Route::post('/student/search', [StudentController::class, 'search']);
// Route::get('/student/{id}/certificates', [StudentController::class, 'certificates']);
Route::apiResource('/student', StudentController::class);

Route::apiResource('/course', CourseController::class);
Route::get('/course/{course}/lessons', [CourseController::class, 'lessons']);

Route::apiResource('/lesson', LessonController::class);

Route::get('/group/{group}/students', [GroupController::class, 'students']);
Route::post('/group/{group}/students', [GroupController::class, 'changeStudents']);
Route::apiResource('/group', GroupController::class);

Route::get('/branch/{branch}/rooms', [BranchController::class, 'rooms']);
Route::post('/branch/{branch}/rooms', [BranchController::class, 'addRooms']);
Route::post('/branch/schedule', [BranchController::class, 'getSchedule']);
Route::apiResource('/branch', BranchController::class);

Route::apiResource('/session', SessionController::class);

Route::apiResource('/schedule', ScheduleController::class);

// Route::apiResource('/certificate', CertificateController::class);

Route::post('/payment/addcard', [PaymentController::class, 'addCard']);
Route::get('/payment/cashier', [PaymentController::class, 'cashierId']);
Route::post('/payment/pay', [PaymentController::class, 'pay']);

Route::apiResource('/role', RoleController::class);

Route::any('/login', function () {
    return response()->json(["error" => "Unauthenticated"], 401);
})->name('login');


// Route::get('test', function () {
//     $changes = Change::with('changeable')->with('linkedable')->get();
//     return response()->json($changes);
// });