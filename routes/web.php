<?php
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
Route::get('/', [PageController::class,'home'])->name('home');
Route::get('/about', [PageController::class,'about'])->name('about');
Route::get('/services', [PageController::class,'services'])->name('services');
Route::get('/articles', [ArticleController::class,'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class,'show'])->name('articles.show');
Route::get('/videos', [PageController::class,'videos'])->name('videos');
Route::get('/tools', [PageController::class,'tools'])->name('tools');
Route::get('/opportunity-atlas', [PageController::class,'atlas'])->name('atlas');
Route::get('/contact', [PageController::class,'contact'])->name('contact');
Route::post('/contact', [PageController::class,'submitContact'])->name('contact.submit');
Route::get('/ai-readiness-assessment', [PageController::class,'assessment'])->name('assessment');
Route::post('/ai-readiness-assessment', [PageController::class,'submitAssessment'])->name('assessment.submit');
Route::get('/ai-readiness-assessment/{assessment}', [PageController::class,'assessmentResult'])->name('assessment.result');