@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Settings</div>
    <div class="right"></div>
</div>
@endsection

@section('content')

<div class="section" style="margin-top: 4rem;">
    <div class="d-flex flex-column align-items-center">

        <div class="avatar mb-2">
            @if(!empty(Auth::guard('karyawan')->user()->foto))
            <img src="{{ asset('storage/uploads/karyawan/'.Auth::guard('karyawan')->user()->foto) }}"
                 alt="Avatar" class="imaged"
                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            @else
            <img src="{{ asset('assets/img/sample/avatar/avatar1.jpg') }}"
                 alt="avatar" class="imaged"
                 style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            @endif
        </div>

        <h3 class="mb-0">{{ Auth::guard('karyawan')->user()->nama_lengkap }}</h3>
        <div class="text-muted">{{ Auth::guard('karyawan')->user()->jabatan }}</div>
    </div>
</div>
<div class="section mt-2">
    <div class="listview-title">Akun</div>
    <ul class="listview image-listview">
        <li>
            <a href="/editprofile" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="person-circle-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>Edit Profil</div>
                </div>
            </a>
        </li>
    </ul>
</div>

<div class="section mt-2">
    <div class="listview-title">Aplikasi</div>
    <ul class="listview image-listview">
        <li>
            <div class="item">
                <div class="icon-box bg-dark">
                    <ion-icon name="information-circle-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>Versi Aplikasi</div>
                    <span class="text-muted">1.0.0</span>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="section mt-2 mb-4">
    <div class="listview-title">Keluar</div>
    <ul class="listview image-listview">
        <li>
            <a href="#" class="item" onclick="event.preventDefault(); document.getElementById('karyawan-logout-form').submit();">
                <div class="icon-box bg-danger">
                    <ion-icon name="exit-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>Logout</div>
                </div>
            </a>
        </li>
    </ul>
</div>

<form id="karyawan-logout-form" action="{{ route('karyawan.logout') }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection
