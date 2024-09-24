<?php

namespace App\Filament\Widgets;

use App\Models\Lembur;
use App\Models\PengaturanTidakMasuk;
use App\Models\Tidak_masuk;
use Filament\Widgets\Widget;

class CutiWidget extends Widget
{
    protected static string $view = 'filament.widgets.jadwal-widget';
    protected int | string | array $columnSpan = 'full';
    protected function getViewData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $cuti = Tidak_masuk::where('keterangan', 'cuti');
        $cuti_pending = Tidak_masuk::where('keterangan', 'cuti');
        $izin = Tidak_masuk::where('keterangan', 'izin');
        $lembur = Lembur::all();
        $maximal_cuti = PengaturanTidakMasuk::where('nama', 'cuti')->first();
        $maximal_izin = PengaturanTidakMasuk::where('nama', 'izin')->first();

        if (!auth()->user()->hasRole('super_admin')) {
            $cuti = $cuti->where('karyawan_id', auth()->user()->karyawan_id)->where('status', 'approved');
            $cuti_pending = $cuti_pending->where('karyawan_id', auth()->user()->karyawan_id)->where('status', 'pending');
            $izin = $izin->where('karyawan_id', auth()->user()->karyawan_id);
            $lembur = $lembur->where('karyawan_id', auth()->user()->karyawan_id);
        }
        if ($startDate !== null && $startDate !== null) {
            $cuti->whereBetween('tgl_mulai', [$startDate, $endDate]);
            $cuti_pending->whereBetween('tgl_mulai', [$startDate, $endDate]);
            $izin->whereBetween('tgl_mulai', [$startDate, $endDate]);
            $lembur->whereBetween('tgl_lembur', [$startDate, $endDate]);
        }
        return [
            'count_jumlah_hari_cuti' => $cuti->count('jumlah_hari'),
            'count_jumlah_hari_cuti_pending' => $cuti_pending->count('jumlah_hari'),
            'count_jumlah_hari_izin' => $izin->count('jumlah_hari'),
            'jumlah_max_cuti' => $maximal_cuti->maximal,
            'jumlah_max_izin' => $maximal_izin->maximal,
            'count_jumlah_jam_lembur' => $lembur->count('jumlah_jam'),
            // 'custom_content' => 'Your content here'
        ];
    }
}