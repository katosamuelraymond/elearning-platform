<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} - Lincoln eLearning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .break-inside-avoid {
                break-inside: avoid;
            }
            body {
                font-size: 12pt;
                line-height: 1.4;
                margin: 0;
                padding: 0;
            }
            .print-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0.5in !important;
            }
        }

        @media screen {
            .print-container {
                max-width: 8.5in;
                margin: 2rem auto;
                background: white;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                padding: 1in;
            }
        }

        .school-header {
            border-bottom: 3px solid #1e40af;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .student-info {
            border: 2px dashed #d1d5db;
            background: #f9fafb;
        }

        .instructions-box {
            border-left: 4px solid #f59e0b;
            background: #fffbeb;
        }

        .general-instructions {
            border-left: 4px solid #3b82f6;
            background: #eff6ff;
        }

        .question-item {
            border: 1px solid #e5e7eb;
            background: white;
        }

        .correct-answer {
            background: #dcfce7;
            border-left: 4px solid #16a34a;
        }

        .answer-lines {
            background: repeating-linear-gradient(
                transparent,
                transparent 1.4em,
                #e5e7eb 1.4em,
                #e5e7eb 1.5em
            );
            line-height: 1.5em;
            padding: 0.5rem;
        }

        .answer-key-section {
            border-top: 3px solid #7c3aed;
            background: #faf5ff;
        }

        .option-letter {
            display: inline-block;
            width: 1.5em;
            height: 1.5em;
            border: 1px solid #4b5563;
            border-radius: 50%;
            text-align: center;
            line-height: 1.4em;
            margin-right: 0.5em;
            font-weight: bold;
        }

        .true-false-option {
            display: inline-flex;
            align-items: center;
            margin-right: 2em;
        }

        .fill-blank-line {
            display: inline-block;
            min-width: 3em;
            border-bottom: 1px solid #4b5563;
            margin: 0 0.25em;
            vertical-align: baseline;
        }
    </style>
</head>
<body class="bg-gray-100">
<!-- Print Controls -->
<div class="no-print fixed top-4 right-4 bg-white p-4 rounded-lg shadow-lg z-50 border">
    <div class="flex flex-col space-y-3">
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center flex-1 justify-center">
                <i class="fas fa-print mr-2"></i>
                Print Now
            </button>
            <button onclick="window.close()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="flex space-x-2">
            @if(!$includeAnswers && !$studentVersion)
                <a href="?answers=1" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg font-medium flex items-center flex-1 justify-center text-sm">
                    <i class="fas fa-key mr-1"></i>
                    With Answers
                </a>
            @endif

            @if($includeAnswers && !$studentVersion)
                <a href="?" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg font-medium flex items-center flex-1 justify-center text-sm">
                    <i class="fas fa-file-alt mr-1"></i>
                    Without Answers
                </a>
            @endif

            @if(!$studentVersion)
                <a href="?student=1" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg font-medium flex items-center flex-1 justify-center text-sm">
                    <i class="fas fa-user-graduate mr-1"></i>
                    Student Version
                </a>
            @endif
        </div>

        @if($studentVersion)
            <a href="?" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <i class="fas fa-chalkboard-teacher mr-1"></i>
                Teacher View
            </a>
        @endif

        <div class="text-xs text-gray-600 text-center pt-2 border-t">
            <i class="fas fa-lightbulb mr-1"></i>
            Tip: Use "Save as PDF" in print dialog
        </div>
    </div>
</div>

