<?php

use Carbon\Carbon;
use App\Models\Perusahaan;
use App\Models\TransaksiPayroll;
use App\Http\Controllers\DownloadPdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $perusahaan = Perusahaan::first();
    $data = [
        'records' => TransaksiPayroll::all(),
        'perusahaan' => $perusahaan
    ];
    // return view('pdf.slip_gaji', $data);
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.slip_gaji', $data);
    return $pdf->stream();
    // return view('welcome');
});
Route::get('/slip_gaji/', [DownloadPdf::class, 'slip_gaji'])->name('pdf.slip_gaji');
Route::get('/surat_tugas/', [DownloadPdf::class, 'surat_tugas'])->name('pdf.surat_tugas');
