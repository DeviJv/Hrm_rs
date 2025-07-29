<div style="font-family: Arial; font-size: 14px;">
      <strong>{{ $bidan->nama }}</strong><br>
      Pasien: {{ $bidan->pasien_count }}<br>
      Kunjungan Valid: {{ $bidan->kunjungan_valid_count }}
  
      <div style="margin-top: 8px;">
          <a  target="_blank" href="{{ route('filament.admin.resources.pasiens.create', ['bidan_mitra_id' => $bidan->id]) }}"
             style="display:inline-block;background:#d97706;color:white;padding:6px 10px;border-radius:4px;text-decoration:none;margin-top:4px;">
             + Tambah Pasien
          </a>
  
          <a  target="_blank" href="{{ route('filament.admin.resources.kunjungans.create', ['bidan_mitra_id' => $bidan->id]) }}"
             style="display:inline-block;background:#2563eb;color:white;padding:6px 10px;border-radius:4px;text-decoration:none;margin-top:4px;">
             + Tambah Kunjungan
          </a>
      </div>
  </div>