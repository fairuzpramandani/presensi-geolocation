@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Lupa Password</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<div id="appCapsule">
    <div class="section" style="margin-top: 60px;">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <input type="email" class="form-control" name="email" placeholder="Masukkan Email Anda" required autofocus>
                </div>
            </div>

            <div class="fab-button bottom-group" style="margin-bottom:50px">
                <button type="submit" class="btn btn-primary btn-block btn-lg">Kirim Link Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection
