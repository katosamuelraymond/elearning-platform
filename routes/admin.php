<?php

use App\Http\Controllers\Modules\Teachers\AdminTeacherAssignmentController;
use App\Http\Controllers\Modules\Students\StudentAssignmentController;
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
use App\Http\Controllers\Modules\Topics\AdminTopicsController;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

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

    // Student Assignments - NEW SECTION
    Route::prefix('student-assignments')->name('student-assignments.')->group(function () {
        Route::get('/', [StudentAssignmentController::class, 'index'])->name('index');
        Route::get('/create', [StudentAssignmentController::class, 'create'])->name('create');
        Route::post('/', [StudentAssignmentController::class, 'store'])->name('store');
        Route::post('/bulk-assign', [StudentAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::put('/{student}', [StudentAssignmentController::class, 'update'])->name('update');
        Route::delete('/{student}', [StudentAssignmentController::class, 'destroy'])->name('destroy');

        // AJAX Routes for dynamic loading
        Route::get('/unassigned-students', [StudentAssignmentController::class, 'getUnassignedStudents'])->name('unassigned-students');
        Route::get('/class/{class}/students', [StudentAssignmentController::class, 'getStudentsByClass'])->name('students-by-class');
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

    // Exams - COMPLETE CRUD OPERATIONS (Fixed)
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

        // Print routes
        Route::get('/{exam}/print', [AdminExamsController::class, 'print'])->name('print');
        Route::get('/{exam}/print-pdf', [AdminExamsController::class, 'printPDF'])->name('print-pdf');

        // Exam Actions
        Route::patch('/{exam}/toggle-publish', [AdminExamsController::class, 'togglePublish'])->name('toggle-publish');
        Route::patch('/{exam}/toggle-archive', [AdminExamsController::class, 'toggleArchive'])->name('toggle-archive');

        // Exam Attempts Management - FIXED: Remove duplicates and use consistent naming
        Route::get('/{exam}/attempts', [AdminExamsController::class, 'attempts'])->name('attempts');
        Route::get('/{exam}/attempts/{attempt}', [AdminExamsController::class, 'showAttempt'])->name('attempts.show');

        // Grading routes
        Route::post('/{exam}/attempts/{attempt}/update-score', [AdminExamsController::class, 'updateScore'])->name('attempts.update-score');
        Route::post('/{exam}/attempts/{attempt}/bulk-update-grades', [AdminExamsController::class, 'bulkUpdateGrades'])->name('attempts.bulk-update-grades');

        // Results management
        Route::post('/{exam}/release-results', [AdminExamsController::class, 'releaseResults'])->name('release-results');
        Route::post('/{exam}/withdraw-results', [AdminExamsController::class, 'withdrawResults'])->name('withdraw-results');
    });

    // Questions
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

    // Quizzes
    // Quizzes - COMPLETE CRUD OPERATIONS
    // Quizzes
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [AdminQuizzesController::class, 'index'])->name('index');
        Route::get('/question-bank', [AdminQuizzesController::class, 'getQuestionBank'])->name('question-bank');
        Route::get('/create', [AdminQuizzesController::class, 'create'])->name('create');
        Route::post('/', [AdminQuizzesController::class, 'store'])->name('store');
        Route::get('/{quiz}', [AdminQuizzesController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [AdminQuizzesController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [AdminQuizzesController::class, 'update'])->name('update');
        Route::patch('/{quiz}', [AdminQuizzesController::class, 'update']);
        Route::delete('/{quiz}', [AdminQuizzesController::class, 'destroy'])->name('destroy');

        // Quiz Actions
        Route::patch('/{quiz}/toggle-publish', [AdminQuizzesController::class, 'togglePublish'])->name('toggle-publish');

        // Quiz Attempts Management - FIXED ROUTES
        Route::get('/{quiz}/attempts', [AdminQuizzesController::class, 'attempts'])->name('attempts');

        // Use explicit parameter name for attempt
        Route::get('/{quiz}/attempts/{quizAttempt}', [AdminQuizzesController::class, 'showAttempt'])->name('attempts.show');
    });

    // Grades
    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [AdminGradesController::class, 'index'])->name('index');
        Route::get('/student/{student}', [AdminGradesController::class, 'studentReport'])->name('student.report');
    });

    //resources
    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [AdminResourcesController::class, 'index'])->name('index');
        Route::get('/upload', [AdminResourcesController::class, 'create'])->name('upload');
        Route::post('/', [AdminResourcesController::class, 'store'])->name('store');
        Route::get('/{resource}', [AdminResourcesController::class, 'show'])->name('show');
        Route::get('/{resource}/download', [AdminResourcesController::class, 'download'])->name('download');
        Route::patch('/{resource}/toggle-status', [AdminResourcesController::class, 'toggleStatus'])->name('toggle-status');

        // AJAX routes
        Route::get('/topics/by-subject', [AdminResourcesController::class, 'getTopicsBySubject'])->name('topics.by-subject');
    });

    // Topics Management
    Route::prefix('topics')->name('topics.')->group(function () {
        Route::get('/', [AdminTopicsController::class, 'index'])->name('index');
        Route::get('/create', [AdminTopicsController::class, 'create'])->name('create');
        Route::post('/', [AdminTopicsController::class, 'store'])->name('store');
        Route::get('/{topic}', [AdminTopicsController::class, 'show'])->name('show');
        Route::get('/{topic}/edit', [AdminTopicsController::class, 'edit'])->name('edit');
        Route::put('/{topic}', [AdminTopicsController::class, 'update'])->name('update');
        Route::delete('/{topic}', [AdminTopicsController::class, 'destroy'])->name('destroy');
        Route::patch('/{topic}/toggle-status', [AdminTopicsController::class, 'toggleStatus'])->name('toggle-status');

        // AJAX routes
        Route::get('/by-subject', [AdminTopicsController::class, 'getBySubject'])->name('by-subject');
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
