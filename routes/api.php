<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTeacherController;
use App\Http\Controllers\AuthParentController;
use App\Http\Controllers\AuthStudentController;
use App\Http\Controllers\AuthUserController;

use App\Http\Controllers\Manage\AssistantTeacherController;
use App\Http\Controllers\Manage\BranchController;
use App\Http\Controllers\Manage\CourseController;
use App\Http\Controllers\Manage\ExamController;
use App\Http\Controllers\Manage\GroupController;
use App\Http\Controllers\Manage\LessonController;
use App\Http\Controllers\Manage\ParentController;
use App\Http\Controllers\Manage\ScheduleController;
use App\Http\Controllers\Manage\SessionController;
use App\Http\Controllers\Manage\StudentController;
use App\Http\Controllers\Manage\TeacherController;
use App\Http\Controllers\Manage\RoleController;
use App\Http\Controllers\Manage\InactiveUserController;
use App\Http\Controllers\Manage\MarkController;
use App\Http\Controllers\Manage\PaymentController;
use App\Http\Controllers\Manage\RollcallController;
use App\Http\Controllers\Manage\UserController;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/update', [AuthController::class, 'update']);
    Route::get('/branches', [AuthController::class, 'branches']);
});

Route::group(['prefix' => '/user'], function () {
    Route::get('/statistics', [AuthUserController::class, 'statistics']);
    Route::get('/cashier', [AuthUserController::class, 'cashierId']);
    Route::post('/pay-for-course', [AuthUserController::class, 'payForCourse']);
});

Route::group(['prefix' => '/teacher'], function () {
    Route::get('/my-groups', [AuthTeacherController::class, 'groups']);
    Route::get('/my-groups/{id}/lessons', [AuthTeacherController::class, 'lessons']);
    Route::get('/my-groups/{id}/exams', [AuthTeacherController::class, 'exams']);
    Route::get('/my-groups/{id}/students', [AuthTeacherController::class, 'students']);    
    Route::get('/my-groups/{group_id}/lesson/{lesson_id}/get-mark', [AuthTeacherController::class, 'getMarksForLesson']);
    Route::get('/my-groups/{group_id}/exam/{exam_id}/get-mark', [AuthTeacherController::class, 'getMarksForExam']);
    Route::get('/get-mark/{id}/lesson', [AuthTeacherController::class, 'getMarkForLesson']);
    Route::get('/get-mark/{id}/exam', [AuthTeacherController::class, 'getMarkForExam']);
    Route::post('/set-mark', [AuthTeacherController::class, 'setMark']);
});

Route::group(['prefix' => '/parent'], function () {
    Route::get('/my-children', [AuthParentController::class, 'myChildren']);
    //! write: get daily marks
    Route::get('/my-children/{id}/courses', [AuthParentController::class, 'getCourses']);
    Route::get('/my-children/{student_id}/course/{course_id}/lessons', [AuthParentController::class, 'getLessons']);
    Route::get('/my-children/{student_id}/course/{course_id}/exams', [AuthParentController::class, 'getExams']);
    Route::get('/my-children/{student_id}/get-mark/{lesson_id}/lesson', [AuthParentController::class, 'getMarkForLesson']);
    Route::get('/my-children/{student_id}/get-mark/{exam_id}/exam', [AuthParentController::class, 'getMarkForExam']);
    Route::get('/all-courses', [AuthParentController::class, 'allCourses']);
    Route::get('/cashier', [AuthParentController::class, 'cashierId']);
    Route::get('/my-cards', [AuthParentController::class, 'myCards']);
    Route::post('/add-card', [AuthParentController::class, 'addCard']);
    Route::delete('/delete-card/{id}', [AuthParentController::class, 'deleteCard']);
    Route::post('/pay-for-course', [AuthParentController::class, 'payForCourse']);
});

