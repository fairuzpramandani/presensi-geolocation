@extends('layouts.presensi')
@section('header')
<style>
.fab.bg-primary {
    background-color: #0A234E !important;
}
.fab.bg-primary:hover,
.fab.bg-primary:focus,
.fab.bg-primary:active {
    background-color: #051532 !important;
    border-color: #051532 !important;
    color: #fff !important;
}
</style>
<!---- App Header ---->
<div class="appHeader text-light" style="background-color: #0A234E !important;">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name= "chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTiitle">Data Izin</div>
    <div class="right"></div>
</div>
<!---- * App Header ---->
@endsection
@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
         @php
            $messagesuccess = Session::get('success');
            $messageerror = Session::get('error');
        @endphp
        @if (Session::get('success'))
        <div class="alert alert-success">
            {{ $messagesuccess }}
        </div>
        @endif
        @if (Session::get('error'))
        <div class="alert alert-danger">
            {{ $messageerror }}
        </div>
        @endif
    </div>
</div>
<ul class="listview image-listview">
    @foreach ($dataizin as $d)
        <li>
            <div class="item">
                <img src="{{ asset('storage/uploads/karyawan/'.Auth::guard('karyawan')->user()->foto) }}" alt="foto profil" class="image">

                <div class="in" style="display: flex; justify-content: space-between; align-items: flex-start;">

                    <div>
                        <b>{{ date("d F Y", strtotime($d->tgl_izin)) }} ({{ $d->status== "sakit" ? "Sakit" : "Izin" }})</b>
                        <br>
                        <small class="text-muted">{{ $d->keterangan }}</small>
                    </div>

                    <div>
                        @if ($d->status_approved == 0)
                            <span class="badge bg-warning small">Menunggu</span>
                        @elseif ($d->status_approved == 1)
                            <span class="badge bg-success small">Disetujui</span>
                        @elseif ($d->status_approved == 2)
                            <span class="badge bg-danger small">Ditolak</span>
                        @endif
                    </div>
                    </div>
            </div>
        </li>
    @endforeach
</ul>
<div class="fab-button bottom-right" style="margin-bottom: 50px;">
    <a href="/presensi/buatizin" class="fab bg-primary">
        <ion-icon name="add-outline"></ion-icon>
    </a>
</div>
@endsection
