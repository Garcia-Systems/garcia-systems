@props(['laboratories'])

<div class="gs-laboratory-grid" data-laboratories>
@forelse($laboratories as $index => $lab)
    <article class="gs-laboratory {{ $index === 0 ? 'gs-laboratory--featured' : '' }}" data-laboratory tabindex="0">
        <div class="gs-lab-diagram gs-lab-diagram--{{ $lab['diagram'] }}" aria-hidden="true">
            @if($lab['diagram'] === 'atlas')
                <svg viewBox="0 0 520 230"><path d="M105 55H230M290 55H415M105 175H230M290 175H415M260 80V150"/><path class="gs-lab-signal" d="M105 55H260V175H415"/></svg>
                <span style="--x:5%;--y:12%">Industry</span><span style="--x:42%;--y:12%">Workflow</span><span style="--x:5%;--y:65%">Friction</span><span style="--x:76%;--y:65%">Solution</span>
            @elseif($lab['diagram'] === 'readiness')
                <svg viewBox="0 0 520 230"><path d="M85 48H220M85 92H220M85 136H220M85 180H220M250 114H355M355 114l45-45M355 114l45 45"/><path class="gs-lab-signal" d="M85 48H235V114H400"/></svg>
                <span style="--x:3%;--y:7%">Workflow</span><span style="--x:3%;--y:61%">Owners</span><span style="--x:44%;--y:39%">Score</span><span style="--x:75%;--y:12%">Tier</span><span style="--x:75%;--y:67%">Next step</span>
            @else
                <svg viewBox="0 0 520 230"><path d="M90 115H205M265 115H380M410 115V175"/><path class="gs-lab-signal" d="M90 115H410V175"/></svg>
                <span style="--x:3%;--y:39%">Draft</span><span style="--x:37%;--y:39%">Review</span><span style="--x:70%;--y:39%">Publish</span><span style="--x:70%;--y:69%">Library</span>
            @endif
        </div>
        <div class="gs-lab-content">
            <p class="gs-lab-index">LAB / {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
            <h3>{{ $lab['name'] }}</h3>
            @if(filled($lab['context'] ?? null))<p class="gs-lab-context">{{ $lab['context'] }}</p>@endif
            <p class="gs-lab-description">{{ $lab['description'] }}</p>
            @if(filled($lab['route'] ?? null) && Route::has($lab['route']))<a class="gs-link gs-focus" href="{{ route($lab['route']) }}">{{ $lab['cta'] ?? 'Explore laboratory' }} <span aria-hidden="true">→</span></a>@endif
        </div>
    </article>
@empty
    <div class="gs-laboratory-empty"><h3>Laboratory exhibits are being prepared.</h3><p>Explore the current tools while the next executable system showcase is documented.</p><a class="gs-link gs-focus" href="{{ route('tools') }}">Explore tools <span aria-hidden="true">→</span></a></div>
@endforelse
</div>
