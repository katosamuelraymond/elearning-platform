<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Public routes
Route::get('/', function () {
    return view('welcome', [
        'showNavbar' => true,
        'showSidebar' => auth()->check(), // Show sidebar only if logged in
        'showFooter' => true
    ]);
})->name('home');

// Auth routes (for guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

});

    Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Include role-specific route files
Route::middleware('auth')->group(function () {
    require __DIR__.'/admin.php';
    require __DIR__.'/teacher.php';
    require __DIR__.'/student.php';
});

// Add this temporary route in routes/web.php
Route::get('/php-info', function() {
    return [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
    ];
});

// Add to routes/web.php
Route::get('/check-temp', function() {
    return [
        'upload_tmp_dir' => ini_get('upload_tmp_dir'),
        'sys_temp_dir' => sys_get_temp_dir(),
        'temp_dir_writable' => is_writable(sys_get_temp_dir()),
        'upload_tmp_dir_writable' => ini_get('upload_tmp_dir') ? is_writable(ini_get('upload_tmp_dir')) : 'Not set',
        'disk_free_space' => disk_free_space(sys_get_temp_dir()),
    ];


});


Route::get('/debug-mcq-issue', function() {
    $attempt = \App\Models\Assessment\ExamAttempt::find(1);
    $question = \App\Models\Assessment\Question::with('options')->find(1);

    $userAnswer = $attempt->answers[1] ?? null;

    // Get all options sorted by order
    $options = $question->options->sortBy('order');

    // Find correct option
    $correctOption = $options->where('is_correct', true)->first();

    // Find the index of the correct option
    $correctIndex = null;
    foreach ($options->values() as $index => $option) {
        if ($option->id === $correctOption->id) {
            $correctIndex = $index;
            break;
        }
    }

    return [
        'question_id' => 1,
        'student_answer' => $userAnswer,
        'student_answer_type' => gettype($userAnswer),
        'correct_option' => [
            'id' => $correctOption->id,
            'text' => $correctOption->option_text,
            'order' => $correctOption->order,
            'calculated_index' => $correctIndex
        ],
        'all_options' => $options->map(function($opt, $index) {
            return [
                'index' => $index,
                'id' => $opt->id,
                'text' => $opt->option_text,
                'order' => $opt->order,
                'is_correct' => $opt->is_correct
            ];
        })->values()->toArray(),
        'comparison_results' => [
            'strict_equality' => $userAnswer == $correctIndex,
            'strict_equality_with_types' => $userAnswer === $correctIndex,
            'int_comparison' => (int)$userAnswer === (int)$correctIndex,
            'string_comparison' => (string)$userAnswer === (string)$correctIndex
        ],
        'should_be_correct' => (int)$userAnswer === (int)$correctIndex
    ];
});

// API route for getting topic details
Route::get('/api/topics/{topic}', function(\App\Models\Teaching\Topic $topic) {
    return response()->json([
        'success' => true,
        'topic' => [
            'id' => $topic->id,
            'title' => $topic->title,
            'subject_id' => $topic->subject_id,
        ]
    ]);
});
