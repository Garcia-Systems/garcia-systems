<?php
namespace App\Http\Controllers;
use App\Models\{Article,Assessment,AssessmentQuestion,AssessmentResponse,ContactSubmission,FrictionPoint,Industry,Video};
use Illuminate\Http\Request;
class PageController extends Controller
{
 public function home(){return view('pages.home',['articles'=>Article::latest('published_at')->take(3)->get(),'videos'=>Video::take(3)->get(),'frictions'=>FrictionPoint::with('workflow.industry')->take(3)->get()]);}
 public function about(){return view('pages.simple',['title'=>'About Garcia Systems','body'=>'Garcia Systems helps growing teams identify practical automation opportunities, clarify workflows, and ship useful technology in measured phases.']);}
 public function services(){return view('pages.services');}
 public function videos(){return view('pages.videos',['videos'=>Video::latest()->get()]);}
 public function tools(){return view('pages.tools');}
 public function atlas(){return view('pages.atlas',['industries'=>Industry::with('workflows.frictionPoints.solutionPatterns')->get()]);}
 public function contact(){return view('pages.contact');}
 public function submitContact(Request $request){$data=$request->validate(['name'=>'required|max:120','email'=>'required|email|max:180','company'=>'nullable|max:180','service_interest'=>'nullable|max:180','message'=>'required|max:5000']); ContactSubmission::create($data); return back()->with('status','Thanks — your message has been saved.');}
 public function assessment(){return view('pages.assessment',['questions'=>AssessmentQuestion::orderBy('sort_order')->get()]);}
 public function submitAssessment(Request $request){$data=$request->validate(['name'=>'nullable|max:120','email'=>'nullable|email|max:180','company'=>'nullable|max:180','responses'=>'required|array']);$score=array_sum(array_map('intval',$data['responses']));$tier=$score>=16?'Ready to prioritize pilots':($score>=10?'Foundation in progress':'Early readiness');$summary=$score>=16?'You appear ready to select a focused pilot and define success metrics.':($score>=10?'You have useful foundations; start with one workflow and tighten data/process ownership.':'Begin with workflow clarity, data quality, and a narrow business problem before investing heavily.');$assessment=Assessment::create([...$request->only('name','email','company'),'score'=>$score,'result_tier'=>$tier,'summary'=>$summary]);foreach($data['responses'] as $qid=>$value){AssessmentResponse::create(['assessment_id'=>$assessment->id,'assessment_question_id'=>$qid,'score'=>(int)$value]);} return redirect()->route('assessment.result',$assessment);}
 public function assessmentResult(Assessment $assessment){return view('pages.assessment-result',['assessment'=>$assessment]);}
}