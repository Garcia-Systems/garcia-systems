<x-layouts.app title="Contact">
    <section class="mx-auto max-w-3xl px-6 py-16">
        <h1 class="text-4xl font-bold">Contact</h1>
        <p class="mt-4 text-slate-300">Tell us what workflow, system, or operational bottleneck you want to improve.</p>

        @if(session('status'))
            <div class="mt-6 rounded border border-emerald-400/40 bg-emerald-500/20 p-4 text-emerald-100" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if(session('contact_error_summary') || $errors->any())
            <div class="mt-6 rounded border border-rose-400/40 bg-rose-500/20 p-4 text-rose-100" role="alert">
                <p class="font-semibold">{{ session('contact_error_summary', 'Please fix the highlighted fields and try again.') }}</p>
                @if($errors->any())
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form class="mt-8 grid gap-4" method="post" action="{{ route('contact.submit') }}" novalidate>
            @csrf
            <label class="grid gap-2">Name
                <input class="rounded bg-white/10 p-3 @error('name') ring-2 ring-rose-400 @enderror" name="name" placeholder="Name" value="{{ old('name') }}" required>
                @error('name')<span class="text-sm text-rose-300">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">Email
                <input class="rounded bg-white/10 p-3 @error('email') ring-2 ring-rose-400 @enderror" name="email" type="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')<span class="text-sm text-rose-300">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">Company
                <input class="rounded bg-white/10 p-3 @error('company') ring-2 ring-rose-400 @enderror" name="company" placeholder="Company" value="{{ old('company') }}">
                @error('company')<span class="text-sm text-rose-300">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">Service interest
                <input class="rounded bg-white/10 p-3 @error('service_interest') ring-2 ring-rose-400 @enderror" name="service_interest" placeholder="Service interest" value="{{ old('service_interest') }}">
                @error('service_interest')<span class="text-sm text-rose-300">{{ $message }}</span>@enderror
            </label>
            <label class="grid gap-2">Message
                <textarea class="rounded bg-white/10 p-3 @error('message') ring-2 ring-rose-400 @enderror" name="message" rows="6" placeholder="What are you trying to improve?" required>{{ old('message') }}</textarea>
                @error('message')<span class="text-sm text-rose-300">{{ $message }}</span>@enderror
            </label>
            <button class="rounded bg-cyan-400 px-5 py-3 font-semibold text-slate-950">Send message</button>
        </form>
    </section>
</x-layouts.app>
