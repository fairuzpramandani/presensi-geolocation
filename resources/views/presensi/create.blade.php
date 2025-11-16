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
    const kantorList = @json($lokasi_kantor_list);

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
        kantorList.forEach(k => {
            const [lat, long] = k.lokasi_kantor.split(',');
            const radius = k.radius || 100;

            L.circle([parseFloat(lat), parseFloat(long)], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.3,
                radius: parseInt(radius)
            }).addTo(map).bindPopup(k.nama_lokasi);
        });
        L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
             .bindPopup('Lokasi Anda Saat Ini').openPopup();
    }

    function errorCallback() {
        alert('Gagal mendapatkan lokasi Anda. Pastikan GPS aktif dan diizinkan.');
        const defaultLat = kantorList[0].lokasi_kantor.split(',')[0];
        const defaultLon = kantorList[0].lokasi_kantor.split(',')[1];

        var map = L.map('map', {
            attributionControl: false
        }).setView([defaultLat, defaultLon], 18);

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
