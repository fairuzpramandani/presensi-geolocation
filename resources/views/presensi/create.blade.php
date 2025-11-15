@extends('layouts.presensi')

@section('header')
<div class="appHeader text-light" style="background-color: #0A234E !important;">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Presensi</div>
    <div class="right"></div>
</div>

<style>
    .webcam-capture,
    .webcam-capture video {
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 15px;
    }
    .btn-primary {
            background-color: #0A234E !important;
            border-color: #0A234E !important;
        }
    #map {
        height: 200px;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        <input type="hidden" id="lokasi">
        <div class="webcam-capture"></div>
    </div>
</div>

<div class="row">
    <div class="col">
        @php
            $ket = request()->get('ket');
            $waktu_masuk_max = "07:45:00";
            $waktu_pulang_min = "17:00:00";
        @endphp

        @if ($ket == 'out')
        <button id="takeabsen" class="btn btn-danger btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Pulang
        </button>
        @else
        <button id="takeabsen" class="btn btn-primary btn-block">
            <ion-icon name="camera-outline"></ion-icon>
            Absen Masuk
        </button>
        @endif
    </div>
</div>

<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    Webcam.set({
        height: 480,
        width: 640,
        image_format: 'jpeg',
        jpeg_quality: 80,
    });
    Webcam.attach('.webcam-capture');
    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position) {
        lokasi.value = position.coords.latitude + "," + position.coords.longitude;

        var map = L.map('map', {
            attributionControl: false
        }).setView([position.coords.latitude, position.coords.longitude], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var kantorList = [
            { lat: -7.34388593350558, long: 112.73523239636584, nama: "Kantor Pusat CMS" },
            { lat: -7.344679449869948, long: 112.73472526289694, nama: "Gerbang Tol Menanggal" },
            { lat: -7.342730715106749, long: 112.75809102472155, nama: "Gerbang Tol Berbek 2" },
            { lat: -7.3470567753921845, long: 112.78926810447702, nama: "Gerbang Tol TambakSumur 1" },
            { lat: -7.346229427986888, long: 112.78391129687654, nama: "Gerbang Tol TambakSumur 2" },
            { lat: -7.357726813064598, long: 112.80496911781243, nama: "Gerbang Tol Juanda" },
            { lat: -7.497382601382557, long: 112.72027988527945, nama: "Test" },
            { lat: -7.32031997825219, long: 112.73802915918043, nama: "Test 1" }

        ];

        kantorList.forEach(k => {
            L.circle([k.lat, k.long], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.3,
                radius: 100
            }).addTo(map).bindPopup(k.nama);
        });

        L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
    }
    function errorCallback() {
        alert('Gagal mendapatkan lokasi Anda. Memuat peta di lokasi default (Kantor Pusat CMS).');

        var map = L.map('map', {
            attributionControl: false
        }).setView([-7.34388593350558, 112.73523239636584], 18);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
    }
    $("#takeabsen").click(function(e) {
        Webcam.snap(function(uri) {
            image = uri;
        });
        var lokasi = $("#lokasi").val();
        if (lokasi == "") {
            Swal.fire({
                title: 'Error!',
                text: 'Lokasi belum didapatkan. Coba lagi.',
                icon: 'error',
            });
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '/presensi/store',
            data: {
                _token: "{{ csrf_token() }}",
                image: image,
                lokasi: lokasi
            },
            cache: false,
            success: function(respond) {
                var status = respond.split("|");
                if (status[0] == "success") {
                    Swal.fire({
                        title: 'Success!',
                        text: status[1],
                        icon: 'success',
                    })
                    setTimeout("location.href='/dashboard'", 3000);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: status[1],
                        icon: 'error',
                    })
                }
            }
        });
    });
</script>
@endpush
