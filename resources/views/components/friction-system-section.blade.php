@php
    $frictionNodes = ['Spreadsheets', 'Emails', 'Manual work', 'Phone calls', 'Duplicate data', 'Handoffs', 'Errors', 'Disconnected systems', 'Unclear ownership'];
    $outcomes = ['Clear data', 'Automated work', 'Better decisions'];
@endphp

<section class="gs-shell gs-section" aria-labelledby="friction-system-title" data-friction-system>
    <div class="max-w-3xl">
        <p class="gs-eyebrow">Operational transformation</p>
        <h2 id="friction-system-title" class="mt-3 text-3xl font-bold tracking-tight md:text-5xl">From Friction to System</h2>
        <p class="mt-4 text-lg text-slate-300">Good technology starts by understanding the work.</p>
    </div>

    <div class="gs-friction-story gs-panel mt-10" style="--story-progress: 1">
        <div class="gs-friction-grid" aria-hidden="true"></div>
        <div class="gs-friction-stage gs-friction-stage--messy">
            <p class="gs-friction-label">Fragmented work</p>
            <svg class="gs-friction-wires" viewBox="0 0 360 400" preserveAspectRatio="none" aria-hidden="true">
                <g fill="none" stroke="currentColor" stroke-linecap="round">
                    <path d="M36 62 C148 30 88 154 212 116 S280 36 334 84" />
                    <path d="M20 208 C112 142 153 278 328 190" />
                    <path d="M52 332 C120 250 216 382 320 294" />
                    <path d="M76 98 L286 326" />
                </g>
            </svg>
            <ul class="gs-friction-nodes" aria-label="Disconnected operational friction">
                @foreach($frictionNodes as $node)
                    <li style="--node-index: {{ $loop->index }}">{{ $node }}</li>
                @endforeach
            </ul>
        </div>

        <div class="gs-friction-arrow" aria-hidden="true"><span></span></div>

        <div class="gs-friction-stage gs-friction-stage--system">
            <p class="gs-friction-label">Structured understanding</p>
            <span class="gs-system-input-label">Inputs</span>
            <div class="gs-system-inputs"><span>People</span><span>Workflow</span><span>Data</span></div>
            <div class="gs-system-core"><small>System design</small><strong>Integrated<br>System</strong><span>Automation layer</span></div>
            <div class="gs-system-path" aria-hidden="true"></div>
        </div>

        <div class="gs-friction-arrow" aria-hidden="true"><span></span></div>

        <div class="gs-friction-stage gs-friction-stage--outcomes">
            <p class="gs-friction-label">Measurable outcomes</p>
            <ul class="gs-outcome-list">
                @foreach($outcomes as $outcome)<li>{{ $outcome }}</li>@endforeach
            </ul>
            <div class="gs-measurable-outcome"><span aria-hidden="true">✓</span><strong>Measurable<br>Outcome</strong></div>
        </div>
    </div>
    <p class="sr-only">Disconnected spreadsheets, emails, manual work, phone calls, duplicate data, handoffs, errors, systems, and ownership are understood as people, workflow, and data; designed into an integrated automated system; and turned into clear data, automated work, better decisions, and a measurable outcome.</p>
</section>
