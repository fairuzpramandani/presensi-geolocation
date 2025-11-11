@extends('layouts.presensi')

@section('header')
{{-- ... (Bagian header Anda tetap sama) ... --}}
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTiitle">Form Izin</div>
    <div class="right"></div>
</div>
@endsection

@section ('content')
<div class="section mt-2">
    <div class="section-title">Formulir Pengajuan</div>
    <div class="card">
        <div class="card-body">

            <form method="POST" action="/presensi/storeizin" id="frmIzin">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="form-label" for="tgl_izin">Tanggal</label>
                        <ion-icon name="calendar-outline" class="clear-input-icon"></ion-icon>
                        <input type="text" id="tgl_izin" name="tgl_izin" class="form-control" placeholder="Pilih Tanggal">
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="" selected disabled>Pilih Status</option>
                            <option value="i">Izin</option>
                            <option value="s">Sakit</option>
                        </select>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="form-label" for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="4" class="form-control" placeholder="Tulis alasan Anda..."></textarea>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        Kirim Pengajuan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#tgl_izin", {
            dateFormat: "Y-m-d",
            enableTime: false,
            minDate: "today",
        });

        $("#frmIzin").submit(function(){
            var tgl_izin = $("#tgl_izin").val();
            var status = $("#status").val();
            var keterangan = $("#keterangan").val();

            if (tgl_izin=="") {
                Swal.fire({
                        title: 'Peringatan',
                        text: 'Tanggal harus Diisi',
                        icon: 'warning',
                });
                return false;
            }else if (status==null || status=="") {
                Swal.fire({
                        title: 'Peringatan',
                        text: 'Status harus Dipilih',
                        icon: 'warning',
                });
                return false
            }else if (keterangan=="") {
                Swal.fire({
                        title: 'Peringatan',
                        text: 'Keterangan harus Diisi',
                        icon: 'warning',
                });
                return false
            }
        });
    });
</script>
@endpush
