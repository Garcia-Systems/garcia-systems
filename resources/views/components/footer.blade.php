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
                <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-sm font-bold text-slate-300 hover:border-cyan-300 hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="https://www.linkedin.com/company/garcia-systems-lcc" target="_blank" rel="noopener noreferrer" aria-label="Garcia Systems on LinkedIn">in</a>
                <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-sm font-bold text-slate-300 hover:border-cyan-300 hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="https://www.youtube.com/@GarciaSystems" target="_blank" rel="noopener noreferrer" aria-label="Garcia Systems on YouTube">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M21.58 7.19a2.73 2.73 0 0 0-1.92-1.93C17.96 4.8 12 4.8 12 4.8s-5.96 0-7.66.46a2.73 2.73 0 0 0-1.92 1.93A28.55 28.55 0 0 0 2 12a28.55 28.55 0 0 0 .42 4.81 2.73 2.73 0 0 0 1.92 1.93c1.7.46 7.66.46 7.66.46s5.96 0 7.66-.46a2.73 2.73 0 0 0 1.92-1.93A28.55 28.55 0 0 0 22 12a28.55 28.55 0 0 0-.42-4.81ZM10 15.27V8.73L15.45 12 10 15.27Z" />
                    </svg>
                </a>
                <a class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/10 text-sm font-bold text-slate-300 hover:border-cyan-300 hover:text-cyan-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-300" href="https://substack.com/@garciasystems" target="_blank" rel="noopener noreferrer" aria-label="Garcia Systems on Substack">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M5 4h14v2H5V4Zm0 4h14v2H5V8Zm0 4h14v8l-7-3.5L5 20v-8Z" />
                    </svg>
                </a>
            </div>
            <p class="mt-6 text-sm text-slate-500">© {{ date('Y') }} Garcia Systems. All rights reserved.</p>
        </section>
    </div>
</footer>
