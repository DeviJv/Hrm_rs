<!DOCTYPE html>
<html lang="en">

<head>
    <title>Surat Tugas</title>
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

<body>
    <div class=" px-0 mx-0  mt-1 text-sm antialiased h-full w-full" id="cetak">
        @foreach ($records as $record)
            <div class="flex items-start">
                {{-- <img class="h-40 w-48" src="{{ asset($perusahaan->logo) }}"> --}}
                {{-- <img class="h-[1040px] w-[1340px] pr-5 -z-30 bg-no-repeat absolute"
                    src="{{ asset('RSIA Bunda Suryatni Letterhead.png') }}"> --}}
            </div>
            <div class="text-center uppercase mt-44">
                <h2 class="underline text-2xl">surat tugas</h2>
                <h4 class="text-lg">Nomor : {{ $record->no_surat }}</h4>
            </div>
            <div class="space-y-1.5 ml-12">
                <h3 class="mt-10">Yang bertanda tangan di bawah ini :</h3>
                <div class="flex flex-row w-auto text-left justify-start">
                    <div class="w-[4%]">
                        Nama
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->nama_direktur }}
                    </div>
                </div>
                <div class="flex flex-row w-auto items-start">
                    <div class="w-[4%]">
                        Jabatan
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->jabatan_direktur }}
                    </div>
                </div>
                <div class="flex flex-row w-auto items-start">
                    <div class="w-[4%]">
                        Alamat
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->alamat_direktur }}
                    </div>
                </div>
            </div>
            <h3 class="mt-6 ml-12">Dengan ini menerangkan bahwa nama-nama dibawah ini :</h3>

            @if (count($record->karyawans) > 1)
                <table class="table table-auto w-[630px] mx-12 mt-3 text-center text-sm">
                    <thead>
                        <tr class="text-sm">
                            <th class="text-sm">No</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Jabatan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($record->karyawans as $k => $karyawan)
                            <tr class="text-sm">
                                <td>{{ $k + 1 }}</td>
                                <td>{{ $karyawan['nama_karyawan'] }}</td>
                                <td>{{ $karyawan['nik_karyawan'] }}</td>
                                <td>{{ $karyawan['jabatan_karyawan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="flex flex-row  items-center ml-12 mb-2 mt-2">
                    <div class="w-[4%]">
                        Nama
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->karyawans[0]['nama_karyawan'] }}
                    </div>
                </div>
                <div class="flex flex-row items-center ml-12 mb-2">
                    <div class="w-[4%]">
                        NIK
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->karyawans[0]['nik_karyawan'] }}
                    </div>
                </div>
                <div class="flex flex-row items-center ml-12">
                    <div class="w-[4%]">
                        Jabatan
                    </div>
                    <div class="w-28">
                        :
                    </div>
                    <div class="w-1/2">
                        {{ $record->karyawans[0]['jabatan_karyawan'] }}
                    </div>
                </div>
            @endif
            <p class="w-full mt-3 ml-12">
                Adalah benar karyawan {{ $perusahaan->nama }} yang kami tugaskan untuk <br />
            </p>
            <p class=" mb-3 ml-12">
                <span class="font-semibold italic">{{ $record->tugas }}</span> yang dilaksakan pada :
            </p>
            <div class="flex flex-row items-center ml-12">
                <div class="w-[6%]">
                    Hari
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    @if ($record->tgl_akhir != null)
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('dddd') }} s/d
                        {{ $today = \Carbon\Carbon::parse($record->tgl_akhir)->isoFormat('dddd') }}
                    @else
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('dddd') }}
                    @endif
                </div>
            </div>
            <div class="flex flex-row items-center ml-12">
                <div class="w-[6%]">
                    Tanggal
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    @if ($record->tgl_akhir != null)
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('D MMMM Y') }} s/d
                        {{ $today = \Carbon\Carbon::parse($record->tgl_akhir)->isoFormat('D MMMM Y') }}
                    @else
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('D MMMM Y') }}
                    @endif
                </div>
            </div>
            <div class="flex flex-row items-center ml-12">
                <div class="w-[6%]">
                    Pukul
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    @if ($record->tgl_akhir != null)
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('H:mm') }} s/d
                        {{ $today = \Carbon\Carbon::parse($record->tgl_akhir)->isoFormat('H:mm') }} WIB
                    @else
                        {{ $today = \Carbon\Carbon::parse($record->tgl_tugas)->isoFormat('H:mm') }} WIB s/d Selesai
                    @endif
                </div>
            </div>
            <div class="flex flex-row items-center ml-12">
                <div class="w-[6%]">
                    Tempat
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->tempat }}
                </div>
            </div>
            <p class="mt-2 ml-12">Demikian surat tugas ini dibuat untuk dapat dipergunakan sebagaimana<br />
            </p>
            <p class="ml-12">mestinya, atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
            <div class="flex flex-row items-start justify-between mt-5 break-after-page ml-12">
                <div class="w-1/2">
                    Diterima oleh,<br />
                    Pejabat yang berwenang
                    <div class="mt-20">
                        (.....................................)
                        <br />
                        Nip :
                    </div>
                </div>
                <div class="w-1/2">
                    Bogor, {{ $today = \Carbon\Carbon::parse($record->created_at)->isoFormat('D MMMM Y') }}<br />
                    @if ($record->stemple)
                        <img class="h-24 w-28" src="{{ asset('storage/' . $perusahaan->stample) }}">
                        <span class="underline">{{ '( ' . $record->nama_direktur . ' ) ' }}</span><br />
                        {{ $record->jabatan_direktur }}
                    @else
                        <div class="mt-[77px]">
                            {{ '( ' . $record->nama_direktur . ' ) ' }}
                            <br />
                            {{ $record->jabatan_direktur }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
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
            unit: 'cm',
            format: 'a4',
            putOnlyUsedFonts: true,
        }
    };
    window.onload = (event) => {
        html2pdf().set(opt).from(element).save(
            'surat-tugas.pdf'
        );
    };
</script>

</html>
