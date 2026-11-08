<div class="profile-facts">
    @foreach (config('basis') as $b)
        <div class="fact-row">
            <div class="fact-label" style="width:150px">
                <span class="pill pill-{{ $b['key'] }}">{{ $b['label'] }}</span>
            </div>
            <div class="fact-body">
                <div class="fact">{{ $b['meaning'] }}</div>
                @if (! empty($b['example']))
                    <div class="fact-note">For example: {{ $b['example'] }}</div>
                @endif
            </div>
        </div>
    @endforeach
</div>