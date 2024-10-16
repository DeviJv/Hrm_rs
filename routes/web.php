<?php

use App\Http\Controllers\CutiAction;
use Carbon\Carbon;
use App\Models\Perusahaan;
use App\Models\TransaksiPayroll;
use App\Http\Controllers\DownloadPdf;
use App\Http\Controllers\LemburAction;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {

//     $perusahaan = Perusahaan::first();
//     $data = [
//         'records' => TransaksiPayroll::all(),
//         'perusahaan' => $perusahaan
//     ];
//     // return view('pdf.slip_gaji', $data);
//     $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.slip_gaji', $data);
//     return $pdf->stream();
//     // return view('welcome');
// });
Route::get('/slip_gaji/', [DownloadPdf::class, 'slip_gaji'])->name('pdf.slip_gaji');
Route::get('/surat_tugas/', [DownloadPdf::class, 'surat_tugas'])->name('pdf.surat_tugas');
Route::get('/surat_paklaring/', [DownloadPdf::class, 'surat_paklaring'])->name('pdf.surat_paklaring');
Route::get('/kb/', [DownloadPdf::class, 'kb'])->name('pdf.kb');
Route::get('/approve/cuti/{tidak_masuk}', [CutiAction::class, 'approve'])->name('cuti.approve');
Route::get('/approve/cuti_force/{tidak_masuk}', [CutiAction::class, 'approve_force'])->name('cuti.approve.force');
Route::get('/decline/cuti/{tidak_masuk}', [CutiAction::class, 'decline'])->name('cuti.decline');
Route::get('/decline/cuti_force/{tidak_masuk}', [CutiAction::class, 'decline_force'])->name('cuti.decline.force');

Route::get('/approve/lembur/{lembur}', [LemburAction::class, 'approve'])->name('lembur.approve');
Route::get('/approve/lembur_force/{lembur}', [LemburAction::class, 'approve_force'])->name('lembur.approve.force');
Route::get('/decline/lembur/{lembur}', [LemburAction::class, 'decline'])->name('lembur.decline');
Route::get('/decline/lembur_force/{lembur}', [LemburAction::class, 'decline_force'])->name('lembur.decline.force');