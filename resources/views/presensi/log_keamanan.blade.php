@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-lock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -7.186 15.632l-1.314 .368l-1.314 -.368a12 12 0 0 1 -7.186 -15.632a12 12 0 0 0 8.5 -3z"></path><path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M12 12v2.5"></path></svg>
                    Audit Log & Keamanan (Anti-Fraud)
                </h2>
                <div class="text-muted mt-1">Riwayat percobaan manipulasi absensi (Wajah & Fake GPS).</div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Waktu Kejadian</th>
                            <th>Email Login</th>
                            <th>Tipe Pelanggaran</th>
                            <th>Detail Laporan (AI)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>
                                    <span class="badge bg-dark">{{ date('d M Y', strtotime($log->created_at)) }}</span><br>
                                    <small>{{ date('H:i:s', strtotime($log->created_at)) }} WIB</small>
                                </td>
                                <td class="fw-bold text-primary">{{ $log->email_login }}</td>
                                <td>
                                    @if($log->tipe_kecurangan == 'Face_Mismatch')
                                        <span class="badge bg-warning">Wajah Tidak Cocok</span>
                                    @elseif($log->tipe_kecurangan == 'Fake_GPS')
                                        <span class="badge bg-danger">Fake GPS Terdeteksi</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $log->tipe_kecurangan }}</span>
                                    @endif
                                </td>
                                <td class="text-danger">{{ $log->pesan_log }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Sistem Aman. Belum ada riwayat kecurangan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
