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
}
