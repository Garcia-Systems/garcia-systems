<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;

class AssessmentSubmissionController extends Controller
{
    public function index()
    {
        return view('admin.assessment-submissions.index', [
            'assessments' => Assessment::query()->latest()->paginate(50),
        ]);
    }

    public function show(Assessment $assessment)
    {
        return view('admin.assessment-submissions.show', [
            'assessment' => $assessment->load('responses.question'),
        ]);
    }
}
