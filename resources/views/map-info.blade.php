@props(['bidan', 'url'])

<div class="custom-info-window font-sans overflow-hidden rounded-lg shadow-lg bg-white ">

  {{-- 1) Street View container --}}
 
  <div
    id="sv-{{ $bidan->id }}"
    class="street-view-container w-full"
    style="height:160px; background:#eee;"
  ></div>

  {{-- 2) Tab bar --}}
  <div class="flex items-center justify-between border-b border-gray-200 px-4">
    {{-- Nama --}}
    <h3 class="text-lg font-semibold uppercase">{{ $bidan->nama }}</h3>

    {{-- Tabs --}}
    <nav class="flex space-x-4 h-12">
      <button
        id="tab-detail-{{ $bidan->id }}"
        class="pb-2 text-sm font-medium border-b-2 border-primary-600 text-primary-600 transition-colors"
        onclick="toggleBidanTab({{ $bidan->id }}, 'detail')"
      >Detail</button>
      <button
        id="tab-kunjungan-{{ $bidan->id }}"
        class="pb-2 text-sm font-medium border-b-2 border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 transition-colors"
        onclick="toggleBidanTab({{ $bidan->id }}, 'kunjungan')"
      >Kunjungan</button>
      <button
        id="tab-pasien-{{ $bidan->id }}"
        class="pb-2 text-sm font-medium border-b-2 border-primary-600 "
        onclick="toggleBidanTab({{ $bidan->id }}, 'pasien')"
      >Pasien</button>
    </nav>
  </div>

  {{-- 3) Tab contents --}}
  <div class="px-4 pt-2 pb-4 bg-white">
    {{-- DETAIL --}}
    
    <div id="detail-{{ $bidan->id }}">
      <table class="w-full text-sm text-left text-gray-700">
        <tbody class="divide-y divide-gray-200">
          <tr><th class="py-1 pr-2 font-medium">Status</th><td class="py-1">{{ ucfirst($bidan->status_kerja_sama) }}</td></tr>
          <tr><th class="py-1 pr-2 font-medium">Telpon</th><td class="py-1">{{ $bidan->telpon }}</td></tr>
          <tr><th class="py-1 pr-2 font-medium">Alamat</th><td class="py-1">{{ $bidan->alamat }}</td></tr>
          <tr><th class="py-1 pr-2 font-medium">Kecamatan</th><td class="py-1">{{ $bidan->kecamatan }}</td></tr>
          <tr><th class="py-1 pr-2 font-medium">Kelurahan</th><td class="py-1">{{ $bidan->kelurahan }}</td></tr>
        </tbody>
      </table>
      <a href="{{ $url }}" target="_blank" class="inline-block mt-2 text-blue-600 hover:underline text-sm">
        🔍 Lihat Detail
      </a>
    </div>

    {{-- KUNJUNGAN, default hidden --}}
    <div id="kunjungan-{{ $bidan->id }}" style="display: none;">
      <table class="w-full text-sm text-left text-gray-700">
        <thead>
          <tr class="bg-gray-100">
            <th class="py-1 px-2">Nama</th>
            <th class="py-1 px-2">Tanggal</th>
            <th class="py-1 px-2">Jam</th>
            <th class="py-1 px-2">Keterangan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          {{-- contoh dummy --}}
          <tr><td class="py-1 px-2">Pasien A</td><td class="py-1 px-2">19/05/2025</td><td class="py-1 px-2">09:00</td><td class="py-1 px-2">Kontrol rutin</td></tr>
          <tr><td class="py-1 px-2">Pasien B</td><td class="py-1 px-2">18/05/2025</td><td class="py-1 px-2">14:30</td><td class="py-1 px-2">Vaksinasi</td></tr>
          <tr><td class="py-1 px-2">Pasien C</td><td class="py-1 px-2">17/05/2025</td><td class="py-1 px-2">11:15</td><td class="py-1 px-2">Konsultasi</td></tr>
        </tbody>
      </table>
    </div>
    <div id="pasien-{{ $bidan->id }}" style="display: none;">
      @livewire('list-pasien', ['bidan_id' => $bidan->id])
    </div>
  </div>
</div>