<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\StudentDashboardController;
use App\Http\Controllers\Modules\Assignments\StudentAssignmentsController;
use App\Http\Controllers\Modules\Subjects\StudentSubjectsController;
use App\Http\Controllers\Modules\Exams\StudentExamsController;
use App\Http\Controllers\Modules\Quizzes\StudentQuizzesController;
use App\Http\Controllers\Modules\Grades\StudentGradesController;
use App\Http\Controllers\Modules\Resources\StudentResourcesController;
use App\Http\Controllers\Modules\Settings\StudentSettingsController;

Route::middleware('auth')->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('subjects')->name('subjects.')->group(function () {
        Route::get('/', [StudentSubjectsController::class, 'index'])->name('index');
    });

    // Student Assignments
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [StudentAssignmentsController::class, 'index'])->name('index');

        // MOVE THIS LINE UP
        Route::get('/my-submissions', [StudentAssignmentsController::class, 'mySubmissions'])->name('my-submissions');

        // ALL WILDCARD ROUTES GO AFTER SPECIFIC ONES
        Route::get('/{assignment}', [StudentAssignmentsController::class, 'show'])->name('show');
        Route::post('/{assignment}/submit', [StudentAssignmentsController::class, 'submit'])->name('submit');
        Route::get('/{assignment}/download', [StudentAssignmentsController::class, 'downloadAssignment'])->name('download');
        Route::get('/{assignment}/submissions/{submission}/download', [StudentAssignmentsController::class, 'downloadSubmission'])->name('submissions.download');
    });
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [StudentExamsController::class, 'index'])->name('index');
        Route::get('/take/{exam}', [StudentExamsController::class, 'show'])->name('take');
    });

    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [StudentQuizzesController::class, 'index'])->name('index');
        Route::get('/take/{quiz}', [StudentQuizzesController::class, 'show'])->name('take');
    });

    Route::prefix('grades')->name('grades.')->group(function () {
        Route::get('/', [StudentGradesController::class, 'index'])->name('index');
        Route::get('/course/{course}', [StudentGradesController::class, 'courseReport'])->name('course.report');
    });

    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('/', [StudentResourcesController::class, 'index'])->name('index');
    });

    Route::get('/profile', [StudentSettingsController::class, 'edit'])->name('profile');
    Route::get('/settings', [StudentSettingsController::class, 'edit'])->name('settings');
});
