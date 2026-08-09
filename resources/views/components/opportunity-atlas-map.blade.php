@props(['chains'])

@php
    $types = ['industry' => 'Industries', 'workflow' => 'Workflows', 'friction' => 'Friction', 'solution' => 'Solution patterns'];
    $nodes = collect($types)->mapWithKeys(fn ($label, $type) => [$type => $chains->map(fn ($chain, $chainIndex) => [...$chain[$type], 'chains' => [$chainIndex]])
        ->groupBy('id')->map(fn ($matches) => [...$matches->first(), 'chains' => $matches->flatMap(fn ($node) => $node['chains'])->unique()->values()->all()])->values()]);
@endphp

<div class="gs-atlas-map" data-atlas-map>
    @if($chains->isNotEmpty())
        <div class="gs-atlas-desktop" data-atlas-stage>
            <div class="gs-atlas-key" aria-hidden="true"><span>Observe context</span><i></i><span>Trace work</span><i></i><span>Locate friction</span><i></i><span>Shape response</span></div>
            <svg class="gs-atlas-connections" data-atlas-connections aria-hidden="true" focusable="false"></svg>
            <div class="gs-atlas-columns">
                @foreach($types as $type => $label)
                    <section class="gs-atlas-column" aria-labelledby="atlas-column-{{ $type }}">
                        <h3 id="atlas-column-{{ $type }}"><span>0{{ $loop->iteration }}</span>{{ $label }}</h3>
                        <div class="gs-atlas-node-list">
                            @foreach($nodes[$type] as $node)
                                <button type="button" class="gs-atlas-node" data-atlas-node="{{ $type }}-{{ $node['id'] }}" data-atlas-chains="{{ implode(',', $node['chains']) }}" aria-pressed="false">
                                    <span class="gs-atlas-node-type">{{ str($type)->replace('_', ' ') }}</span>
                                    <strong>{{ $node['name'] }}</strong>
                                </button>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            @foreach($chains as $index => $chain)
                @foreach([['industry','workflow'], ['workflow','friction'], ['friction','solution']] as [$from,$to])
                    <span hidden data-atlas-edge data-chain="{{ $index }}" data-from="{{ $from }}-{{ $chain[$from]['id'] }}" data-to="{{ $to }}-{{ $chain[$to]['id'] }}"></span>
                @endforeach
            @endforeach
            <button class="gs-atlas-reset gs-focus" type="button" data-atlas-reset hidden>Reset selection</button>
        </div>

        <div class="gs-atlas-mobile" aria-label="Opportunity Atlas relationship paths">
            <div class="gs-atlas-mobile-tabs" role="tablist" aria-label="Choose an opportunity path">
                @foreach($chains as $index => $chain)
                    <button type="button" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="atlas-path-{{ $index }}" id="atlas-tab-{{ $index }}" data-atlas-mobile-tab="{{ $index }}">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</button>
                @endforeach
            </div>
            @foreach($chains as $index => $chain)
                <ol id="atlas-path-{{ $index }}" role="tabpanel" aria-labelledby="atlas-tab-{{ $index }}" class="gs-atlas-mobile-path" @if($index !== 0) hidden @endif>
                    @foreach($types as $type => $label)
                        <li><span>{{ $label }}</span><strong>{{ $chain[$type]['name'] }}</strong>@if(!empty($chain[$type]['description']))<p>{{ $chain[$type]['description'] }}</p>@endif</li>
                    @endforeach
                </ol>
            @endforeach
        </div>

        <div class="sr-only">
            <h3>Opportunity Atlas relationships</h3>
            @foreach($chains as $chain)
                <p>{{ $chain['industry']['name'] }} leads to workflow {{ $chain['workflow']['name'] }}, where {{ $chain['friction']['name'] }} can be addressed by {{ $chain['solution']['name'] }}.</p>
            @endforeach
        </div>
    @else
        <div class="gs-atlas-empty"><p class="gs-eyebrow">Atlas preview</p><h3>Opportunity paths are being mapped.</h3><p>Explore the full Atlas as industries, workflows, friction points, and solution patterns are added.</p></div>
    @endif
</div>
