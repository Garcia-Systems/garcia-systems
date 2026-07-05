<x-admin.layout :heading="'Lead / '.($lead->name ?: $lead->email)">
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5 lg:col-span-2">
            <h2 class="font-semibold text-white">Lead details</h2>
            <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
                <div><dt class="text-slate-400">Name</dt><dd>{{ $lead->name ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Email</dt><dd>{{ $lead->email }}</dd></div>
                <div><dt class="text-slate-400">Company</dt><dd>{{ $lead->company ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Created date</dt><dd>{{ $lead->created_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Source</dt><dd>{{ str($lead->source)->replace('_', ' ')->headline() }}</dd></div>
                <div><dt class="text-slate-400">Assessment score</dt><dd>{{ $lead->assessment_score ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Assessment tier</dt><dd>{{ $lead->assessment_tier ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Next follow-up</dt><dd>{{ $lead->next_follow_up_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Last contacted</dt><dd>{{ $lead->last_contacted_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
                <div><dt class="text-slate-400">Latest activity</dt><dd>{{ $lead->latest_activity_at?->format('M j, Y g:i A') ?: '—' }}</dd></div>
            </dl>
        </section>
        <form method="post" action="{{ route('admin.leads.update', $lead) }}" class="rounded-xl border border-slate-700 bg-slate-900 p-5">
            @csrf @method('put')
            <h2 class="font-semibold text-white">Update lead</h2>
            <label class="mt-4 block text-sm">Status<select class="mt-1 w-full rounded px-3 py-2 text-slate-900" name="status">@foreach($statuses as $status)<option value="{{ $status }}" @selected(old('status', $lead->status) === $status)>{{ str($status)->headline() }}</option>@endforeach</select></label>
            <label class="mt-4 block text-sm">Last contacted<input class="mt-1 w-full rounded px-3 py-2 text-slate-900" name="last_contacted_at" type="datetime-local" value="{{ old('last_contacted_at', $lead->last_contacted_at?->format('Y-m-d\\TH:i')) }}"></label>
            <label class="mt-4 block text-sm">Next follow-up<input class="mt-1 w-full rounded px-3 py-2 text-slate-900" name="next_follow_up_at" type="datetime-local" value="{{ old('next_follow_up_at', $lead->next_follow_up_at?->format('Y-m-d\\TH:i')) }}"></label>
            <label class="mt-4 block text-sm">Notes<textarea class="mt-1 w-full rounded px-3 py-2 text-slate-900" name="notes" rows="6">{{ old('notes', $lead->notes) }}</textarea></label>
            <button class="mt-4 rounded bg-cyan-400 px-4 py-2 font-semibold text-slate-950">Save lead</button>
        </form>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5"><h2 class="font-semibold text-white">Related contact submissions</h2><div class="mt-4 space-y-4">@forelse($lead->contactSubmissions as $submission)<article class="text-sm text-slate-300"><p class="font-medium text-white">{{ $submission->name }} · {{ $submission->created_at?->format('M j, Y') }}</p><p>{{ $submission->company ?: 'No company' }} · {{ $submission->service_interest ?: 'No service interest' }}</p><p class="mt-2">{{ $submission->message }}</p></article>@empty<p class="text-sm text-slate-400">No contact submissions.</p>@endforelse</div></section>
        <section class="rounded-xl border border-slate-700 bg-slate-900 p-5"><h2 class="font-semibold text-white">Related assessments</h2><div class="mt-4 space-y-4">@forelse($lead->assessments as $assessment)<article class="text-sm text-slate-300"><p class="font-medium text-white">Score: {{ $assessment->score }} · {{ $assessment->result_tier }}</p><p>{{ $assessment->summary }}</p><p class="mt-1 text-slate-400">{{ $assessment->created_at?->format('M j, Y') }}</p></article>@empty<p class="text-sm text-slate-400">No assessments.</p>@endforelse</div></section>
    </div>
</x-admin.layout>
