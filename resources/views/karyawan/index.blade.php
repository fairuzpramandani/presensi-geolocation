@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title">Data Karyawan</h2>
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
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                @endif
                                @if (Session::get('warning'))
                                    <div class="alert alert-warning">
                                        {{ Session::get('warning') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="dol-12">
                                <a href="#" class="btn btn-primary" id="btnTambahkaryawan">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                            <form action="/karyawan" method="GET">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" placeholder="Nama Karyawan" value="{{ Request('nama_karyawan') }}">
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-group">
                                            <select name="kode_dept" id="kode_dept" class="form-select">
                                                <option value="">Jabatan</option>
                                                @foreach ($departemen as $d)
                                                    <option {{ Request('kode_dept')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
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
                                            <th>Email</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>No. HP</th>
                                            <th>Foto</th>
                                            <th>Departemen</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            @foreach ($karyawan as $d)
                                            @php
                                                $path = Storage::url('uploads/karyawan/'.$d->foto);
                                            @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration + $karyawan->firstItem() -1 }}</td>
                                                    <td>{{ $d->email }}</td>
                                                    <td>{{ $d->nama_lengkap }}</td>
                                                    <td>{{ $d->jabatan }}</td>
                                                    <td>{{ $d->no_hp }}</td>
                                                    <td>
                                                        @if (empty($d->foto))
                                                        <img src="assets/img/sample/avatar/avatar1.jpg" alt="" class="avatar">
                                                        @else
                                                        <img src="{{ url($path) }}" class="avatar" alt="">
                                                        @endif
                                                    </td>
                                                    <td>{{ $d->nama_dept }}</td>
                                                    <td>
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <a href="#" class="edit btn btn-primary btn-sm" email="{{ $d->email }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                    <path d="M16 5l3 3" />
                                                                </svg>
                                                            </a>
                                                            <form action="/karyawan/{{ $d->email }}/delete" method="POST" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="email" value="{{ $d->email }}">
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
                                        {{ $karyawan->links("vendor.pagination.bootstrap-5") }}
                                </div>
                            </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
    <div class="modal modal-blur fade" id="modal-inputkaryawan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Data Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="/karyawan/store" method="POST" id="frmKaryawan" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                </span>
                            <input type="text" value="" id="email" class="form-control" name="email" placeholder="Email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                            <input type="text" value="" id="nama_lengkap" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9z" />
                                        <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                    </svg>
                                </span>
                            <input type="text" value="" id="jabatan" class="form-control" name="jabatan" placeholder="Jabatan">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                    </svg>
                                </span>
                            <input type="text" value="" id="no_hp" class="form-control" name="no_hp" placeholder="No. HP">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                            <input type="file" name="foto" class="form-control">
                        </div>
                    </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <select name="kode_dept" id="kode_dept" class="form-select">
                                    <option value="">Jabatan</option>
                                        @foreach ($departemen as $d)
                                            <option {{ Request('kode_dept')==$d->kode_dept ? 'selected' : '' }} value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <select name="kode_jam_kerja" id="kode_jam_kerja" class="form-select">
                                    <option value="">Pilih Jam Kerja</option>
                                    @foreach ($jam_kerja as $j)
                                        <option value="{{ $j->kode_jam_kerja }}">{{ $j->nama_jam_kerja }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="row mt-2">
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
                </form>
            </div>

          </div>
        </div>
      </div>
    </div>

{{--Modal Edit--}}

    <div class="modal modal-blur fade" id="modal-editkaryawan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Data Karyawan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="loadeditform">
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
            $("#btnTambahkaryawan").click(function()
            {
                $("#modal-inputkaryawan").modal("show");
            });

            $(".edit").click(function()
            {
                var email = $(this).attr('email');
                $.ajax(
                    {
                        type:'POST',
                        url:'/karyawan/edit',
                        cache:false,
                        data:
                        {
                            _token:"{{ csrf_token(); }}",
                            email: email
                        },
                        success:function(respond)
                        {
                            $("#loadeditform").html(respond);
                        }
                    });
                $("#modal-editkaryawan").modal("show");
            });

            $(".delete-confirm").click(function(e)
            {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: "Apakah Anda Yakin Ingin Menghapus Data?",
                    text: "Jika Iya Anda tidak dapat mengembalikannya!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, hapus!"
                    }).then((result) => {
                        if (result.isConfirmed)
                        {
                            form.submit();
                        }
                    });
                });

            @if (Session::get('success'))
                Swal.fire({
                    title: 'Dihapus!',
                    text: '{{ Session::get('success') }}',
                    icon: 'success',
                    showConfirmButton: true,
                    timer: 2000
                });
            @endif

            $("#frmKaryawan").submit(function()
            {
                var email = $("#email").val();
                var nama_lengkap = $("#nama_lengkap").val();
                var jabatan = $("#jabatan").val();
                var no_hp = $("#no_hp").val();
                var kode_dept = $("#frmKaryawan").find("#kode_dept").val();
                var kode_jam_kerja = $("#frmKaryawan").find("#kode_jam_kerja").val();
                if (email=="")
                {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Email Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result)=>
                    {
                        $("#email").focus();
                    });
                    return false;
                }else if (nama_lengkap=="")
                {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Nama Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result)=>
                    {
                        $("#nama_lengkap").focus();
                    });
                    return false;
                }else if (jabatan=="")
                {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Jabatan Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result)=>
                    {
                        $("#jabatan").focus();
                    });
                    return false;
                }else if (no_hp=="")
                {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'No. HP Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result)=>
                    {
                        $("#no_hp").focus();
                    });
                    return false;
                }else if (kode_dept=="")
                {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Departemen Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result)=>
                    {
                        $("#frmKaryawan").find("#kode_dept").focus()
                    });
                    return false;
                }else if (kode_jam_kerja == "") {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Jam Kerja Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $("#frmKaryawan").find("#kode_jam_kerja").focus()
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
