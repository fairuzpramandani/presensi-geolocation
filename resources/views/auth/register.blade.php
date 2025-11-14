<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Registrasi</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />
    <link rel="icon" type="image/png" href="{{ asset ('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="__manifest.json">
</head>

<body class="bg-white">

    <div id="loader">
        <div class="spinner-border text-primary" role="status"></div>
    </div>
    <div id="appCapsule" class="pt-0">

        <div class="login-form mt-1">
            <div class="section">
                <img src="{{ asset('assets/img/login/login.png') }}" alt="image" class="form-image">
            </div>
            <div class="section mt-1">
                <h1>Buat Akun Karyawan</h1>
                <h4>Silahkan Isi Data Anda</h4>
            </div>
            <div class="section mt-1 mb-5">
                    @if ($errors->any())
                    <div class="alert alert-outline-danger mb-1">
                        <strong>Gagal membuat akun:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (Session::get('warning'))
                    <div class="alert alert-outline-warning">
                        {{ Session::get('warning') }}
                    </div>
                    @endif

                    <form action="/prosesregister" method="POST">
                    @csrf
                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}">
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Jabatan" value="{{ old('jabatan') }}">
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" placeholder="No. HP" value="{{ old('no_hp') }}">
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <select class="form-control" name="kode_dept" id="kode_dept">
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ old('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ $d->nama_dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                    </div>

                    <div class="form-group boxed">
                        <div class="input-wrapper">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi Password">
                        </div>
                    </div>

                    <div class="form-button-group mt-2">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Buat Akun</button>
                    </div>

                    <div class="form-links mt-2">
                        <div>
                            <a href="/">Sudah Punya Akun? Login di sini</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
    <script src="{{ asset('assets/js/lib/jquery-3.4.1.min.js')}}"></script>
    <script src="{{ asset('assets/js/lib/bootstrap.min.js')}}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
    <script src="{{ asset('assets/js/plugins/owl-carousel/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/jquery-circle-progress/circle-progress.min.js')}}"></script>
    <script src="{{ asset('assets/js/base.js')}}"></script>

</body>
</html>
