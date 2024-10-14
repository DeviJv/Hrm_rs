<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Lembur</th>
            <th>Jumlah Jam</th>
            <th>Nama</th>
            <th>Harga Lembur</th>
            <th>Harga Perjam</th>
            <th>Harga Jam Pertama</th>
            <th>Harga Total Jam</th>
            <th>Total Lembur</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lembur as $k => $l)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ date('d F,Y', strtotime($l->tgl_lembur)) }}</td>
                <td>{{ $l->jumlah_jam }}</td>
                <td>{{ $l->karyawan->nama }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_lembur) }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_perjam) }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_jam_pertama) }}</td>
                <td>{{ 'Rp ' . number_format($l->harga_total_jam) }}</td>
                <td>{{ 'Rp ' . number_format($l->total_lembur) }}</td>
            </tr>
            @if (!$loop->last)
                @if ($lembur[$k + 1]->karyawan_id !== $l->karyawan_id)
                    <tr style="background-color: green;">
                        <td colspan=2>Total : </td>
                        <td>{{ $sum_jumlah_jam[$l->karyawan_id] }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><b>{{ 'Rp' . number_format($harga_jam_pertama[$l->karyawan_id]) }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($harga_total_jam[$l->karyawan_id]) }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($total_lembur[$l->karyawan_id]) }}</b></td>
                    </tr>
                @endif
            @else
                @if ($lembur[$k]->karyawan_id === $l->karyawan_id)
                    <tr style="background-color: green;">
                        <td colspan=2>Total : </td>
                        <td>{{ $sum_jumlah_jam[$l->karyawan_id] }}</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td><b>{{ 'Rp' . number_format($harga_jam_pertama[$l->karyawan_id]) }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($harga_total_jam[$l->karyawan_id]) }}</b></td>
                        <td><b>{{ 'Rp ' . number_format($total_lembur[$l->karyawan_id]) }}</b></td>
                    </tr>
                @endif
            @endif
        @endforeach
    </tbody>
</table>
