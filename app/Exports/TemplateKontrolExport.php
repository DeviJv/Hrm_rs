<?php

namespace App\Exports;

use Throwable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PenjualanItem;
use App\Models\ReturPenjualanItem;
use Illuminate\Bus\Queueable;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateKontrolExport implements FromCollection, WithMapping,  WithTitle, WithHeadings {

    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct(public array|null|Collection $selected = null,) {
        $this->selected = $selected;
    }


    public function collection() {
        return $this->selected;
    }

    // public function sheets(): array
    // {
    //     $sheets = [];
    //     $sheets[0] = new ProduksiExport();
    //     $sheets[1] = new MaterialSheet();
    //     $sheets[2] = new HasilSheet();

    //     return $sheets;
    // }

    public function headings(): array {
        return [
            'TANGGAL DIBUAT',
            'TANGGAL KONTROL',
            'NAMA',
            'NO RM',
            'STATUS',
            'JK',
            'UMUR',
            'ALAMAT',
            'NO HP',
            'DIAGNOSA',
            'TINDAKAN',
            'HPL',
            'PENJAMIN',
            'KETERANGAN',
        ];
    }

    public function map($row): array {
        $data =  [
            $row->created_at->format('d/m/y'),
            $row->tgl_kontrol->format('d/m/y'),
            $row->nama,
            $row->no_rm,
            $row->status,
            $row->jk,
            $row->umur,
            $row->alamat,
            $row->no_hp,
            $row->diagnosa,
            $row->tindakan,
            $row->hpl,
            $row->penjamin,
            $row->keterangan,
        ];
        return $data;
    }
    public function title(): string {
        return 'BidanExport';
    }
}