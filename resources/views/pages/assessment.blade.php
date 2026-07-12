<x-layouts.app title="AI Readiness Assessment" page-description="Score workflow clarity, data quality, ownership, and risk to identify practical AI or automation pilot readiness.">
    <section class="mx-auto max-w-3xl px-6 py-16">
        <h1 class="text-4xl font-bold">AI Readiness Assessment</h1>
        <p class="mt-4 text-slate-300">Score each area from 1 (early) to 5 (strong).</p>

        @if(session('assessment_unavailable') || $questions->isEmpty())
            <x-card class="mt-8"><p class="text-slate-300">{{ session('assessment_unavailable', 'The AI Readiness Assessment is temporarily unavailable while the question set is being reviewed.') }}</p></x-card>
        @else

        @if($errors->any())
            <div class="mt-6 rounded border border-rose-400/40 bg-rose-500/20 p-4 text-rose-100" role="alert">
                <p class="font-semibold">Please complete the assessment with valid scores.</p>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-8 grid gap-6" method="post" action="{{ route('assessment.submit') }}" novalidate>
            @csrf
            <div class="hidden" aria-hidden="true">
                <label>Website
                    <input tabindex="-1" autocomplete="off" name="website" value="{{ old('website') }}">
                </label>
            </div>
            <input class="rounded bg-white/10 p-3" name="name" placeholder="Name" value="{{ old('name') }}">
            <input class="rounded bg-white/10 p-3" name="email" type="email" placeholder="Email" value="{{ old('email') }}">
            <input class="rounded bg-white/10 p-3" name="company" placeholder="Company" value="{{ old('company') }}">
            @foreach($questions as $q)
                <x-card>
                    @if($q->category)
                        <p class="mb-2 text-sm font-semibold uppercase tracking-[0.2em] text-cyan-300">{{ $q->category }}</p>
                    @endif
                    <label class="font-semibold" for="q{{ $q->id }}">{{ $q->question }}</label>
                    <p class="text-slate-300">{{ $q->help_text }}</p>
                    <select id="q{{ $q->id }}" class="mt-3 rounded bg-slate-900 p-3 @error('responses.'.$q->id) ring-2 ring-rose-400 @enderror" name="responses[{{ $q->id }}]" required>
                        <option value="">Select a score</option>
                        @for($i=1;$i<=5;$i++)
                            <option value="{{ $i }}" @selected((string) old('responses.'.$q->id) === (string) $i)>{{ $i }}</option>
                        @endfor
                    </select>
                    @error('responses.'.$q->id)<p class="mt-2 text-sm text-rose-300">{{ $message }}</p>@enderror
                </x-card>
            @endforeach
            <button class="rounded bg-cyan-400 px-5 py-3 font-semibold text-slate-950">See result</button>
        </form>
        @endif
    </section>
</x-layouts.app>
