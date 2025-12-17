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
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>

    .webcam-capture {
        position: relative;
        width: 100% !important;
        height: calc(100vh - 56px) !important;
        overflow: hidden;
        margin: 0;
        border-radius: 0;
    }

    .webcam-capture video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        position: absolute;
        top: 0;
        left: 0;
    }
    .takephoto-button {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 85px;

        width: 70px;
        height: 70px;
        border-radius: 50%;

        background-color: rgba(0, 0, 0, 0.3) !important;
        backdrop-filter: blur(2px);

        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;

        border: 2px solid;
        outline: none;
        box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }

    .takephoto-button ion-icon {
        font-size: 40px;
    }
    .btn-absen-masuk {
        border-color: #ffffff;
        color: #ffffff;
    }
    .btn-absen-pulang {
        border-color: #ffffff;
        color: #ffffff;
        background-color: rgba(8, 8, 8, 0.2) !important;
    }

    .takephoto-button:active {
        transform: translateX(-50%) scale(0.90);
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
    #map {
        height: 200px;
        width: 100%;
        border-radius: 10px;
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .map-container {
        padding: 10px;
        background: #fff;
    }
</style>
@endsection

@section('content')
<div class="row" style="margin-top: 0;">
    <div class="col">
        <input type="hidden" id="lokasi">
       <div class="webcam-capture">
            <div class="shift-info">
                <ion-icon name="time-outline"></ion-icon>
                {{ $karyawan->nama_jam_kerja }} ({{ $karyawan->jam_masuk }} - {{ $karyawan->jam_pulang }})
            </div>
        </div>
        @php
            $ket = request()->get('ket');
        @endphp

        @if ($ket == 'out')
            <button id="takeabsen" class="takephoto-button btn-absen-pulang">
                <ion-icon name="camera-outline"></ion-icon>
            </button>
        @else
            <button id="takeabsen" class="takephoto-button btn-absen-masuk">
                <ion-icon name="camera-outline"></ion-icon>
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
    }
    $("#takeabsen").click(function(e) {
        Webcam.snap(function(uri) {
            image = uri;
        });
        var ket = "{{ request()->get('ket') }}";
        var lokasiVal = $("#lokasi").val();
        if (lokasiVal == "") {
            Swal.fire({
                title: 'Error!',
                text: 'Lokasi belum didapatkan. Tunggu sebentar atau aktifkan GPS.',
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
                lokasi: lokasiVal,
                ket: ket
            },
            cache: false,
            success: function(respond) {
                var status = respond.split("|");
                if (status[0] == "success") {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: status[1],
                        icon: 'success',
                    })
                    setTimeout("location.href='/dashboard'", 3000);
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: status[1],
                        icon: 'error',
                    })
                }
            }
        });
    });
</script>
@endpush
