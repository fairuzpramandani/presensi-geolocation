<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi Karyawan Excel</title>
    <style>
        .title-header {
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
            vertical-align: middle;
        }
        .address-sub {
            font-family: Arial, sans-serif;
            font-size: 10px;
            font-style: italic;
            text-align: left;
            vertical-align: middle;
        }
        .label-data {
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            background-color: #ededed;
            border: 1px solid #c0c0c0;
            text-align: left;
            padding: 4px;
        }
        .value-data {
            font-family: Arial, sans-serif;
            font-size: 11px;
            border: 1px solid #c0c0c0;
            text-align: left;
            padding: 4px;
        }
        .th-header {
            border: 1px solid #000000;
            background-color: #dbdbdb;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px;
        }
        .td-body {
            border: 1px solid #000000;
            font-family: Arial, sans-serif;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
            padding: 6px 4px;
        }
        .td-left {
            border: 1px solid #000000;
            font-family: Arial, sans-serif;
            font-size: 11px;
            text-align: left;
            vertical-align: middle;
            padding: 6px 6px;
        }
        .td-ttd-lbl {
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .td-ttd-val {
            font-family: Arial, sans-serif;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <table style="border-collapse: collapse;">
        <!-- Logo and Letterhead exactly styled to match the PDF look -->
        <tr>
            <td colspan="2" rowspan="4" style="text-align: center; vertical-align: middle; border: none; padding: 10px;">
                <img src="{{ asset('assets/img/PT CMS.png') }}" width="60" height="60" alt="Logo PT CMS">
            </td>
            <td colspan="5" class="title-header" style="font-size: 15px;">LAPORAN PRESENSI KARYAWAN</td>
        </tr>
        <tr>
            <td colspan="5" class="title-header">PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td colspan="5" class="title-header">PT. CITRA MARGATAMA SURABAYA</td>
        </tr>
        <tr>
            <td colspan="5" class="address-sub">Jln. Wisata Menanggal No.21, Dukuh Menanggal, Kec. Gayungan, Surabaya, Jawa Timur 60234</td>
        </tr>

        <!-- Space rows before data cards -->
        <tr><td colspan="7" style="border: none; height: 15px;"></td></tr>

        <!-- Employee Profile Block closely structured -->
        <tr>
            <td class="label-data" style="width: 140px;">Email</td>
            <td class="value-data" colspan="6">{{ $karyawan->email }}</td>
        </tr>
        <tr>
            <td class="label-data">Nama Karyawan</td>
            <td class="value-data" colspan="6">{{ $karyawan->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label-data">Jabatan</td>
            <td class="value-data" colspan="6">{{ $karyawan->jabatan }}</td>
        </tr>
        <tr>
            <td class="label-data">Departemen</td>
            <td class="value-data" colspan="6">{{ $karyawan->nama_dept }}</td>
        </tr>
        <tr>
            <td class="label-data">No. HP / Kontak</td>
            <td class="value-data" colspan="6">{{ $karyawan->no_hp }}</td>
        </tr>

        <!-- Spacing before main details -->
        <tr><td colspan="7" style="border: none; height: 18px;"></td></tr>

        <!-- Data Grid Header -->
        <thead>
            <tr>
                <th class="th-header" style="width: 40px;">No.</th>
                <th class="th-header" style="width: 110px;">Tanggal</th>
                <th class="th-header" style="width: 100px;">Jam Masuk</th>
                <th class="th-header" style="width: 100px;">Jam Pulang</th>
                <th class="th-header" style="width: 250px;">Lokasi / GPS Koordinat</th>
                <th class="th-header" style="width: 130px;">Keterangan Hari</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presensi as $d)
            @php
                $keterangan_text = "";
                if (!empty($d->jam_in)) {
                    if ($d->jam_in > '08:00:00') {
                        $keterangan_text = "Terlambat";
                    } else {
                        $keterangan_text = "Tepat Waktu";
                    }
                } else {
                    $keterangan_text = "Tidak Absen";
                }
            @endphp
                <tr>
                    <td class="td-body">{{ $loop->iteration }}</td>
                    <td class="td-body">{{ date("d-m-Y", strtotime($d->tgl_presensi)) }}</td>
                    <td class="td-body" style="{{ $keterangan_text == 'Terlambat' ? 'color: red; font-weight: bold;' : '' }}">
                        {{ $d->jam_in }}
                    </td>
                    <td class="td-body">{{ $d->jam_out ?? '-' }}</td>
                    <td class="td-left">{{ $d->nama_lokasi_in ?? 'Luar Radius/Murni Koordinat' }}</td>
                    <td class="td-body" style="{{ $keterangan_text == 'Terlambat' ? 'color: red; font-weight: bold;' : 'color: green; font-weight: bold;' }}">
                        {{ $keterangan_text }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        <!-- Spacing before Signatures -->
        <tr><td colspan="7" style="border: none; height: 35px;"></td></tr>

        <!-- Signature Lines aligned beautifully to columns -->
        <tr>
            <td colspan="2" class="td-ttd-val" style="border: none;"></td>
            <td colspan="2" class="td-ttd-val" style="border: none;"></td>
            <td colspan="2" class="td-ttd-val" style="border: none; font-weight: bold; text-align: left; padding-left: 50px;">
                Surabaya, {{ date('d-m-Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="td-ttd-lbl" style="border: none; padding-top: 5px;">HRD</td>
            <td colspan="2" class="td-ttd-lbl" style="border: none; padding-top: 5px;">Kepala Departemen</td>
            <td colspan="2" class="td-ttd-lbl" style="border: none; padding-top: 5px; text-align: left; padding-left: 50px;">Direktur</td>
        </tr>
        <tr>
            <td colspan="2" style="border: none; height: 60px;"></td>
            <td colspan="2" style="border: none;"></td>
            <td colspan="2" style="border: none;"></td>
        </tr>
        <tr>
            <td colspan="2" class="td-ttd-val" style="border: none;">( .................................... )</td>
            <td colspan="2" class="td-ttd-val" style="border: none;">( .................................... )</td>
            <td colspan="2" class="td-ttd-val" style="border: none; text-align: left; padding-left: 50px;">( .................................... )</td>
        </tr>
    </table>

</body>
</html>
