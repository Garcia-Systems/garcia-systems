<?php
namespace App\Http\Controllers;
use App\Models\Article;
class ArticleController extends Controller { public function index(){return view('articles.index',['articles'=>Article::with('category','tags')->latest('published_at')->paginate(9)]);} public function show(Article $article){return view('articles.show',compact('article'));}}