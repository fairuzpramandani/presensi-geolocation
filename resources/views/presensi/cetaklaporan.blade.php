<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Laporan Presensi</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

  <style>
    @page { size: A4 }
    body {
        font-family: Arial, Helvetica, sans-serif;
    }
    #title{
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
    .tabelpresensi tr th{
        border: 1px solid #131212;
        padding: 6px;
        background: #dbdbdb;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
    }
    .tabelpresensi tr td{
        border: 1px solid #131212;
        padding: 4px;
        font-size: 12px;
        vertical-align: middle;
        text-align: center;
    }
    .tabelpresensi tr {
        page-break-inside: avoid;
    }
    .foto{
        width: 40px;
        height: 40px;
        object-fit: cover;
        border: 1px solid #ddd;
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
        height: 80px;
    }
  </style>
</head>

<body class="A4">

  <section class="sheet padding-10mm">
    <table style="width: 100%">
        <tr>
            <td style="width: 100px; text-align:center">
                <img src="{{ asset('assets/img/PT CMS.png') }}" width="80" height="80" alt="">
            </td>
            <td style="text-align: left;">
                <span id="title" style="line-height: 1.4;">
                    LAPORAN PRESENSI KARYAWAN<br>
                    PERIODE {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }} <br>
                    PT. CITRA MARGATAMA SURABAYA<br>
                </span>
                <span style="font-size: 12px"><i>Jln. Wisata Menanggal No.21, Dukuh Menanggal, Kec. Gayungan,<br>Surabaya, Jawa Timur 60234</i></span>
            </td>
        </tr>
    </table>

    <table class="tabeldatakaryawan" style="width: 100%">
        <tr>
            <td style="width: 120px;">Email</td>
            <td style="width: 10px;">:</td>
            <td style="text-align: left;">{{ $karyawan->email}}</td>
        </tr>
        <tr>
            <td>Nama Karyawan</td>
            <td>:</td>
            <td style="text-align: left;">{{ $karyawan->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td style="text-align: left;">{{ $karyawan->jabatan }}</td>
        </tr>
        <tr>
            <td>Departemen</td>
            <td>:</td>
            <td style="text-align: left;">{{ $karyawan->nama_dept }}</td>
        </tr>
        <tr>
            <td>No. Hp</td>
            <td>:</td>
            <td style="text-align: left;">{{ $karyawan->no_hp }}</td>
        </tr>
    </table>

    <table class="tabelpresensi">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Lokasi</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($presensi as $d)
            @php
                $path_in = asset('storage/uploads/absen/'.$d->foto_in);
                $path_out = asset('storage/uploads/absen/'.$d->foto_out);
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
                <td>{{ $loop->iteration }}</td>
                <td>{{ date("d-m-Y",strtotime($d->tgl_presensi)) }}</td>
                <td>{{ $d->jam_in }}</td>
                <td>
                    @if (!empty($d->foto_in))
                        <img src="{{ $path_in }}" class="foto" alt="In" onerror="this.onerror=null;this.src='{{ asset('assets/img/camera.jpg') }}';">
                    @else
                        <img src="{{ asset('assets/img/camera.jpg') }}" class="foto" alt="">
                    @endif
                </td>
                <td>{{ $d->jam_out ?? '-' }}</td>
                <td>
                    @if (!empty($d->foto_out))
                        <img src="{{ $path_out }}" class="foto" alt="Out" onerror="this.onerror=null;this.src='{{ asset('assets/img/camera.jpg') }}';">
                    @else
                        <img src="{{ asset('assets/img/camera.jpg') }}" class="foto" alt="">
                    @endif
                </td>
                <td>{{ $d->nama_lokasi_in ?? '-' }}</td>
                <td>{{ $keterangan_text }}</td>
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
                ( .................................... )
            </td>
            <td>
                Kepala Departemen
                <div class="spasi_ttd"></div>
                ( .................................... )
            </td>
            <td>
                Direktur
                <div class="spasi_ttd"></div>
                ( .................................... )
            </td>
        </tr>
    </table>

  </section>

</body>
</html>
