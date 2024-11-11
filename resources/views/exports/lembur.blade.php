<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Lembur</th>
            <th>Jumlah Jam</th>
            <th>Setelah Pengurangan</th>
            <th>Nama</th>
            <th>Harga Lembur</th>
            <th>Harga Perjam</th>
            <th>Harga Jam Pertama</th>
            <th>Harga Total Jam</th>
            <th>Total Lembur</th>
        </tr>
    </thead>
    <tbody>
        <?php $sum = [];
        $hitung_array = []; ?>
        @foreach ($lembur as $k => $l)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ date('d F,Y', strtotime($l->tgl_lembur)) }}</td>
                <?php
                $dari = date_create('' . $l->tgl_lembur . '' . $l->jm_mulai . '');
                $sampai = date_create('' . $l->tgl_lembur . '' . $l->jm_selesai . '');
                $hitung = date_diff($dari, $sampai);
                $sum[] = $hitung->h;
                ?>
                <td>{{ $hitung->h }}</td>
                <td>{{ $l->jumlah_jam }}</td>
                <td>{{ $l->karyawan->nama }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_lembur, 0, ',', '.') }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_perjam, 0, ',', '.') }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_jam_pertama, 0, ',', '.') }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_total_jam, 0, ',', '.') }}</td>
                <td>{{ 'Rp ' . number_format($l->total_lembur, 0, ',', '.') }}</td>
            </tr>
            @if (!$loop->last)
                @if ($lembur[$k + 1]->karyawan_id !== $l->karyawan_id)
                    <tr style="background-color: green;">
                        <td colspan=2>Total : </td>

                        <td>
                            {{ $jumlah_jam_real[$l->karyawan_id] }}
                        </td>
                        <td>{{ $sum_jumlah_jam[$l->karyawan_id] }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><b>{{ 'Rp' . number_format($harga_jam_pertama[$l->karyawan_id], 0, ',', '.') }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($harga_total_jam[$l->karyawan_id], 0, ',', '.') }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($total_lembur[$l->karyawan_id], 0, ',', '.') }}</b></td>
                    </tr>
                @endif
            @else
                @if ($lembur[$k]->karyawan_id === $l->karyawan_id)
                    <tr style="background-color: green;">
                        <td colspan=2>Total : </td>
                        <td>
                            {{ $jumlah_jam_real[$l->karyawan_id] }}
                        </td>
                        <td>{{ $sum_jumlah_jam[$l->karyawan_id] }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><b>{{ 'Rp' . number_format($harga_jam_pertama[$l->karyawan_id], 0, ',', '.') }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($harga_total_jam[$l->karyawan_id], 0, ',', '.') }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($total_lembur[$l->karyawan_id], 0, ',', '.') }}</b></td>
                    </tr>
                @endif
            @endif
        @endforeach
    </tbody>
</table>
