<x-layouts.app :title="$title ?? 'Admin'">
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-white/10 bg-white/5 p-5">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-cyan-300">Garcia Systems CMS</p>
                <h1 class="mt-2 text-3xl font-bold">{{ $heading ?? 'Admin' }}</h1>
            </div>

            <nav class="flex flex-wrap items-center gap-2 text-sm" aria-label="Admin navigation">
                @foreach(['articles' => 'Articles', 'videos' => 'Videos', 'categories' => 'Categories', 'tags' => 'Tags'] as $route => $label)
                    <a class="rounded-full px-3 py-2 text-slate-300 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('admin.'.$route.'.index') }}">{{ $label }}</a>
                @endforeach
                <a class="rounded-full px-3 py-2 text-slate-300 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('admin.atlas.index', 'industries') }}">Atlas</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full px-3 py-2 text-slate-300 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300">Log out</button>
                </form>
            </nav>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-400/40 bg-emerald-500/20 p-4 text-emerald-100" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-400/40 bg-rose-500/20 p-4 text-rose-100" role="alert">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </section>
</x-layouts.app>
