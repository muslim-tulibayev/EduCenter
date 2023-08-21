<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/user'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/emailverification', [UserController::class, 'emailverification']);
});

Route::post('/teacher/login', [TeacherController::class, 'login']);
Route::get('/teacher/logout', [TeacherController::class, 'logout']);
Route::apiResource('/teacher', TeacherController::class);

Route::post('/parent/login', [ParentController::class, 'login']);
Route::get('/parent/logout', [ParentController::class, 'logout']);
Route::apiResource('/parent', ParentController::class);

Route::post('/student/login', [StudentController::class, 'login']);
Route::get('/student/logout', [StudentController::class, 'logout']);
Route::post('/student/search', [StudentController::class, 'search']);
Route::get('/student/{id}/certificates', [StudentController::class, 'certificates']);
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

Route::apiResource('/schedule', ScheduleController::class);

Route::apiResource('/certificate', CertificateController::class);

Route::get('/login', function () {
    return response()->json(["error" => "Unauthenticated"], 401);
})->name('login');


// Route::get('test', function () {
//     $changes = Change::with('changeable')->with('linkedable')->get();
//     return response()->json($changes);
// });