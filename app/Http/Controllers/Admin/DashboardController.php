<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Assessment;
use App\Models\ContactSubmission;
use App\Models\Industry;
use App\Models\Video;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.index', [
            'metrics' => [
                'Articles' => Article::count(),
                'Videos' => Video::count(),
                'Industries' => Industry::count(),
                'Assessments' => Assessment::count(),
                'Contact submissions' => ContactSubmission::count(),
            ],
            'recentArticles' => Article::latest()->take(5)->get(),
            'recentAssessments' => Assessment::latest()->take(5)->get(),
            'recentContactSubmissions' => ContactSubmission::latest()->take(5)->get(),
        ]);
    }
}
