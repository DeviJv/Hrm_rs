<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadPdf extends Controller
{
    public function slip_gaji()
    {
        $data = session()->get('slip_gaji');
        return view('pdf.slip_gaji', $data);
    }
    public function surat_tugas()
    {
        $data = session()->get('surat_tugas');
        return view('pdf.surat_tugas', $data);
    }
    public function surat_paklaring()
    {
        $data = session()->get('surat_paklaring');
        return view('pdf.surat_paklaring', $data);
    }
    public function kb()
    {
        $data = session()->get('surat_kb');
        return view('pdf.surat_kerja', $data);
    }
}
