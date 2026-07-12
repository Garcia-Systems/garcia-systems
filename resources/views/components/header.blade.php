@php
    $navItems = [
        ['label' => 'Home', 'route' => 'home', 'patterns' => ['home']],
        ['label' => 'About', 'route' => 'about', 'patterns' => ['about']],
        ['label' => 'Services', 'route' => 'services', 'patterns' => ['services']],
        ...(config('garcia.features.opportunity_atlas') ? [['label' => 'Atlas', 'route' => 'atlas', 'patterns' => ['atlas*']]] : []),
        ...(config('garcia.features.ai_assessment') ? [['label' => 'Assessment', 'route' => 'assessment', 'patterns' => ['assessment*']]] : []),
        ['label' => 'Articles', 'route' => 'articles.index', 'patterns' => ['articles*']],
        ['label' => 'Videos', 'route' => 'videos', 'patterns' => ['videos']],
        ['label' => 'Contact', 'route' => 'contact', 'patterns' => ['contact']],
    ];

    if (auth()->check()) {
        $navItems[] = ['label' => 'Admin', 'route' => 'admin.index', 'patterns' => ['admin*']];
    }

    $baseLink = 'rounded-full px-3 py-2 text-sm font-medium transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950';
    $inactiveLink = 'text-slate-300';
    $activeLink = 'bg-cyan-400 text-slate-950 shadow-sm shadow-cyan-950/40 hover:bg-cyan-300 hover:text-slate-950';
@endphp

<header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/95 backdrop-blur">
    <nav class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-4" aria-label="Primary navigation">
        <a
            href="{{ route('home') }}"
            class="rounded text-xl font-bold tracking-tight text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
        >
            Garcia Systems
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach($navItems as $item)
                @php($isActive = request()->routeIs(...$item['patterns']))
                <a
                    href="{{ route($item['route']) }}"
                    class="{{ $baseLink }} {{ $isActive ? $activeLink : $inactiveLink }}"
                    @if($isActive) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <details class="group relative lg:hidden">
            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-300/50 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950" aria-label="Open primary navigation menu">
                <span>Menu</span>
                <span class="flex h-5 w-5 flex-col justify-center gap-1" aria-hidden="true">
                    <span class="block h-0.5 rounded bg-current"></span>
                    <span class="block h-0.5 rounded bg-current"></span>
                    <span class="block h-0.5 rounded bg-current"></span>
                </span>
            </summary>

            <div class="absolute right-0 mt-3 w-72 rounded-3xl border border-white/10 bg-slate-900 p-3 shadow-2xl shadow-slate-950/60">
                <div class="grid gap-1" aria-label="Mobile primary navigation">
                    @foreach($navItems as $item)
                        @php($isActive = request()->routeIs(...$item['patterns']))
                        <a
                            href="{{ route($item['route']) }}"
                            class="{{ $baseLink }} {{ $isActive ? $activeLink : $inactiveLink }}"
                            @if($isActive) aria-current="page" @endif
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </details>
    </nav>
</header>
