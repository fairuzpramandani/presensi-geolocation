@extends('layouts.admin.tabler')
@section('content')

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Overview</div>
                <h2 class="page-title">Dashboard Presensi</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="/presensi/laporan" class="btn btn-primary d-none d-sm-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" /><path d="M7 11l5 5l5 -5" /><path d="M12 4l0 12" /></svg>
                        Download Laporan Hari Ini
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <div class="row row-deck row-cards mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" /><path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" /><path d="M12 11v2a14 14 0 0 0 2.5 8" /><path d="M8 15a18 18 0 0 0 1.8 6" /><path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium text-uppercase text-muted">Karyawan Hadir</div>
                                <div class="h1 mb-0">{{ $rekappresensi->jmlhadir ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium text-uppercase text-muted">Karyawan Izin</div>
                                <div class="h1 mb-0">{{ $rekapizin->jmlizin ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-stethoscope"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h-1a2 2 0 0 0 -2 2v3.5h0a5.5 5.5 0 0 0 11 0v-3.5a2 2 0 0 0 -2 -2h-1" /><path d="M8 15a6 6 0 1 0 12 0v-3" /><path d="M11 3v2" /><path d="M6 6v2" /><path d="M20 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium text-uppercase text-muted">Karyawan Sakit</div>
                                <div class="h1 mb-0">{{ $rekapizin->jmlsakit ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-danger text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-alarm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16 6.072a8 8 0 1 1 -11.995 7.213l-.005 -.285l.005 -.285a8 8 0 0 1 11.995 -6.643zm-4 2.928a1 1 0 0 0 -1 1v3l.007 .117a1 1 0 0 0 .993 .883h2l.117 -.007a1 1 0 0 0 .883 -.993l-.007 -.117a1 1 0 0 0 -.993 -.883h-1v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" /><path d="M6.412 3.191a1 1 0 0 1 1.273 1.539l-.097 .08l-2.75 2a1 1 0 0 1 -1.273 -1.54l.097 -.08l2.75 -2z" /><path d="M16.191 3.412a1 1 0 0 1 1.291 -.288l.106 .067l2.75 2a1 1 0 0 1 -1.07 1.685l-.106 -.067l-2.75 -2a1 1 0 0 1 -.22 -1.397z" /></svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium text-uppercase text-muted">Telat / Alpa</div>
                                <div class="h1 mb-0">{{ $rekappresensi->jmlterlambat ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tren Kehadiran (7 Hari Terakhir)</h3>
                    </div>
                    <div class="card-body">
                        <div id="chart-attendance" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card" style="height: 100%;">
                    <div class="card-header">
                        <h3 class="card-title">Butuh Persetujuan</h3>
                        <span class="badge bg-red ms-auto">{{ $izin_pending != null ? count($izin_pending) : 0 }} Menunggu</span>
                    </div>

                    <div class="list-group list-group-flush list-group-hoverable">
                        @if(isset($izin_pending) && count($izin_pending) > 0)
                            @foreach ($izin_pending as $izin)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="badge {{ strtolower($izin->status ?? 'izin') == 'sakit' ? 'bg-warning' : 'bg-info' }}"></span>
                                    </div>
                                    <div class="col text-truncate">
                                        <a href="#" class="text-reset d-block">{{ $izin->nama_lengkap }}</a>
                                        <div class="d-block text-secondary text-truncate mt-n1">Pengajuan ({{ $izin->keterangan }})</div>
                                    </div>
                                    <div class="col-auto">
                                        <form action="/presensi/aprrovedizinsakit" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id_izinsakit_form" value="{{ $izin->id }}">
                                            <input type="hidden" name="status_approved" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Setujui pengajuan ini?')">Setujui</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="list-group-item text-center text-muted">
                                Tidak ada pengajuan yang perlu disetujui.
                            </div>
                        @endif

                        <div class="list-group-item">
                            <div class="text-center">
                                <a href="/presensi/izinsakit" class="text-muted text-sm text-decoration-none">Lihat Semua Pengajuan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cards">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Presensi Hari Ini</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter text-nowrap datatable">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($riwayat_hari_ini) && count($riwayat_hari_ini) > 0)
                                    @foreach ($riwayat_hari_ini as $riwayat)
                                    <tr>
                                        <td>
                                            <div class="font-weight-medium">{{ $riwayat->nama_lengkap }}</div>
                                            <div class="text-secondary text-sm">{{ $riwayat->jabatan ?? 'Karyawan' }}</div>
                                        </td>
                                        <td>{{ $riwayat->jam_in != null ? date('H:i', strtotime($riwayat->jam_in)) . ' WIB' : '-' }}</td>
                                        <td>{{ $riwayat->jam_out != null ? date('H:i', strtotime($riwayat->jam_out)) . ' WIB' : 'Belum Pulang' }}</td>
                                        <td>
                                            @if ($riwayat->jam_in > '08:00:00')
                                                <span class="badge bg-danger me-1"></span> Terlambat
                                            @else
                                                <span class="badge bg-success me-1"></span> Tepat Waktu
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data presensi hari ini.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Log Validasi Lokasi</h3>
                    </div>
                    <div class="card-body p-3">
                        <ul class="steps steps-vertical">
                            @if(isset($riwayat_hari_ini) && count($riwayat_hari_ini) > 0)
                                @foreach ($riwayat_hari_ini as $log)
                                <li class="step-item">
                                    <div class="h4 m-0">
                                        {{ $log->nama_lengkap }}
                                        @if ($log->jam_in > '08:00:00')
                                            <span class="badge bg-red-lt ms-2" style="font-size: 10px;">Terlambat</span>
                                        @else
                                            <span class="badge bg-green-lt ms-2" style="font-size: 10px;">Tepat Waktu</span>
                                        @endif
                                    </div>
                                    <div class="text-secondary mt-2">

                                        <div class="fw-bold text-light mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-community" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 9l5 5v7h-5v-4m0 4h-5v-7l5 -5m1 1v-6a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v17h-8" /><path d="M13 7l0 .01" /><path d="M17 7l0 .01" /><path d="M17 11l0 .01" /><path d="M17 15l0 .01" /></svg>
                                            Radius: <span class="text-info">{{ $log->nama_lokasi_absen ?? 'Tidak Dikenali' }}</span>
                                        </div>

                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin text-red" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" /></svg>
                                        <a href="http://googleusercontent.com/maps.google.com/?q={{ $log->location_in }}" target="_blank" class="text-decoration-none text-primary" style="font-size: 12px;">
                                            Buka Titik Peta GPS
                                        </a>

                                        <small class="text-muted d-block mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 7v5l3 3" /></svg>
                                            Hari ini, {{ date('H:i', strtotime($log->jam_in)) }} WIB
                                        </small>
                                    </div>
                                </li>
                                @endforeach
                            @else
                                <li class="step-item">
                                    <div class="text-secondary">Belum ada log lokasi hari ini.</div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Data Dummy untuk grafik (Nanti bisa diganti dengan variabel dari Laravel)
        window.ApexCharts && (new ApexCharts(document.getElementById('chart-attendance'), {
            chart: {
                type: "bar",
                fontFamily: 'inherit',
                height: 280,
                parentHeightOffset: 0,
                toolbar: { show: false },
                animations: { enabled: true }
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 4
                }
            },
            dataLabels: { enabled: false },
            fill: { opacity: 1 },
            series: [{
                name: "Hadir",
                data: [45, 48, 42, 50, 49, 44, 46] // Contoh data
            }, {
                name: "Terlambat",
                data: [5, 2, 8, 0, 1, 6, 4] // Contoh data
            }],
            tooltip: { theme: 'dark' },
            grid: {
                padding: { top: -20, right: 0, left: -4, bottom: -4 },
                strokeDashArray: 4,
            },
            xaxis: {
                labels: { padding: 0 },
                tooltip: { enabled: false },
                axisBorder: { show: false },
                categories: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            },
            yaxis: { labels: { padding: 4 } },
            colors: ["#2fb344", "#d63939"],
            legend: { show: true, position: 'bottom' },
        })).render();
    });
</script>

@endsection