<!-- Exam Content -->
<div class="print-container bg-white">
    <!-- School Header -->
    <div class="school-header text-center">
        <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">LINCOLN E-LEARNING ACADEMY</h1>
            <p class="text-lg text-gray-600 uppercase tracking-wide">Quality Education for Future Leaders</p>
        </div>
        <div class="mt-4 pt-4 border-t">
            <h2 class="text-2xl font-bold text-blue-800 uppercase">{{ $exam->title }}</h2>
            <p class="text-gray-600 font-medium">{{ ucfirst($exam->type) }} Examination</p>
            <p class="text-sm text-gray-500 mt-1">Academic Year {{ date('Y') }}/{{ date('Y') + 1 }}</p>
        </div>
    </div>

    <!-- Exam Information -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border">
        <div class="text-center">
            <div class="font-semibold text-gray-700">Class</div>
            <div class="text-lg font-bold text-blue-800">{{ $exam->class->name }}</div>
        </div>
        <div class="text-center">
            <div class="font-semibold text-gray-700">Subject</div>
            <div class="text-lg font-bold text-green-800">{{ $exam->subject->name }}</div>
        </div>
        <div class="text-center">
            <div class="font-semibold text-gray-700">Duration</div>
            <div class="text-lg font-bold text-orange-800">{{ $exam->duration }} minutes</div>
        </div>
        <div class="text-center">
            <div class="font-semibold text-gray-700">Total Marks</div>
            <div class="text-lg font-bold text-purple-800">{{ $exam->total_marks }}</div>
        </div>
    </div>

    <!-- Student Information Section -->
    <div class="student-info p-4 rounded-lg mb-6">
        <h3 class="font-bold text-lg mb-3 text-center text-gray-700">STUDENT INFORMATION</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="space-y-2">
                <div>Full Name: _________________________________</div>
                <div>Admission Number: _________________________</div>
            </div>
            <div class="space-y-2">
                <div>Class & Stream: ___________________________</div>
                <div>Date: ____________________________________</div>
            </div>
        </div>
        <div class="mt-3 text-center text-xs text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Fill in your details clearly in block letters
        </div>
    </div>

    <!-- Exam Instructions -->
    @if($exam->instructions)
        <div class="instructions-box p-4 rounded-lg mb-6">
            <h3 class="font-bold text-lg mb-2 flex items-center">
                <i class="fas fa-info-circle mr-2 text-yellow-600"></i>
                EXAMINATION INSTRUCTIONS
            </h3>
            <div class="text-sm text-gray-700 leading-relaxed">
                {!! nl2br(e($exam->instructions)) !!}
            </div>
        </div>
    @endif

    <!-- General Instructions -->
    <div class="general-instructions p-4 rounded-lg mb-8">
        <h3 class="font-bold text-lg mb-3 flex items-center">
            <i class="fas fa-clipboard-list mr-2 text-blue-600"></i>
            GENERAL INSTRUCTIONS
        </h3>
        <ul class="text-sm text-gray-700 space-y-2 list-none">
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>Write your answers in the spaces provided below each question</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>For multiple choice questions, circle the letter of the correct answer</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>For true/false questions, circle either TRUE or FALSE</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>All questions are compulsory and must be attempted</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>Read each question carefully before answering</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>Manage your time wisely across all questions</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>No cheating or unauthorized materials allowed</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0"></i>
                <span>Write legibly in blue or black ink only</span>
            </li>
        </ul>
    </div>

    <!-- Questions Section -->
    <div class="questions-section">
        <h3 class="text-xl font-bold mb-6 text-center text-gray-800 border-b pb-2">
            QUESTIONS
        </h3>

        @foreach($exam->questions as $index => $question)
            <div class="question-item mb-8 p-6 rounded-lg break-inside-avoid">
                <!-- Question Header -->
                <div class="flex justify-between items-start mb-4 pb-3 border-b">
                    <h4 class="font-bold text-lg text-gray-800">
                        QUESTION {{ $index + 1 }}
                        <span class="text-sm font-normal text-gray-600 ml-2">({{ $question->pivot->points }} marks)</span>
                    </h4>
                    <span class="bg-gray-100 px-3 py-1 rounded-full text-xs font-medium text-gray-700 capitalize">
                        {{ str_replace('_', ' ', $question->type) }}
                    </span>
                </div>

                <!-- Question Text -->
                <div class="question-text mb-6 text-gray-800 text-lg leading-relaxed">
                    {!! nl2br(e($question->question_text)) !!}
                </div>

                <!-- Multiple Choice Options -->
                @if($question->type === 'mcq')
                    <div class="options-space ml-4 space-y-3">
                        @foreach($question->options as $optionIndex => $option)
                            <div class="flex items-start {{ $includeAnswers && $option->is_correct ? 'correct-answer p-3 rounded-lg' : '' }}">
                                <span class="font-bold text-gray-700 mr-3">{{ chr(65 + $optionIndex) }}.</span>
                                <span class="text-gray-800 flex-1">{{ $option->option_text }}</span>
                                @if($includeAnswers && $option->is_correct)
                                    <span class="ml-3 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold whitespace-nowrap">
                        <i class="fas fa-check mr-1"></i>Correct
                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>


                    <!-- True/False Question -->
                @elseif($question->type === 'true_false')
                    <div class="answer-space mt-4 p-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-2 font-medium">Circle the correct answer:</div>
                        <div class="flex space-x-8 text-lg font-bold">
                            <div class="true-false-option">
                                <span class="option-letter">T</span>
                                <span>TRUE</span>
                            </div>
                            <div class="true-false-option">
                                <span class="option-letter">F</span>
                                <span>FALSE</span>
                            </div>
                        </div>
                        @if($includeAnswers)
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-sm font-medium text-blue-800 mb-1">Correct Answer:</div>
                                <div class="text-sm text-blue-700">{{ ucfirst($question->correct_answer) }}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Short Answer -->
                @elseif($question->type === 'short_answer')
                    <div class="answer-space mt-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-2 font-medium">Write your answer below:</div>
                        <div class="answer-lines h-32 border border-gray-300 rounded bg-white"></div>
                        @if($includeAnswers && $question->expected_answer)
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="text-sm font-medium text-blue-800 mb-1">Expected Answer:</div>
                                <div class="text-sm text-blue-700">{!! nl2br(e($question->expected_answer)) !!}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Essay Question -->
                @elseif($question->type === 'essay')
                    <div class="answer-space mt-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-2 font-medium">Write your essay below:</div>
                        <div class="answer-lines h-64 border border-gray-300 rounded bg-white"></div>
                        @if($includeAnswers && $question->grading_rubric)
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <div class="text-sm font-medium text-purple-800 mb-1">Grading Rubric:</div>
                                <div class="text-sm text-purple-700">{!! nl2br(e($question->grading_rubric)) !!}</div>
                            </div>
                        @endif
                    </div>

                    <!-- Fill in the Blanks -->
                @elseif($question->type === 'fill_blank')
                    <div class="answer-space mt-4 rounded-lg">
                        <div class="text-sm text-gray-600 mb-2 font-medium">Fill in the blanks:</div>
                        <div class="p-4 bg-white border rounded-lg">
                            @php
                                $blankText = $question->blank_question ?? '';
                                $blanks = preg_match_all('/\[blank\]/', $blankText);
                                $blankCount = $blanks ?: 1;
                            @endphp
                            <div class="text-gray-800 mb-4 leading-relaxed">
                                @php
                                    $parts = preg_split('/\[blank\]/', $blankText);
                                    $output = '';
                                    for ($i = 0; $i < count($parts); $i++) {
                                        $output .= e($parts[$i]);
                                        if ($i < count($parts) - 1) {
                                            $output .= '<span class="fill-blank-line"></span>';
                                        }
                                    }
                                @endphp
                                {!! $output !!}
                            </div>
                        </div>
                        @if($includeAnswers && $question->blank_answers)
                            <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="text-sm font-medium text-green-800 mb-1">Correct Answers:</div>
                                <div class="text-sm text-green-700">{{ $question->blank_answers }}</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Page break after every 3 questions for better printing -->
            @if(($index + 1) % 3 === 0)
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>

    <!-- Answer Key Section (Only for teacher with answers) -->
    @if($includeAnswers && !$studentVersion)
        <div class="answer-key-section mt-12 pt-8 page-break">
            <h3 class="text-2xl font-bold mb-6 text-center text-purple-800">
                <i class="fas fa-key mr-2"></i>
                ANSWER KEY
            </h3>

            @foreach($exam->questions as $index => $question)
                <div class="mb-6 p-4 bg-white rounded-lg border">
                    <h4 class="font-bold text-lg text-gray-800 mb-3">
                        Question {{ $index + 1 }} - Correct Answer
                    </h4>

                    @if($question->type === 'mcq')
                        @foreach($question->options as $optionIndex => $option)
                            @if($option->is_correct)
                                <div class="correct-answer p-3 rounded-lg">
                                    <span class="font-bold text-green-700">{{ chr(65 + $optionIndex) }}. {{ $option->option_text }}</span>
                                </div>
                            @endif
                        @endforeach
                    @elseif($question->type === 'true_false')
                        <div class="correct-answer p-3 rounded-lg">
                            <span class="font-bold text-green-700">{{ ucfirst($question->correct_answer) }}</span>
                        </div>
                    @elseif($question->type === 'short_answer' && $question->expected_answer)
                        <div class="correct-answer p-3 rounded-lg">
                            <div class="font-bold text-green-700 mb-1">Expected Answer:</div>
                            <div class="text-green-700">{!! nl2br(e($question->expected_answer)) !!}</div>
                        </div>
                    @elseif($question->type === 'fill_blank' && $question->blank_answers)
                        <div class="correct-answer p-3 rounded-lg">
                            <div class="font-bold text-green-700 mb-1">Correct Answers:</div>
                            <div class="text-green-700">{{ $question->blank_answers }}</div>
                        </div>
                    @elseif($question->type === 'essay' && $question->grading_rubric)
                        <div class="correct-answer p-3 rounded-lg">
                            <div class="font-bold text-green-700 mb-1">Grading Rubric:</div>
                            <div class="text-green-700">{!! nl2br(e($question->grading_rubric)) !!}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="mt-12 pt-6 border-t text-center text-sm text-gray-500">
        <p>Lincoln eLearning Academy - Examination Paper</p>
        <p>Generated on: {{ now()->format('F j, Y \\a\\t g:i A') }}</p>
        <p class="mt-2 text-xs">Page 1 of 1</p>
    </div>
</div>

<script>
    // Auto-print option
    @if(request()->has('autoprint'))
        window.onload = function() {
        window.print();
    }
    @endif

    // Close window after print
    window.onafterprint = function() {
        // Optional: auto-close after printing
        // window.close();
    };
</script>
</body>
</html>
