<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\Tag; use Illuminate\Http\RedirectResponse; use Illuminate\Http\Request; use Illuminate\Support\Str; use Illuminate\Validation\Rule; use Illuminate\View\View;
class TagController extends Controller { public function index(): View { return view('admin.tags.index',['tags'=>Tag::orderBy('name')->paginate(50)]); } public function store(Request $request): RedirectResponse { $data=$request->validate(['name'=>['required','string','max:255'],'slug'=>['nullable','string','max:255',Rule::unique('tags','slug')]]); $data['slug']=Str::slug($data['slug']?:$data['name']); Tag::create($data); return back()->with('status','Tag created.'); } }
