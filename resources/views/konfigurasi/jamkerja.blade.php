@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Konfigurasi Jam Kerja</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if (Session::get('success'))
                                    <div class="alert alert-success">{{ Session::get('success') }}</div>
                                @endif
                                @if (Session::get('warning'))
                                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahJK">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14" /><path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <form action="/konfigurasi/jamkerja" method="GET">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="form-group" style="position: relative;">
                                                <input type="text" name="nama_jam_kerja" id="nama_jam_kerja_pencarian" class="form-control"
                                                    placeholder="Cari Nama Jam Kerja"
                                                    value="{{ Request('nama_jam_kerja') }}" style="padding-right: 35px;">

                                                @if(Request('nama_jam_kerja'))
                                                <a href="/konfigurasi/jamkerja" class="text-white" title="Hapus Pencarian"
                                                    style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); z-index: 10; cursor: pointer;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" />
                                                    </svg>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                        <path d="M21 21l-6 -6" />
                                                    </svg>
                                                    Cari
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode JK</th>
                                            <th>Nama JK</th>
                                            <th>Awal Jam Masuk</th>
                                            <th>Jam Masuk</th>
                                            <th>Akhir Jam Masuk</th>
                                            <th>Jam Pulang</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jam_kerja as $d)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $d->kode_jam_kerja}}</td>
                                                <td>{{ $d->nama_jam_kerja}}</td>
                                                <td>{{ $d->awal_jam_masuk}}</td>
                                                <td>{{ $d->jam_masuk}}</td>
                                                <td>{{ $d->akhir_jam_masuk}}</td>
                                                <td>{{ $d->jam_pulang}}</td>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <a href="#" class="edit btn btn-primary btn-sm" kode_jam_kerja="{{ $d->kode_jam_kerja }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                <path d="M16 5l3 3" />
                                                            </svg>
                                                        </a>
                                                        <form action="/konfigurasi/{{ $d->kode_jam_kerja}}/delete" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="kode_jam_kerja" value="{{ $d->kode_jam_kerja }}">
                                                            <button type="submit" class="btn btn-danger btn-sm delete-confirm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-inputjk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Jam Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/konfigurasi/storejamkerja" method="POST" id="frmJK">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-1a2 2 0 0 1 2 -2h2" /><path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                        <path d="M16 4h2a2 2 0 0 1 2 2v1" /><path d="M16 20h2a2 2 0 0 0 2 -2v-1" /><path d="M5 11h1v2h-1z" /><path d="M10 11l0 2" />
                                        <path d="M14 11h1v2h-1z" /><path d="M19 11l0 2" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="kode_jam_kerja" class="form-control" name="kode_jam_kerja" placeholder="Kode Jam kerja">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-barcode">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7v-1a2 2 0 0 1 2 -2h2" /><path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                        <path d="M16 4h2a2 2 0 0 1 2 2v1" /><path d="M16 20h2a2 2 0 0 0 2 -2v-1" /><path d="M5 11h1v2h-1z" /><path d="M10 11l0 2" />
                                        <path d="M14 11h1v2h-1z" /><path d="M19 11l0 2" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="nama_jam_kerja" class="form-control" name="nama_jam_kerja" placeholder="Nama Jam kerja">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-alarm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16 6.072a8 8 0 1 1 -11.995 7.213l-.005 -.285l.005 -.285a8 8 0 0 1 11.995 -6.643zm-4 2.928a1 1 0 0 0 -1 1v3l.007 .117a1 1 0 0 0
                                        .993 .883h2l.117 -.007a1 1 0 0 0 .883 -.993l-.007 -.117a1 1 0 0 0 -.993 -.883h-1v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                                        <path d="M6.412 3.191a1 1 0 0 1 1.273 1.539l-.097 .08l-2.75 2a1 1 0 0 1 -1.273 -1.54l.097 -.08l2.75 -2z" />
                                        <path d="M16.191 3.412a1 1 0 0 1 1.291 -.288l.106 .067l2.75 2a1 1 0 0 1 -1.07 1.685l-.106 -.067l-2.75 -2a1 1 0 0 1 -.22 -1.397z" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="awal_jam_masuk" class="form-control" name="awal_jam_masuk" placeholder="Awal Jam Masuk">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-alarm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16 6.072a8 8 0 1 1 -11.995 7.213l-.005 -.285l.005 -.285a8 8 0 0 1 11.995 -6.643zm-4 2.928a1 1 0 0 0 -1 1v3l.007 .117a1 1 0 0 0
                                        .993 .883h2l.117 -.007a1 1 0 0 0 .883 -.993l-.007 -.117a1 1 0 0 0 -.993 -.883h-1v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                                        <path d="M6.412 3.191a1 1 0 0 1 1.273 1.539l-.097 .08l-2.75 2a1 1 0 0 1 -1.273 -1.54l.097 -.08l2.75 -2z" />
                                        <path d="M16.191 3.412a1 1 0 0 1 1.291 -.288l.106 .067l2.75 2a1 1 0 0 1 -1.07 1.685l-.106 -.067l-2.75 -2a1 1 0 0 1 -.22 -1.397z" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="jam_masuk" class="form-control" name="jam_masuk" placeholder="Jam Masuk">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-alarm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16 6.072a8 8 0 1 1 -11.995 7.213l-.005 -.285l.005 -.285a8 8 0 0 1 11.995 -6.643zm-4 2.928a1 1 0 0 0 -1 1v3l.007 .117a1 1 0 0 0
                                        .993 .883h2l.117 -.007a1 1 0 0 0 .883 -.993l-.007 -.117a1 1 0 0 0 -.993 -.883h-1v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                                        <path d="M6.412 3.191a1 1 0 0 1 1.273 1.539l-.097 .08l-2.75 2a1 1 0 0 1 -1.273 -1.54l.097 -.08l2.75 -2z" />
                                        <path d="M16.191 3.412a1 1 0 0 1 1.291 -.288l.106 .067l2.75 2a1 1 0 0 1 -1.07 1.685l-.106 -.067l-2.75 -2a1 1 0 0 1 -.22 -1.397z" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="akhir_jam_masuk" class="form-control" name="akhir_jam_masuk" placeholder="Akhir Jam Masuk">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-alarm"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M16 6.072a8 8 0 1 1 -11.995 7.213l-.005 -.285l.005 -.285a8 8 0 0 1 11.995 -6.643zm-4 2.928a1 1 0 0 0 -1 1v3l.007 .117a1 1 0 0 0
                                        .993 .883h2l.117 -.007a1 1 0 0 0 .883 -.993l-.007 -.117a1 1 0 0 0 -.993 -.883h-1v-2l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
                                        <path d="M6.412 3.191a1 1 0 0 1 1.273 1.539l-.097 .08l-2.75 2a1 1 0 0 1 -1.273 -1.54l.097 -.08l2.75 -2z" />
                                        <path d="M16.191 3.412a1 1 0 0 1 1.291 -.288l.106 .067l2.75 2a1 1 0 0 1 -1.07 1.685l-.106 -.067l-2.75 -2a1 1 0 0 1 -.22 -1.397z" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="jam_pulang" class="form-control" name="jam_pulang" placeholder="Jam Pulang">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12
                                        -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z" />
                                        <path d="M6.5 12h14.5" />
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-editjk" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Jam Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditform">
                </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
    <script>
    $(function() {
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: "Apakah Anda Yakin Ingin Menghapus Data Ini?",
                text: "Jika iya, Data ini akan hilang secara permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $(".edit").click(function() {
            var kode_jam_kerja = $(this).attr('kode_jam_kerja');
            $.ajax({
                type: 'POST',
                url: '/konfigurasi/editjamkerja',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_jam_kerja: kode_jam_kerja
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                }
            });
            $("#modal-editjk").modal("show");
        });

        $("#btnTambahJK").click(function() {
            $("#modal-inputjk").modal("show");
        });

        $("#frmJK").submit(function() {
            var kode_jam_kerja = $("#frmJK").find("#kode_jam_kerja").val();
            var nama_jam_kerja = $("#frmJK").find("#nama_jam_kerja").val();
            var awal_jam_masuk = $("#frmJK").find("#awal_jam_masuk").val();
            var jam_masuk = $("#frmJK").find("#jam_masuk").val();
            var akhir_jam_masuk = $("#frmJK").find("#akhir_jam_masuk").val();
            var jam_pulang = $("#frmJK").find("#jam_pulang").val();

            if (kode_jam_kerja == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Kode Jam Kerja Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#kode_jam_kerja").focus();
                });
                return false;
            } else if (nama_jam_kerja == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Jam Kerja Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#nama_jam_kerja").focus();
                });
                return false;
            } else if (awal_jam_masuk == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Awal Jam Masuk Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#awal_jam_masuk").focus();
                });
                return false;
            } else if (jam_masuk == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jam Masuk Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#jam_masuk").focus();
                });
                return false;
            } else if (akhir_jam_masuk == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Akhir Masuk Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#akhir_jam_masuk").focus();
                });
                return false;
            } else if (jam_pulang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jam Pulang Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#jam_pulang").focus();
                });
                return false;
            }
        });
    });
    </script>
@endpush
