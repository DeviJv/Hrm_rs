@if ($getState() !== null)
    <div class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white underline">
        <a href="{{ asset('storage/' . $getState()) }}" target="__new">Unduh</a>
    </div>
@else
    <div>
        -
    </div>
@endif
