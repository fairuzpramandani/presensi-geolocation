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
            $waktu_masuk_max = "07:45:00";
            $waktu_pulang_min = "17:00:00";

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

    <a href="/presensi/create?ket=in" class="icon-item"
       onclick="return checkAbsenTime('{{ $waktu_masuk_max }}', 'in')">
        <img src="{{ asset('assets/img/icon/absen-masuk.png') }}" alt="Absen Masuk" class="icon-image">
        <span>Absen Masuk</span>
    </a>

    <a href="/presensi/create?ket=out" class="icon-item"
       onclick="return checkAbsenTime('{{ $waktu_pulang_min }}', 'out')">
        <img src="{{ asset('assets/img/icon/absen-keluar.png') }}" alt="Absen Pulang" class="icon-image">
        <span>Absen Pulang</span>
    </a>

    <a href="/presensi/izin" class="icon-item">
        <img src="{{ asset('assets/img/icon/izin.png') }}" alt="Izin" class="icon-image">
        <span>Ajukan Izin</span>
    </a>
    <a href="/presensi/histori" class="icon-item">
        <img src="{{ asset('assets/img/icon/histori.png') }}" alt="Histori" class="icon-image">
        <span>Histori</span>
    </a>

</div>

<div style="height: 100px;"></div>

@endsection
@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dashboardcustom.css') }}">
@endpush
@push('scripts')
<script>
    function checkAbsenTime(batasWaktu, jenisAbsen) {
        var now = new Date();
        var currentTime = now.toTimeString().split(' ')[0];
        var waktuDisplay = batasWaktu.substring(0, 5);

        /*if (jenisAbsen === 'in') {
            if (currentTime > batasWaktu) {
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Absen Masuk hanya bisa dilakukan sebelum jam ' + waktuDisplay + ' WIB.',
                    icon: 'warning',
                });
                return false;
            }
        } else if (jenisAbsen === 'out') {
            if (currentTime < batasWaktu) {
                Swal.fire({
                    title: 'Belum Waktunya!',
                    text: 'Absen Pulang bisa dilakukan setelah jam ' + waktuDisplay + ' WIB.',
                    icon: 'warning',
                });
                return false;
            }
        }*/
        return true;
    }
</script>
@endpush
