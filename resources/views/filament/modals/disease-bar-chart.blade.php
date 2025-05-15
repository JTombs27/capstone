<div class="col-span-12">
    <h1>Top 3 Disease Matches</h1>

    @if (!empty($details) && count($details) > 0)
        <ul>
            @foreach ($details as $detail)
                <li>{{ $detail['name'] }} - {{ $detail['percentage'] }}</li>
            @endforeach
        </ul>
    @else
        <p>No matching diseases found.</p>
    @endif
</div>
