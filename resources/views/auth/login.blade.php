<x-layouts.app title="Admin login">
    <section class="mx-auto max-w-md px-6 py-16">
        <h1 class="text-3xl font-bold">Admin login</h1>
        <p class="mt-4 text-slate-300">Authentication is required to access the admin area.</p>

        @if(session('status'))
            <div class="mt-6 rounded bg-emerald-500/20 p-3 text-sm text-emerald-100">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="mt-6 rounded bg-red-500/20 p-3 text-sm text-red-100">
                <p class="font-semibold">Please fix the following:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5 rounded-xl border border-slate-700 bg-slate-900 p-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-200">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-2 w-full rounded border border-slate-700 bg-slate-950 px-3 py-2 text-white focus:border-cyan-400 focus:outline-none">
                @error('email')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-200">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded border border-slate-700 bg-slate-950 px-3 py-2 text-white focus:border-cyan-400 focus:outline-none">
                @error('password')<p class="mt-2 text-sm text-red-300">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-700 bg-slate-950">
                Remember me
            </label>
            <button type="submit" class="w-full rounded bg-cyan-400 px-4 py-2 font-semibold text-slate-950 hover:bg-cyan-300">Log in</button>
        </form>
    </section>
</x-layouts.app>
