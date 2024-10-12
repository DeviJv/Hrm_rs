<?php

namespace App\Exports;

use App\Models\Lembur;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class LemburExport implements FromView
{

    public $records;

    public function __construct($records = null)
    {
        $this->records = $records;
    }

    public function view(): View
    {
        $sum_jumlah_jam = $this->records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('jumlah_jam');
            });
        $harga_jam_pertama = $this->records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('harga_jam_pertama');
            });
        $harga_total_jam = $this->records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('harga_total_jam');
            });
        $total_lembur = $this->records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('total_lembur');
            });


        return view('exports.lembur', [
            'lembur' => $this->records ? $this->records : Lembur::with('karyawan')->orderBy('karyawan_id', 'asc')->get(),
            'sum_jumlah_jam' => $sum_jumlah_jam,
            'harga_total_jam' => $harga_total_jam,
            'harga_jam_pertama' => $harga_jam_pertama,
            'total_lembur' => $total_lembur,
        ]);
    }
}