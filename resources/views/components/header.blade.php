@php
    $navItems = [
        ['label' => 'Home', 'route' => 'home', 'patterns' => ['home']],
        ['label' => 'Services', 'route' => 'services', 'patterns' => ['services']],
        ['label' => 'Laboratories', 'route' => 'laboratories', 'patterns' => ['laboratories']],
        ...(config('garcia.features.opportunity_atlas') ? [['label' => 'Atlas', 'route' => 'atlas', 'patterns' => ['atlas*']]] : []),
        ...(config('garcia.features.ai_assessment') ? [['label' => 'Assessment', 'route' => 'assessment', 'patterns' => ['assessment*']]] : []),
        ['label' => 'Thinking', 'route' => 'articles.index', 'patterns' => ['articles*', 'videos']],
        ['label' => 'About', 'route' => 'about', 'patterns' => ['about']],
        ['label' => 'Contact', 'route' => 'contact', 'patterns' => ['contact']],
    ];

    if (auth()->check()) {
        $navItems[] = ['label' => 'Admin', 'route' => 'admin.index', 'patterns' => ['admin*']];
    }

    $baseLink = 'gs-focus relative rounded-lg px-3 py-2 text-sm font-medium transition hover:text-white focus:outline-none';
    $inactiveLink = 'text-slate-300';
    $activeLink = 'text-cyan-200 after:absolute after:inset-x-3 after:-bottom-1 after:h-px after:bg-cyan-300 after:shadow-[0_0_9px_rgb(103_232_249)]';
@endphp

<a href="#main-content" class="gs-button gs-button-primary fixed left-4 top-3 z-[60] -translate-y-24 focus:translate-y-0">Skip to content</a>
<header class="sticky top-0 z-50 border-b border-cyan-200/10 bg-[#030914]/85 shadow-[0_8px_30px_rgb(0_0_0/.18)] backdrop-blur-xl">
    <nav class="gs-shell flex min-h-16 items-center justify-between gap-4" aria-label="Primary navigation">
        <a
            href="{{ route('home') }}"
            class="gs-focus flex items-center gap-3 rounded text-lg font-bold tracking-tight text-white focus:outline-none"
        >
            <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-cyan-200/30 bg-cyan-300/5 text-xs text-cyan-200 shadow-[0_0_18px_rgb(34_211_238/.12)]" aria-hidden="true">GS</span>
            <span>Garcia <span class="font-medium text-slate-400">Systems</span></span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach($navItems as $item)
                @php($isActive = request()->routeIs(...$item['patterns']))
                <a
                    href="{{ route($item['route']) }}"
                    class="{{ $item['route'] === 'contact' ? 'gs-button gs-button-primary ml-2 min-h-0 px-4 py-2 text-sm' : $baseLink.' '.($isActive ? $activeLink : $inactiveLink) }}"
                    @if($isActive) aria-current="page" @endif
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <details class="group relative lg:hidden">
            <summary class="gs-focus flex cursor-pointer list-none items-center gap-2 rounded-full border border-cyan-200/20 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-cyan-300/50 hover:bg-white/5 focus:outline-none" aria-label="Open primary navigation menu">
                <span>Menu</span>
                <span class="flex h-5 w-5 flex-col justify-center gap-1" aria-hidden="true">
                    <span class="block h-0.5 rounded bg-current"></span>
                    <span class="block h-0.5 rounded bg-current"></span>
                    <span class="block h-0.5 rounded bg-current"></span>
                </span>
            </summary>

            <div class="gs-panel absolute right-0 mt-3 w-[min(18rem,calc(100vw-2rem))] p-3">
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
