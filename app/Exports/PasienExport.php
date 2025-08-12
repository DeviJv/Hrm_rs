<?php

namespace App\Exports;

use Throwable;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PenjualanItem;
use Illuminate\Bus\Queueable;
use App\Models\ReturPenjualanItem;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PasienExport implements FromCollection, WithMapping,  WithTitle, WithHeadings, WithEvents {

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
            'BIDAN MITRA',
            'NAMA',
            'USIA',
            'TINDAKAN',
            'OPERASI',
            'TELPON',
            'KELAS',
            'JENIS',
            'STATUS',
            'FEE',
            'JAMINAN',
            'KETERANGAN',
        ];
    }

    public function map($row): array {
        $data =  [
            $row->created_at->format('d/m/y'),
            $row->bidanMitra->nama,
            $row->nama,
            $row->usia,
            $row->tindakan?->nama ?? "-",
            $row->operasi,
            $row->no_tlp,
            $row->kelas,
            $row->jenis,
            $row->status,
            $row->fee,
            $row->jaminan,
            $row->keterangan,
        ];
        return $data;
    }
    public function title(): string {
        return 'BidanExport';
    }

    public function registerEvents(): array {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Hitung jumlah baris data + heading
                $rowCount = $this->selected->count() + 2; // +1 heading, +1 row data start dari 2
                $totalFee = $this->selected->sum('fee');

                // Tulis label TOTAL di kolom KETERANGAN (misalnya kolom K)
                $sheet->setCellValue('J' . $rowCount, 'TOTAL');

                // Tulis total fee di kolom L (fee)
                $sheet->setCellValue('K' . $rowCount, $totalFee);

                // Bold baris total
                $sheet->getStyle('J' . $rowCount . ':K' . $rowCount)->getFont()->setBold(true);
            }
        ];
    }
}
