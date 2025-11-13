<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>A4</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <style>
    @page { size: A4 landscape }

    #title{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }
    .tabeldatakaryawan{
        margin-top: 40px;
    }
    .tabeldatakaryawan tr td {
        padding: 5px;
    }
    .tabelpresensi{
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }
    .tabelpresensi thead {
        display: table-header-group;
    }
    .tabelpresensi tr {
        page-break-inside: avoid;
    }
    .tabelpresensi tr th{
        border: 1px solid #131212;
        padding: 4px;
        background: #dbdbdb;
        font-size: 10px;
        text-align: center;
    }
    .tabelpresensi tr td{
        border: 1px solid #131212;
        padding: 3px;
        font-size: 9px;
        vertical-align: middle;
        text-align: center;
    }
    .text-danger {
        color: red;
        font-weight: bold;
    }
    .foto{
        width: 35px;
        height: 35px;
        object-fit: cover;
    }
    .tabelttd {
        margin-top: 50px;
        width: 100%;
    }
    .tabelttd td {
        text-align: center;
        padding: 25px;
        font-size: 12px;
    }
    .spasi_ttd {
        height: 100px;
    }
  </style>
</head>

<body class="A4 landscape">

  <section class="sheet padding-10mm">
   <table style="width: 100%">
        <tr>
            <td style="width: 100px; vertical-align: top;">
                <img src="{{ asset('assets/img/PT CMS.png') }}" width="80" height="80" alt="">
            </td>
            <td style="vertical-align: top;">
                <span id="title" style="margin: 0; line-height: 1.4;">
                    REKAP PRESENSI KARYAWAN<br>
                    PERIODE {{ strtoupper($namabulan) }} {{ $tahun }} <br>
                    PT. CITRA MARGATAMA SURABAYA<br>
                </span>
                <span><i>Jln. Wisata Menanggal No.21, Dukuh Menanggal, Kec. Gayungan,<br>Surabaya, Jawa Timur 60234</i></span>
            </td>
        </tr>
    </table>

    <table class="tabelpresensi">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Email</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="31">Tanggal</th>

                <th rowspan="2">TH</th> <th rowspan="2">TT</th> </tr>
            <tr>
                @for ($i = 1; $i <= 31; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->email }}</td>
                    <td>{{ $data->nama_lengkap }}</td>

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

                        <td>
                            @if ($is_telat)
                                <span class="text-danger">{{ $jam_in }}</span>
                            @else
                                {{ $jam_in }}
                            @endif

                            @if (!empty($jam_out) && $jam_out != '00:00')
                                <br>
                                {{ $jam_out }}
                            @endif
                        </td>
                    @endfor

                    <td>{{ $data->total_hadir }}</td>
                    <td>{{ $data->total_terlambat }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <table class="tabelttd">
        <tr>
            <td style="width: 33%;"></td>
            <td style="width: 33%;"></td>
            <td style="width: 33%;">
                Surabaya, {{ date('d-m-Y') }}
            </td>
        </tr>
        <tr>
            <td>
                HRD
                <div class="spasi_ttd"></div>
                ( . . . . . . . . . . . . . . . . . . . . . . )
            </td>
            <td>
                Kepala Departemen
                <div class="spasi_ttd"></div>
                ( . . . . . . . . . . . . . . . . . . . . . . )
            </td>
            <td>
                Direktur
                <div class="spasi_ttd"></div>
                ( . . . . . . . . . . . . . . . . . . . . . . )
            </td>
        </tr>
    </table>

  </section>

</body>
</html>
