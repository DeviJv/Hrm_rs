<x-filament::modal id="custom-bidan-modal" max-width="2xl">
    @if ($bidan)
        <x-filament::card>
            <h2 class="text-xl font-bold">{{ $bidan->nama }}</h2>
            <p><strong>Alamat:</strong> {{ $bidan->alamat }}</p>
            <p><strong>Status:</strong> {{ $bidan->status_kerja_sama }}</p>
            <p><strong>Telp:</strong> {{ $bidan->telpon }}</p>
            <p><strong>Kecamatan:</strong> {{ $bidan->kecamatan }}</p>
            <p><strong>Kelurahan:</strong> {{ $bidan->kelurahan }}</p>
        </x-filament::card>
    @else
        <p class="text-red-500">Data tidak ditemukan.</p>
    @endif
</x-filament::modal>