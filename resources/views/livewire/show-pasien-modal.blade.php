<x-filament::modal id="kunjunganModal" width="4xl" heading="📋 Daftar {{ ucfirst($type) }}">
    @if ($bidanId)
        @if ($type === 'kunjungan')
            @livewire('list-kujungan', ['bidan_id' => $bidanId], key('list-kujungan-'.$bidanId))
        @elseif ($type === 'pasien')
            @livewire('list-pasien', ['bidan_id' => $bidanId], key('list-pasien-'.$bidanId))
        @endif
    @else
        <div class="text-sm text-gray-600">Silakan pilih bidan terlebih dahulu.</div>
    @endif
</x-filament::modal>