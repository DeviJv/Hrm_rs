<!DOCTYPE html>
<html lang="en">

<head>
    <title>Surat Paklaring</title>
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

<body class=" px-0 mx-0  text-sm antialiased h-full w-full" id="cetak">
    @foreach ($records as $record)
        <div class="flex items-start">
            {{-- <img class="h-40 w-48" src="{{ asset($perusahaan->logo) }}"> --}}
            {{-- <img class="h-[1040px] pr-5 w-screen -z-30 bg-no-repeat absolute"
                src="{{ asset('RSIA Bunda Suryatni Letterhead.png') }}"> --}}
        </div>
        <div class="text-center uppercase mt-44">
            <h2 class="underline text-xl">surat keterangan pengalaman bekerja</h2>
            <h4 class="text-lg">Nomor : {{ $record->no_surat }}</h4>
        </div>
        <div class="space-y-1.5 text-sm ml-12">
            <h3 class="mt-10 mb-4">Dengan Hormat,</h3>
            <h3 class="">Yang bertanda tangan di bawah ini :</h3>
            <div class=" flex flex-row w-auto items-start">
                <div class="w-[8%]">
                    Nama
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->nama_manager }}
                </div>
            </div>
            <div class="flex flex-row w-auto items-start">
                <div class="w-[8%]">
                    Jabatan
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->jabatan_manager }}
                </div>
            </div>
            <div class="flex flex-row w-auto items-start">
                <div class="w-[8%]">
                    Nama Perusahaan
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $perusahaan->nama }}
                </div>
            </div>
            <div class="flex flex-row w-auto items-start">
                <div class="w-[8%]">
                    Alamat
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->alamat }}
                </div>
            </div>
        </div>
        <div class="space-y-1.5 ml-12">
            <h3 class="mt-6 mb-4">Dengan ini menerangkan bahwa karyawan atas nama terlampir di bawah ini :</h3>
            <div class="flex flex-row w-auto items-start">
                <div class="w-[8%]">
                    Nama
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->nama_karyawan }}
                </div>
            </div>
            <div class="flex flex-row items-center">
                <div class="w-[8%]">
                    Unit/Jabatan
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->unit_karyawan }} / {{ $record->jabatan_karyawan }}
                </div>
            </div>
            <div class="flex flex-row items-center">
                <div class="w-[8%]">
                    Alamat
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ $record->alamat_karyawan }}
                </div>
            </div>
            <div class="flex flex-row items-center">
                <div class="w-[8%]">
                    Tanggal Masuk
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ date('d/m/Y', strtotime($record->tgl_masuk)) }}
                </div>
            </div>
            <div class="flex flex-row items-center">
                <div class="w-[8%]">
                    Tanggal Keluar
                </div>
                <div class="w-24">
                    :
                </div>
                <div class="w-1/2">
                    {{ date('d/m/Y', strtotime($record->tgl_keluar)) }}
                </div>
            </div>
        </div>

        <p class="w-full mt-3 text-sm ml-12">
            Yang bersangkutan telah memberikan kontribusi yang baik kepada perusahaan selama bekerja <br />
        </p>
        <p class="text-sm ml-12">
            tidak pernah melakukan perbuatan yang merugikan perusahaan kami, Demikian surat keterangan <br />
        </p>
        <p class="text-sm mb-3 ml-12">
            bekerja ini dibuat agar dapat dipergunakan sebagaimana mestinya. <br />
        </p>

        <div class="flex flex-row items-end justify-end  mt-5 break-after-page ml-12">

            <div class="w-1/2">
                Bogor, {{ $today = \Carbon\Carbon::parse($record->created_at)->isoFormat('D MMMM Y') }}<br />
                Hormat kami,
                @if ($record->stemple)
                    <img class="h-24 w-28" src="{{ asset('storage/' . $perusahaan->stample) }}">
                    <span class="underline">{{ '( ' . $record->nama_manager . ' ) ' }}</span><br />
                    {{ $record->jabatan_manager }}
                @else
                    <div class="mt-16 ">
                        {{ '( ' . $record->nama_manager . ' ) ' }}
                        <br />
                        {{ $record->jabatan_manager }}
                    </div>
                @endif
            </div>
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
            unit: 'cm',
            format: 'a4',
            putOnlyUsedFonts: true,
        }
    };
    window.onload = (event) => {
        html2pdf().set(opt).from(element).save(
            'surat-paklaring.pdf'
        );
    };
</script>

</html>
