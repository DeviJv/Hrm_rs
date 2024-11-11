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

        $records = $this->records;

        $sum_jumlah_jam = $records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('jumlah_jam');
            });
        $sum_jumlah_real = $records
            ->map(function ($item, $k) {

                $dari = date_create('' . $item['tgl_lembur'] . '' . $item['jm_mulai'] . '');
                $sampai = date_create('' . $item['tgl_lembur'] . '' . $item['jm_selesai'] . '');
                $hitung = date_diff($dari, $sampai);
                $item['jj'] = $hitung->h;
                return $item;
            });
        $sum_jumlah_jam_real = $sum_jumlah_real->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('jj');
            });

        $harga_jam_pertama = $records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('harga_jam_pertama');
            });
        $harga_total_jam = $records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('harga_total_jam');
            });
        $total_lembur = $records->groupBy('karyawan_id')
            ->map(function ($item) {
                return $item->sum('total_lembur');
            });

        return view('exports.lembur', [
            'lembur' =>  $records,
            'sum_jumlah_jam' => $sum_jumlah_jam,
            'harga_total_jam' => $harga_total_jam,
            'harga_jam_pertama' => $harga_jam_pertama,
            'total_lembur' => $total_lembur,
            'jumlah_jam_real' => $sum_jumlah_jam_real
        ]);
    }
}
