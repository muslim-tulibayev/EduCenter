<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeacherController as AuthTeacherController;
use App\Http\Controllers\ParentController as AuthParentController;
use App\Http\Controllers\StudentController as AuthStudentController;

// use App\Http\Controllers\Manage\PaymentController;
use App\Http\Controllers\Manage\BranchController;
use App\Http\Controllers\Manage\CourseController;
use App\Http\Controllers\Manage\GroupController;
use App\Http\Controllers\Manage\LessonController;
use App\Http\Controllers\Manage\ParentController;
use App\Http\Controllers\Manage\ScheduleController;
use App\Http\Controllers\Manage\SessionController;
use App\Http\Controllers\Manage\StudentController;
use App\Http\Controllers\Manage\TeacherController;
use App\Http\Controllers\Manage\RoleController;
use App\Http\Controllers\Manage\UnactiveUserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/update', [AuthController::class, 'update']);
});

Route::group(['prefix' => '/teacher'], function () {
    Route::post('/something', [AuthTeacherController::class, 'something']);
});

Route::group(['prefix' => '/parent'], function () {
    Route::post('/something', [AuthParentController::class, 'something']);
});

Route::group(['prefix' => '/student'], function () {
    Route::post('/something', [AuthStudentController::class, 'something']);
});

Route::group(['prefix' => '/manage'], function () {
    Route::apiResource('/user/unactive', UnactiveUserController::class);
    Route::apiResource('/teacher', TeacherController::class);
    Route::apiResource('/parent', ParentController::class);
    Route::apiResource('/student', StudentController::class);
    Route::post('/student/search', [StudentController::class, 'search']);
    Route::apiResource('/course', CourseController::class);
    Route::apiResource('/branch', BranchController::class);
    Route::apiResource('/lesson', LessonController::class);
    Route::apiResource('/group', GroupController::class);
    Route::apiResource('/session', SessionController::class);
    Route::apiResource('/schedule', ScheduleController::class);
    Route::apiResource('/role', RoleController::class);
});



// Route::get('/student/{id}/certificates', [StudentController::class, 'certificates']);
// Route::get('/course/{course}/lessons', [CourseController::class, 'lessons']);
// Route::get('/group/{group}/students', [GroupController::class, 'students']);
// Route::post('/group/{group}/students', [GroupController::class, 'changeStudents']);

// Route::post('/payment/addcard', [PaymentController::class, 'addCard']);
// Route::get('/payment/cashier', [PaymentController::class, 'cashierId']);
// Route::post('/payment/pay', [PaymentController::class, 'pay']);


Route::any('/login', function () {
    return response()->json(["error" => "Unauthenticated"], 401);
})->name('login');


// Route::get('test', function () {
//     $changes = Change::with('changeable')->with('linkedable')->get();
//     return response()->json($changes);
// });