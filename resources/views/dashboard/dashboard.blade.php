@extends('layouts.presensi')
@section('content')

<div class="app-header">
    <div>
        <img src="{{ asset('assets/img/cms-hadir.png') }}" alt="Logo" class="logo">
    </div>
    <div>
        @if(!empty(Auth::guard('karyawan')->user()->foto))
        <img src="{{ asset('storage/uploads/karyawan/'.Auth::guard('karyawan')->user()->foto) }}" alt="Avatar" class="avatar">
        @else
        <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="avatar">
        @endif
    </div>
</div>

<div class="greeting-text text-center mb-3">
    <h2 class="mb-0">
        @php
    $hour = date('H');
    $greeting = "Selamat Datang";
    if ($hour >= 4 && $hour < 12) {
        $greeting = "Selamat Pagi";
    }
    elseif ($hour >= 12 && $hour < 15) {
        $greeting = "Selamat Siang";
    }
    elseif ($hour >= 15 && $hour < 18) {
        $greeting = "Selamat Sore";
    }
    else {
        $greeting = "Selamat Malam";
    }
@endphp
{{ $greeting }}
    </h2>
    <p class="text-muted mb-0" style="font-size: 0.9rem;">Semoga harimu menyenangkan!</p>
    <p class="text-muted mb-0">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</p>
</div>


<div class="icon-grid">

    <a href="/presensi/create" class="icon-item">
        <img src="{{ asset('assets/img/icon/absen-masuk.png') }}" alt="Absen Masuk" class="icon-image">
        <span>Absen Masuk</span>
    </a>

    <a href="/presensi/create" class="icon-item">
        <img src="{{ asset('assets/img/icon/absen-keluar.png') }}" alt="Absen Pulang" class="icon-image">
        <span>Absen Pulang</span>
    </a>

    <a href="/presensi/izin" class="icon-item">
        <img src="{{ asset('assets/img/icon/izin.png') }}" alt="Izin" class="icon-image">
        <span>Izin</span>
    </a>
    <a href="/presensi/histori" class="icon-item">
        <img src="{{ asset('assets/img/icon/histori.png') }}" alt="Izin" class="icon-image">
        <span>Histori</span>
    </a>

</div>

<div style="height: 100px;"></div>

@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dashboardcustom.css') }}">
@endpush