Route::group(['prefix' => '/student'], function () {
    Route::get('/my-courses', [AuthStudentController::class, 'myCourses']);
    Route::get('/my-courses/{id}/lessons', [AuthStudentController::class, 'lessons']);
    Route::get('/my-courses/{id}/exams', [AuthStudentController::class, 'exams']);
    Route::get('/get-mark/{id}/lesson', [AuthStudentController::class, 'getMarkForLesson']);
    Route::get('/get-mark/{id}/exam', [AuthStudentController::class, 'getMarkForExam']);
    Route::get('/all-courses', [AuthStudentController::class, 'allCourses']);
    Route::get('/cashier', [AuthStudentController::class, 'cashierId']);
    Route::get('/my-cards', [AuthStudentController::class, 'myCards']);
    Route::post('/add-card', [AuthStudentController::class, 'addCard']);
    Route::delete('/delete-card/{id}', [AuthStudentController::class, 'deleteCard']);
    Route::post('/pay-for-course', [AuthStudentController::class, 'payForCourse']);
});

Route::group(['prefix' => '/manage'], function () {
    Route::apiResource('/user/inactive', InactiveUserController::class);
    Route::apiResource('/user', UserController::class);

    Route::apiResource('/teacher/assistant', AssistantTeacherController::class);
    Route::apiResource('/teacher', TeacherController::class);

    Route::apiResource('/parent', ParentController::class);
    Route::get('/parent/{parent_id}/students', [ParentController::class, 'getStudents']);
    Route::get('/parent/{parent_id}/card', [ParentController::class, 'getCards']);
    Route::get('/parent/{parent_id}/card/{card_id}', [ParentController::class, 'getCard']);
    Route::post('/parent/{parent_id}/card', [ParentController::class, 'storeCard']);
    Route::put('/parent/{parent_id}/card/{card_id}', [ParentController::class, 'updateCard']);
    Route::delete('/parent/{parent_id}/card/{card_id}', [ParentController::class, 'destroyCard']);

    Route::post('/student/search', [StudentController::class, 'search']);
    Route::apiResource('/student', StudentController::class);
    Route::post('/student/add-group', [StudentController::class, 'addGroup']);
    Route::get('/student/{student_id}/card', [StudentController::class, 'getCards']);
    Route::get('/student/{student_id}/card/{card_id}', [StudentController::class, 'getCard']);
    Route::post('/student/{student_id}/card', [StudentController::class, 'storeCard']);
    Route::put('/student/{student_id}/card/{card_id}', [StudentController::class, 'updateCard']);
    Route::delete('/student/{student_id}/card/{card_id}', [StudentController::class, 'destroyCard']);

    Route::apiResource('/course', CourseController::class);
    Route::get('/course/{id}/lessons', [CourseController::class, 'lessons']);
    Route::get('/course/{id}/exams', [CourseController::class, 'exams']);

    Route::apiResource('/group', GroupController::class);
    Route::get('/group/{group}/students', [GroupController::class, 'getStudents']);


    Route::post('/schedule/by-filter', [ScheduleController::class, 'getSchedulesByFilter']);
    Route::apiResource('/schedule', ScheduleController::class);

    Route::apiResource('/branch', BranchController::class);
    Route::apiResource('/session', SessionController::class);
    Route::apiResource('/exam', ExamController::class)->except(['index']);
    Route::apiResource('/lesson', LessonController::class)->except(['index']);
    Route::apiResource('/role', RoleController::class);
    Route::apiResource('/payment', PaymentController::class)->except(['store', 'update']);

    Route::apiResource('/mark', MarkController::class);
    Route::apiResource('/rollcall', RollcallController::class);

    // Route::apiResource('/cashier', CashierController::class);
    // Route::apiResource('/card', CardController::class);
});

Route::any('/login', function () {
    return response()->json([
        "success" => false,
        "status" => 401,
        "message" => trans('msg.unauthenticated'),
        "data" => null,
        "pagination" => null,
    ]);
})->name('login');


Route::get('test1', function () {
    return 'this is test 67845';
});

// Route::get('test', function () {
//     $changes = Change::with('changeable')->with('linkedable')->get();
//     return response()->json($changes);
// });