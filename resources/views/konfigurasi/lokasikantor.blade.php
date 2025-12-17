@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Konfigurasi Lokasi</h2>
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
                                <a href="#" class="btn btn-primary" id="btnTambahLokasi">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14" /><path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Lokasi
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <form action="/konfigurasi/lokasikantor" method="GET">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="form-group" style="position: relative;">
                                                <input type="text" name="nama_lokasi" id="nama_lokasi" class="form-control" placeholder="Cari Nama Lokasi" value="{{ Request('nama_lokasi') }}" style="padding-right: 35px;">
                                                @if(Request('nama_lokasi'))
                                                <a href="/konfigurasi/lokasikantor" class="text-white" title="Hapus Pencarian"
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
                                            <th>Nama Lokasi</th>
                                            <th>Koordinat (Lat,Lon)</th>
                                            <th>Radius (m)</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lokasi_kantor_list as $lokasi)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $lokasi->nama_lokasi }}</td>
                                            <td>{{ $lokasi->lokasi_kantor }}</td>
                                            <td>{{ $lokasi->radius }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-icon btn-primary edit-lokasi" data-id="{{ $lokasi->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.582a2.152 2.152 0 0 0 -2.914 -2.915l-9.155 9.155v3.66h3.66l9.155 -9.155z" /><path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                                <form action="/konfigurasi/{{ $lokasi->id }}/deletelokasi" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus lokasi {{ $lokasi->nama_lokasi }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24"
                                                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                    </button>
                                                </form>
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

<div class="modal modal-blur fade" id="modal-tambah-lokasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/konfigurasi/storelokasikantor" method="POST" id="frmStoreLokasi">
                    @csrf
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24h0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg></span>
                        <input type="text" value="" id="nama_lokasi_add" class="form-control" name="nama_lokasi" placeholder="Nama Lokasi" autocomplete="off">
                    </div>

                    <div class="input-icon mb-3">
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-map" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7l6 -3l6 3l6 -3v13l-6 3l-6 -3l-6 3v-13" /><path d="M9 4v13" /><path d="M15 7v13" /></svg></span>
                        <input type="text" value="" id="lokasi_kantor_add" class="form-control" name="lokasi_kantor" placeholder="Koordinat Kantor" autocomplete="off">
                    </div>

                    <div class="input-icon mb-3">
                        <span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-radar-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15.51 15.56a5 5 0 1 0 -3.51 1.44" /><path d="M18.832 17.86a9 9 0 1 0 -6.832 3.14" /><path d="M12 12v9" /></svg></span>
                        <input type="text" value="" id="radius_add" class="form-control" name="radius" placeholder="Radius" autocomplete="off">
                    </div>

                    <button class="btn btn-primary w-100" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="modal-edit-lokasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lokasi</h5>
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
        $("#btnResetSearch").click(function() {
            $("#nama_lokasi").val('');
            $("#frmCariLokasi").submit();
        });
        if ($("#nama_lokasi").val() == "") {
        }
        $("#btnTambahLokasi").click(function() {
        $("#modal-tambah-lokasi").modal("show");
        });
        $(".edit-lokasi").click(function() {
            var id = $(this).data('id');
            $.ajax({
                type: 'GET',
                url: '/konfigurasi/' + id + '/editlokasi',
                cache: false,
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    $("#modal-edit-lokasi").modal("show");
                }
            });
        });
        $(".delete-lokasi").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: "Apakah Anda Yakin Ingin Menghapus Lokasi?",
                text: "Jika iya, lokasi ini akan hilang secara permanen!",
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
        $("#frmStoreLokasi").submit(function(e) {
            var nama_lokasi = $("#nama_lokasi_add").val();
            var lokasi_kantor = $("#lokasi_kantor_add").val();
            var radius = $("#radius_add").val();
            if (nama_lokasi == "" || lokasi_kantor == "" || radius == "") {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Semua kolom harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
@endpush
