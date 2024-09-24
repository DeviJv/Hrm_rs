<?php

use App\Http\Controllers\CutiAction;
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
Route::get('/approve/cuti/{tidak_masuk}', [CutiAction::class, 'approve'])->name('cuti.approve');
Route::get('/approve/cuti_force/{tidak_masuk}', [CutiAction::class, 'approve_force'])->name('cuti.approve.force');
Route::get('/decline/cuti/{tidak_masuk}', [CutiAction::class, 'decline'])->name('cuti.decline');
Route::get('/decline/cuti_force/{tidak_masuk}', [CutiAction::class, 'decline_force'])->name('cuti.decline.force');