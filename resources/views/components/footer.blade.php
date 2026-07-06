<footer class="mt-20 border-t border-white/10 bg-slate-950">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-12 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
        <section>
            <h2 class="text-lg font-bold text-white">Garcia Systems</h2>
            <p class="mt-4 max-w-sm text-sm leading-6 text-slate-300">
                Practical systems, automation, product, and AI readiness consulting for teams that need clearer workflows and measurable execution paths.
            </p>
            <p class="mt-5 text-sm font-semibold text-cyan-300">Newsletter</p>
            <p class="mt-2 text-sm text-slate-400">Field notes on workflow modernization and practical AI readiness coming soon.</p>
        </section>

        <nav aria-label="Footer navigation">
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-200">Navigate</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-400">
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('home') }}">Home</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('about') }}">About</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('articles.index') }}">Articles</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('videos') }}">Videos</a></li>
            </ul>
        </nav>

        <nav aria-label="Footer services navigation">
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-200">Services</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-400">
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('services') }}">Consulting services</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('atlas') }}">Opportunity Atlas</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('assessment') }}">AI Readiness Assessment</a></li>
                <li><a class="hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </nav>

        <section>
            <h2 class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-200">Social</h2>
            <div class="mt-4 flex gap-3">
                <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-sm font-bold text-slate-300 hover:border-cyan-300 hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="#" aria-label="Garcia Systems on LinkedIn placeholder">in</a>
                <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-sm font-bold text-slate-300 hover:border-cyan-300 hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="#" aria-label="Garcia Systems social profile placeholder">GS</a>
            </div>
            <p class="mt-6 text-sm text-slate-500">© {{ date('Y') }} Garcia Systems. All rights reserved.</p>
        </section>
    </div>
</footer>
