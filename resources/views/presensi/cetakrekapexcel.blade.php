<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Karyawan</title>

    <style>
        @page {
            mso-page-orientation: landscape;
            margin: 0.2in;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; background-color: #ffffff;">

    <table style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 9px; background-color: #ffffff;">

        <tr>
            <td colspan="3" rowspan="4" style="text-align: center; vertical-align: middle; border: none;">
                <img src="{{ url('assets/img/PT CMS.png') }}" width="70" height="70" alt="Logo">
            </td>
            <td colspan="33" style="font-size: 14px; font-weight: bold; text-align: left; border: none;">REKAP PRESENSI KARYAWAN</td>
        </tr>
        <tr>
            <td colspan="33" style="font-size: 14px; font-weight: bold; text-align: left; border: none;">PERIODE {{ strtoupper($namabulan) }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td colspan="33" style="font-size: 14px; font-weight: bold; text-align: left; border: none;">PT. CITRA MARGATAMA SURABAYA</td>
        </tr>
        <tr>
            <td colspan="33" style="font-size: 10px; font-style: italic; text-align: left; border: none;">Jln. Wisata Menanggal No.21, Dukuh Menanggal, Kec. Gayungan, Surabaya, Jawa Timur 60234</td>
        </tr>

        <tr><td colspan="36" style="border: none;"></td></tr>

        <tr>
            <th rowspan="2" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 25px;">No.</th>
            <th rowspan="2" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 140px;">Email</th>
            <th rowspan="2" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 140px;">Nama Karyawan</th>
            <th colspan="31" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle;">Tanggal</th>
            <th rowspan="2" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 30px;">TH</th>
            <th rowspan="2" style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 30px;">TT</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 31; $i++)
                <th style="border: 1px solid #000000; background-color: #dbdbdb; font-weight: bold; text-align: center; vertical-align: middle; width: 30px;">{{ $i }}</th>
            @endfor
        </tr>

        @foreach ($rekap as $data)
            <tr>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; padding-left: 3px;">{{ $data->email }}</td>
                <td style="border: 1px solid #000000; text-align: left; vertical-align: middle; padding-left: 3px;">{{ $data->nama_lengkap }}</td>

                @for ($i = 1; $i <= 31; $i++)
                    @php
                        $tgl_kolom  = "tgl_" . $i;
                        $jam_string = $data->$tgl_kolom;
                        $jam_parts  = explode('-', $jam_string);
                        $jam_in     = $jam_parts[0] ?? '';
                        $jam_out    = $jam_parts[1] ?? '';
                        $batas_telat = "08:00";

                        $is_telat = false;
                        if (!empty($jam_in) && $jam_in > $batas_telat) {
                            $is_telat = true;
                        }
                    @endphp

                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; mso-number-format:'\@'; padding: 2px;">
                        @if ($is_telat)
                            <span style="color: #ff0000; font-weight: bold;">{{ $jam_in }}</span>
                        @else
                            {{ $jam_in }}
                        @endif

                        @if (!empty($jam_out) && $jam_out != '00:00')
                            <br style="mso-data-placement:same-cell;" />
                            {{ $jam_out }}
                        @endif
                    </td>
                @endfor

                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">{{ $data->total_hadir }}</td>
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold; color: #ff0000;">{{ $data->total_terlambat }}</td>
            </tr>
        @endforeach

        <tr><td colspan="36" style="border: none;"></td></tr>
        <tr><td colspan="36" style="border: none;"></td></tr>

        <tr>
            <td colspan="3" style="border: none;"></td>
            <td colspan="10" style="border: none; text-align: center; font-weight: bold; font-size: 11px;">HRD</td>
            <td colspan="10" style="border: none; text-align: center; font-weight: bold; font-size: 11px;">Kepala Departemen</td>
            <td colspan="13" style="border: none; text-align: center; font-weight: bold; font-size: 11px;">Surabaya, {{ date('d-m-Y') }} <br style="mso-data-placement:same-cell;" /> Direktur</td>
        </tr>

        <tr><td colspan="36" style="border: none; height: 60px;"></td></tr>

        <tr>
            <td colspan="3" style="border: none;"></td>
            <td colspan="10" style="border: none; text-align: center; font-size: 11px;">( . . . . . . . . . . . . . . . . . . . )</td>
            <td colspan="10" style="border: none; text-align: center; font-size: 11px;">( . . . . . . . . . . . . . . . . . . . )</td>
            <td colspan="13" style="border: none; text-align: center; font-size: 11px;">( . . . . . . . . . . . . . . . . . . . )</td>
        </tr>

    </table>
</body>
</html>
