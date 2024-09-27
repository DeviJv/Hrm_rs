<?php
function terbilang($x)
{
    $angka = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    if ($x < 12) {
        return ' ' . $angka[$x];
    } elseif ($x < 20) {
        return terbilang($x - 10) . ' belas';
    } elseif ($x < 100) {
        return terbilang($x / 10) . ' puluh' . terbilang($x % 10);
    } elseif ($x < 200) {
        return 'seratus' . terbilang($x - 100);
    } elseif ($x < 1000) {
        return terbilang($x / 100) . ' ratus' . terbilang($x % 100);
    } elseif ($x < 2000) {
        return 'seribu' . terbilang($x - 1000);
    } elseif ($x < 1000000) {
        return terbilang($x / 1000) . ' ribu' . terbilang($x % 1000);
    } elseif ($x < 1000000000) {
        return terbilang($x / 1000000) . ' juta' . terbilang($x % 1000000);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Slip Gaji</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
        integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('fonts/Inter.ttf') }}') format('truetype');
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body class="mx-auto container mt-10 text-xs antialiased" id="cetak">
    @foreach ($records as $record)
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="width: 50%;">
                        <center><img width="250" height="220"
                                src="data:image/png;base64,{{ base64_encode(file_get_contents(asset($perusahaan->logo))) }}">
                        </center>
                    </td>
                    <td style="width: 50%;">
                        <center>
                            <h2 class="text-4xl">SLIP GAJI KARYAWAN</h2>
                        </center>
                    </td>
                </tr>
                <tr class="text-sm">
                    <td class="h-9">
                        <div class="flex flex-row items-center mx-auto px-4 container w-full">
                            <div class="w-1/3 font-semibold">Nama</div>
                            <div class="">:</div>
                            <div class="w-full">{{ $record->karyawan->nama }}</div>
                        </div>
                    </td>
                    <td class="h-9">
                        <div class="flex flex-row items-center mx-auto px-4 container w-full">
                            <div class="w-1/3 font-semibold ">Perusahaan</div>
                            <div class="">:</div>
                            <div class="w-full">{{ $perusahaan->nama }}</div>
                        </div>
                    </td>
                </tr>
                <tr class="text-sm">
                    <td class="h-9">
                        <div class="flex flex-row items-center mx-auto px-4 container w-full">
                            <div class="w-1/3 font-semibold">Kode Pegawai</div>
                            <div class="">: </div>
                            <div class="w-full">{{ $record->karyawan->nik }}</div>
                        </div>
                    </td>
                    <td class="h-9">
                        <div class="flex flex-row items-center mx-auto px-4 container w-full">
                            <div class="w-1/3 font-semibold">Periode</div>
                            <div class="">: </div>
                            <div class="w-full">{{ date('F-Y', strtotime($record->created_at)) }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-10 text-center font-semibold">PENDAPATAN KARYAWAN</td>
                </tr>
                <tr class="uppercase text-xs">
                    <td class="content-start pt-5">
                        <h3 class="text-center underline text-lg font-semibold items-start">PENDAPATAN</h3>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/5 font-semibold">Gaji Poko</div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->gaji_pokok) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/5 font-semibold">Transport </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->transport) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/5 font-semibold">Makan </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->makan) }}</div>
                        </div>
                    </td>
                    <td>
                        <h3 class="text-center underline text-lg font-semibold items-start">tunjangan</h3>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">penyesuaian</div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->penyesuaian) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">INSENTIF </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->insentif) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">fungsional Umum</div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->fungsional) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">fungsional Khusus</div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->fungsional_it) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">jabatan</div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->jabatan) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">lembur </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->lembur) }}</div>
                        </div>
                    </td>
                </tr>
                <tr class="uppercase">
                    <td class="h-10 text-center font-semibold">
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/3 font-semibold">total pendapatan</div>
                            <div class="w-1/3 text-right"> Rp
                                {{ number_format($record->gaji_pokok + $record->transport + $record->makan) }}</div>
                        </div>
                    </td>
                    <td class="h-10 text-center font-semibold">
                        <div class="flex flex-row items-center justify-between px-4 container w-full">
                            <div class="w-1/2 font-semibold">total pendapatan tunjangan</div>
                            <div class="w-1/3 text-right"> Rp
                                {{ number_format($record->penyesuaian + $record->insentif + $record->fungsional + $record->lembur + $record->fungsional_it + $record->jabatan) }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-12 text-center font-semibold bg-gray-200">
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/3 font-semibold">TOTAL PENDAPATAN DITERIMA KARYAWAN</div>
                            <div class="w-1/3 text-right"> Rp
                                {{ number_format($record->penyesuaian + $record->insentif + $record->fungsional + $record->lembur + $record->gaji_pokok + $record->transport + $record->makan + $record->fungsional_it + $record->jabatan) }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-10 text-center font-semibold uppercase">
                        <h3 class="text-center  text-lg font-semibold">kewajibatan karyawan</h3>
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/3 font-semibold">Iuran BPJS Ketenagakerjaan </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->bpjs_kesehatan) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/3 font-semibold">Iuran BPJS Kesehatan </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->ketenagakerjaan) }}</div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/3 font-semibold">Catering / Pembelian Obat / Lain-lain/Admin Beda Bank
                            </div>
                            <div class="w-1/2 text-right">
                                @if ($record->payment_method == 'transfer_non_bri')
                                    Rp {{ number_format($record->piutang + 2900) }}
                                @else
                                    Rp {{ number_format($record->piutang) }}
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/3 font-semibold">Tidak Masuk/Izin
                                {{ collect($record->karyawan->tidak_masuks)->sum('jumlah_hari') }}
                                (Hari)
                            </div>
                            <div class="w-1/2 text-right"> Rp {{ number_format($record->tidak_masuk) }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-10 text-center font-semibold bg-gray-200">
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/2 font-semibold">TOTAL KEWAJIBAN KARYAWAN</div>
                            <div class="w-1/3 text-right"> Rp
                                {{ number_format($record->tidak_masuk + $record->piutang + $record->ketenagakerjaan + $record->bpjs_kesehatan) }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-7"></td>
                </tr>
                <tr>
                    <td colspan="2" class="h-10 text-center font-semibold bg-gray-200">
                        <div class="flex flex-row items-center justify-between px-8 container w-full">
                            <div class="w-1/2 font-semibold">TOTAL PEMBAYARAN DITERIMA KARYAWAN</div>
                            <div class="w-1/3 text-right"> Rp
                                <?php $total = $record->penyesuaian + $record->insentif + $record->fungsional + $record->lembur + $record->gaji_pokok + $record->transport + $record->makan + $record->fungsional_it + $record->jabatan - $record->tidak_masuk - $record->piutang - $record->ketenagakerjaan - $record->bpjs_kesehatan;
                                ?>
                                {{ number_format($total) }}

                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="h-7 bg-gray-200 text-center font-bold italic">
                        "{{ ucwords(terbilang($total)) }}"
                    </td>
                </tr>
                <tr class="text-center text-sm">
                    <td>
                        <div class="h-36">
                            Penerima
                        </div>
                        <div class="border border-t border-black ">
                            {{ $record->karyawan->nama }}
                        </div>
                    </td>
                    <td>
                        <div class="h-36">
                            Mengetahui
                        </div>
                        <div class="border border-t border-black ">
                            &nbsp;
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="page-break mb-5"> <!-- Page 1 content: your table -->
            &nbsp;

        </div>
    @endforeach

</body>
<script>
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    var element = document.getElementById('cetak');
    var opt = {
        margin: 1,
        // filename:     'myfile.pdf',
        image: {
            type: 'jpeg',
            quality: 0.98
        },
        html2canvas: {
            scale: 2
        },
        jsPDF: {
            orientation: 'p',
            unit: 'mm',
            format: 'a4',
            putOnlyUsedFonts: true,
        }
    };
    window.onload = (event) => {
        html2pdf().set(opt).from(element).save(
            'Slip-gaji.pdf'
        );
    };
</script>

</html>
