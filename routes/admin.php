<?php

use App\Http\Controllers\Modules\Teachers\AdminTeacherAssignmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Modules\Assignments\AdminAssignmentsController;
use App\Http\Controllers\Modules\Subjects\AdminSubjectsController;
use App\Http\Controllers\Modules\Exams\AdminExamsController;
use App\Http\Controllers\Modules\Questions\AdminQuestionsController;
use App\Http\Controllers\Modules\Quizzes\AdminQuizzesController;
use App\Http\Controllers\Modules\Grades\AdminGradesController;
use App\Http\Controllers\Modules\Resources\AdminResourcesController;
use App\Http\Controllers\Modules\Settings\AdminSettingsController;
use App\Http\Controllers\Modules\Users\AdminUsersController;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
// Temporary test route - add this at the top of your admin routes

    // Subjects - COMPLETE CRUD OPERATIONS
    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [AdminSubjectsController::class, 'index'])->name('index');
        Route::get('/create', [AdminSubjectsController::class, 'create'])->name('create');
        Route::post('/', [AdminSubjectsController::class, 'store'])->name('store');
        Route::get('/{subject}/edit', [AdminSubjectsController::class, 'edit'])->name('edit');
        Route::put('/{subject}', [AdminSubjectsController::class, 'update'])->name('update');
        Route::patch('/{subject}', [AdminSubjectsController::class, 'update']); // Alternative update
        Route::delete('/{subject}', [AdminSubjectsController::class, 'destroy'])->name('destroy');
        Route::patch('/{subject}/toggle-status', [AdminSubjectsController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Assignments - COMPLETE CRUD OPERATIONS
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [AdminAssignmentsController::class, 'index'])->name('index');
        Route::get('/create', [AdminAssignmentsController::class, 'create'])->name('create');
        Route::post('/', [AdminAssignmentsController::class, 'store'])->name('store');
        Route::get('/{assignment}', [AdminAssignmentsController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [AdminAssignmentsController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [AdminAssignmentsController::class, 'update'])->name('update');
        Route::patch('/{assignment}', [AdminAssignmentsController::class, 'update']); // Alternative update
        Route::delete('/{assignment}', [AdminAssignmentsController::class, 'destroy'])->name('destroy');
        Route::patch('/{assignment}/toggle-publish', [AdminAssignmentsController::class, 'togglePublish'])->name('toggle-publish');

        // Submission routes
        Route::get('/{assignment}/submissions', [AdminAssignmentsController::class, 'submissions'])->name('submissions');
        Route::get('/{assignment}/submissions/{submission}', [AdminAssignmentsController::class, 'showSubmission'])->name('submissions.show');
        Route::get('/{assignment}/submissions/{submission}/download', [AdminAssignmentsController::class, 'downloadSubmission'])->name('submissions.download');
        Route::get('/{assignment}/submissions/{submission}/edit', [AdminAssignmentsController::class, 'editSubmission'])->name('submissions.edit');
        Route::put('/{assignment}/submissions/{submission}', [AdminAssignmentsController::class, 'updateSubmission'])->name('submissions.update');
        Route::delete('/{assignment}/submissions/{submission}', [AdminAssignmentsController::class, 'destroySubmission'])->name('submissions.destroy');
        Route::post('/{assignment}/submissions/bulk-action', [AdminAssignmentsController::class, 'bulkSubmissionAction'])->name('submissions.bulk-action');
        Route::get('/{assignment}/download', [AdminAssignmentsController::class, 'download'])->name('download');
    });

    // Exams - COMPLETE CRUD OPERATIONS (Updated)

    Route::prefix('exams')->name('exams.')->group(function () {
        // CRUD Routes
        Route::get('/', [AdminExamsController::class, 'index'])->name('index');
        Route::get('/question-bank', [AdminExamsController::class, 'getQuestionBank'])->name('question-bank');

        Route::get('/create', [AdminExamsController::class, 'create'])->name('create');
        Route::post('/', [AdminExamsController::class, 'store'])->name('store');
        Route::get('/{exam}', [AdminExamsController::class, 'show'])->name('show');
        Route::get('/{exam}/edit', [AdminExamsController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [AdminExamsController::class, 'update'])->name('update');
        Route::patch('/{exam}', [AdminExamsController::class, 'update']);
        Route::delete('/{exam}', [AdminExamsController::class, 'destroy'])->name('destroy');

        Route::get('admin/exams/{exam}/print', [AdminExamsController::class, 'print'])->name('print');
        Route::get('admin/exams/{exam}/print-pdf', [AdminExamsController::class, 'printPDF'])->name('print-pdf');


        // Exam Actions
        Route::patch('/{exam}/toggle-publish', [AdminExamsController::class, 'togglePublish'])->name('toggle-publish');
        Route::patch('admin/exams/{exam}/toggle-archive', [AdminExamsController::class, 'toggleArchive'])->name('toggle-archive');

        // Exam Attempts Management
        Route::get('/{exam}/attempts', [AdminExamsController::class, 'attempts'])->name('attempts.index');
        Route::get('/{exam}/attempts/{attempt}', [AdminExamsController::class, 'showAttempt'])->name('attempts.show');

           });

    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [AdminQuestionsController::class, 'index'])->name('index');
        Route::get('/create', [AdminQuestionsController::class, 'create'])->name('create');
        Route::post('/', [AdminQuestionsController::class, 'store'])->name('store');
        Route::get('/{question}', [AdminQuestionsController::class, 'show'])->name('show');
        Route::get('/{question}/edit', [AdminQuestionsController::class, 'edit'])->name('edit');
        Route::put('/{question}', [AdminQuestionsController::class, 'update'])->name('update');
        Route::delete('/{question}', [AdminQuestionsController::class, 'destroy'])->name('destroy');
        Route::patch('/{question}/toggle-status', [AdminQuestionsController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Teacher Assignments - COMPLETE CRUD OPERATIONS
    Route::prefix('teacher-assignments')->name('teacher-assignments.')->group(function () {
        Route::get('/', [AdminTeacherAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [AdminTeacherAssignmentController::class, 'create'])->name('create');
        Route::post('/', [AdminTeacherAssignmentController::class, 'store'])->name('store');
        Route::get('/{assignment}/edit', [AdminTeacherAssignmentController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [AdminTeacherAssignmentController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [AdminTeacherAssignmentController::class, 'destroy'])->name('destroy');
        Route::post('/{assignment}/toggle-status', [AdminTeacherAssignmentController::class, 'toggleStatus'])->name('toggle-status');

        // AJAX Routes for dynamic loading
        Route::get('/{teacher}/assignments', [AdminTeacherAssignmentController::class, 'getTeacherAssignments'])->name('get-teacher');
        Route::get('/{teacher}/available-subjects', [AdminTeacherAssignmentController::class, 'getAvailableSubjects'])->name('available-subjects');
        Route::get('/{teacher}/available-classes', [AdminTeacherAssignmentController::class, 'getAvailableClasses'])->name('available-classes');

        // Add this route for class streams
        Route::get('/classes/{class}/streams', [AdminTeacherAssignmentController::class, 'getClassStreams'])->name('class-streams');
    });

    // Quizzes
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [AdminQuizzesController::class, 'index'])->name('index');
        Route::get('/create', [AdminQuizzesController::class, 'create'])->name('create');
    });

    // Grades
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [AdminGradesController::class, 'index'])->name('index');
        Route::get('/student/{student}', [AdminGradesController::class, 'studentReport'])->name('student.report');
    });

    // Resources
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [AdminResourcesController::class, 'index'])->name('index');
        Route::get('/upload', [AdminResourcesController::class, 'create'])->name('upload');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUsersController::class, 'index'])->name('index');
        Route::get('/create', [AdminUsersController::class, 'create'])->name('create');
        Route::post('/store', [AdminUsersController::class, 'store'])->name('store');
        Route::get('/edit/{user}', [AdminUsersController::class, 'edit'])->name('edit');
        Route::put('/update/{user}', [AdminUsersController::class, 'update'])->name('update');
        Route::delete('/destroy/{user}', [AdminUsersController::class, 'destroy'])->name('destroy');
    });

    // Settings
    Route::get('/profile', [AdminSettingsController::class, 'profile'])->name('profile');
    Route::get('/settings', [AdminSettingsController::class, 'settings'])->name('settings');
});
