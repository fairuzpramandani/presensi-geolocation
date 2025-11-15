@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Ubah Password</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div id="appCapsule">
    <div class="section" style="margin-top: 80px;">

        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (session('warning'))
        <div class="alert alert-warning" role="alert">
            {{ session('warning') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.direct.update') }}">
            @csrf
            <div class="section mt-1 mb-2">
                <h4>Masukkan Email & Password Baru</h4>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="email" class="form-control" name="email" placeholder="Email Terdaftar" required autofocus value="{{ old('email') }}">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password" placeholder="Password Baru (Min. 5 karakter)" required>
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Ketik Ulang Password Baru" required>
                </div>
            </div>

            <div class="fab-button bottom" style="margin-bottom:50px">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Ubah Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
