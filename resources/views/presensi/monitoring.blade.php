@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Monitoring Presensi</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">

        @if(isset($log_kecurangan_hari_ini) && count($log_kecurangan_hari_ini) > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-danger alert-important" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 15px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>
                        </div>
                        <div>
                            <h4 class="alert-title">Peringatan Keamanan Hari Ini! Terdapat Indikasi Kecurangan (1:N)</h4>
                            <div class="text-muted">
                                <ul class="mb-0 mt-1 pl-3">
                                    @foreach($log_kecurangan_hari_ini as $log)
                                        <li>
                                            <strong>{{ date('H:i', strtotime($log->waktu)) }} WIB</strong> -
                                            Akun: <b>{{ $log->email_login }}</b> | Laporan: {{ $log->pesan_kecurangan }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" /><path d="M16 3v4" /><path d="M8 3v4" />
                                            <path d="M4 11h16" /><path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                        </svg>
                                    </span>
                                    <input type="text" class="form-control" id="tanggal" autocomplete="off" name="tanggal" placeholder="Tanggal Presensi">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Email</th>
                                            <th>Nama Karyawan</th>
                                            <th>Departemen</th>
                                            <th>Jam Masuk</th>
                                            <th>Foto</th>
                                            <th>Jam Pulang</th>
                                            <th>Foto</th>
                                            <th>Keterangan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="loadpresensi"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-tampilkanpeta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lokasi Presensi Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadmap">
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-cek-wajah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Wajah Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-md-6">
                        <span class="badge bg-primary mb-2">Foto Master (Data Karyawan)</span><br>
                        <img id="load-foto-master" src="" class="img-fluid rounded shadow-sm" style="width: 250px; height: 250px; object-fit: cover; border: 3px solid #206bc4;">
                        <p class="mt-2 text-muted fw-bold" id="nama-karyawan-modal">-</p>
                    </div>
                    <div class="col-md-6">
                        <span class="badge bg-success mb-2">Bukti Absen Kamera</span><br>
                        <img id="load-foto-absen" src="" class="img-fluid rounded shadow-sm" style="width: 250px; height: 250px; object-fit: cover; border: 3px solid #2ecc71;">
                        <p class="mt-2 text-muted fw-bold" id="jam-absen-modal">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
    <script>
        $(function()
        {
            $("#tanggal").datepicker(
                {
                    autoclose: true,
                    todayHighlight: true,
                    format : 'yyyy-mm-dd'
                });

            function loadPresensi(tanggal) {
                $.ajax({
                    type:'POST',
                    url:'/getpresensi',
                    data:{
                        _token:"{{ csrf_token() }}",
                        tanggal:tanggal
                    },
                    success:function(respond){
                        $("#loadpresensi").html(respond);
                    }
                });
            }

            var hariIni = new Date().toISOString().split('T')[0];
            $("#tanggal").val(hariIni);
            loadPresensi(hariIni);
            $("#tanggal").change(function(e)
            {
                var tanggal = $(this).val();
                loadPresensi(tanggal);
            });

            var modalPeta = $("#modal-tampilkanpeta");
            $("#loadpresensi").on("click", ".tampilkanpeta", function(e) {
                e.preventDefault();
                var id = $(this).attr("id");

                $.ajax({
                    type: 'POST',
                    url: '/tampilkanpeta',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadmap").html(respond);
                        modalPeta.modal("show");
                    }
                });
            });

            modalPeta.on('shown.bs.modal', function () {
                if (typeof window.initMap === 'function') {
                    window.initMap();
                }
            });

            modalPeta.on('hidden.bs.modal', function () {
                $("#loadmap").html("");
            });

            $("#loadpresensi").on("click", ".cek-wajah", function(e) {
                e.preventDefault();

                var masterImg = $(this).attr('data-master');
                var absenImg  = $(this).attr('data-absen');
                var namaKaryawan = $(this).attr('data-nama');
                var jamMasuk  = $(this).attr('data-jam');

                $("#load-foto-master").attr("src", masterImg);
                $("#load-foto-absen").attr("src", absenImg);
                $("#nama-karyawan-modal").text("Atas Nama: " + namaKaryawan);
                $("#jam-absen-modal").text("Waktu Absen: " + jamMasuk);

                $("#modal-cek-wajah").modal("show");
            });

        });
    </script>
@endpush
