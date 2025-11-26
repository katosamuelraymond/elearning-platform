<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\TeacherDashboardController;
use App\Http\Controllers\Modules\Assignments\TeacherAssignmentsController;
use App\Http\Controllers\Modules\Subjects\TeacherSubjectsController;
use App\Http\Controllers\Modules\Exams\TeacherExamsController;
use App\Http\Controllers\Modules\Questions\TeacherQuestionsController;
use App\Http\Controllers\Modules\Quizzes\TeacherQuizzesController;
use App\Http\Controllers\Modules\Grades\TeacherGradesController;
use App\Http\Controllers\Modules\Resources\TeacherResourcesController;
use App\Http\Controllers\Modules\Settings\TeacherSettingsController;

Route::middleware('auth')->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [TeacherSubjectsController::class, 'index'])->name('index');
        Route::get('/{subject}', [TeacherSubjectsController::class, 'show'])->name('show');
        Route::get('/{subject}/lessons', [TeacherSubjectsController::class, 'lessons'])->name('lessons');
    });

    // Teacher Assignments
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [TeacherAssignmentsController::class, 'index'])->name('index');
        Route::get('/create', [TeacherAssignmentsController::class, 'create'])->name('create');
        Route::post('/', [TeacherAssignmentsController::class, 'store'])->name('store');
        Route::get('/{assignment}', [TeacherAssignmentsController::class, 'show'])->name('show');
        Route::get('/{assignment}/edit', [TeacherAssignmentsController::class, 'edit'])->name('edit');
        Route::put('/{assignment}', [TeacherAssignmentsController::class, 'update'])->name('update');
        Route::delete('/{assignment}', [TeacherAssignmentsController::class, 'destroy'])->name('destroy');
        Route::patch('/{assignment}/toggle-publish', [TeacherAssignmentsController::class, 'togglePublish'])->name('toggle-publish');

        // Submission routes
        Route::get('/{assignment}/submissions', [TeacherAssignmentsController::class, 'submissions'])->name('submissions');
        Route::get('/{assignment}/submissions/{submission}', [TeacherAssignmentsController::class, 'showSubmission'])->name('submissions.show');
        Route::get('/{assignment}/submissions/{submission}/download', [TeacherAssignmentsController::class, 'downloadSubmission'])->name('submissions.download');
        Route::get('/{assignment}/submissions/{submission}/grade', [TeacherAssignmentsController::class, 'gradeSubmission'])->name('submissions.grade');
        Route::put('/{assignment}/submissions/{submission}', [TeacherAssignmentsController::class, 'updateSubmission'])->name('submissions.update');
        Route::get('/{assignment}/download', [TeacherAssignmentsController::class, 'download'])->name('download');
    });

    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [TeacherExamsController::class, 'index'])->name('index');
        Route::get('/question-bank', [TeacherExamsController::class, 'getQuestionBank'])->name('question-bank');

        Route::get('/create', [TeacherExamsController::class, 'create'])->name('create');
        Route::post('/', [TeacherExamsController::class, 'store'])->name('store');
        Route::get('/{exam}', [TeacherExamsController::class, 'show'])->name('show');

        // Manual Grading Routes - ADD THESE
        Route::post('/{exam}/attempts/{attempt}/update-score', [TeacherExamsController::class, 'updateScore'])->name('attempts.update-score');
        Route::post('/{exam}/attempts/{attempt}/bulk-update-grades', [TeacherExamsController::class, 'bulkUpdateGrades'])->name('attempts.bulk-update-grades');

        Route::get('/{exam}/edit', [TeacherExamsController::class, 'edit'])->name('edit');
        Route::put('/{exam}', [TeacherExamsController::class, 'update'])->name('update');
        Route::delete('/{exam}', [TeacherExamsController::class, 'destroy'])->name('destroy');

        // Exam Attempts
        Route::get('/{exam}/attempts', [TeacherExamsController::class, 'attempts'])->name('attempts');
        Route::get('/{exam}/attempts/{attempt}', [TeacherExamsController::class, 'showAttempt'])->name('attempts.show');

        // Print and PDF
        Route::get('/{exam}/print', [TeacherExamsController::class, 'print'])->name('print');
        Route::get('/{exam}/print-pdf', [TeacherExamsController::class, 'printPDF'])->name('print-pdf');

        // Status Management
        Route::patch('/{exam}/toggle-publish', [TeacherExamsController::class, 'togglePublish'])->name('toggle-publish');
        Route::patch('/{exam}/toggle-archive', [TeacherExamsController::class, 'toggleArchive'])->name('toggle-archive');
        Route::post('/{exam}/release-results', [TeacherExamsController::class, 'releaseResults'])
            ->name('release-results');
        Route::post('/{exam}/withdraw-results', [TeacherExamsController::class, 'withdrawResults'])
            ->name('withdraw-results');
    });

    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [TeacherQuestionsController::class, 'index'])->name('index');
        Route::get('/create', [TeacherQuestionsController::class, 'create'])->name('create');
        Route::post('/', [TeacherQuestionsController::class, 'store'])->name('store');
        Route::get('/{question}', [TeacherQuestionsController::class, 'show'])->name('show');
        Route::get('/{question}/edit', [TeacherQuestionsController::class, 'edit'])->name('edit');
        Route::put('/{question}', [TeacherQuestionsController::class, 'update'])->name('update');
        Route::delete('/{question}', [TeacherQuestionsController::class, 'destroy'])->name('destroy');
        Route::post('/{question}/toggle-status', [TeacherQuestionsController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/api/exam-questions', [TeacherQuestionsController::class, 'getQuestionsForExam'])->name('api.exam-questions');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [TeacherQuizzesController::class, 'index'])->name('index');
        Route::get('/create', [TeacherQuizzesController::class, 'create'])->name('create');
    });

    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [TeacherGradesController::class, 'index'])->name('index');
        Route::get('/student/{student}', [TeacherGradesController::class, 'studentReport'])->name('student.report');
    });

    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [TeacherResourcesController::class, 'index'])->name('index');
        Route::get('/upload', [TeacherResourcesController::class, 'create'])->name('upload');
    });

    Route::get('/profile', [TeacherSettingsController::class, 'edit'])->name('profile');
    Route::get('/settings', [TeacherSettingsController::class, 'edit'])->name('settings');
});
