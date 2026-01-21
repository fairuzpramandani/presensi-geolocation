@extends('layouts.presensi')

@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" onclick="forceLogout()" class="headerButton">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Registrasi Wajah</div>
    <div class="right"></div>
</div>
@endsection

@section('content')
<style>
    .appBottomMenu, .bottom-nav, .navbar, nav { display: none !important; }
    body { background-color: #1a1a1a !important; overflow: hidden; }
    .row-camera { position: fixed; top: 56px; left: 0; width: 100%; height: calc(100vh - 56px); background: #000; z-index: 99; margin: 0; padding: 0; }
    .camera-container { position: relative; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; overflow: hidden; }
    video#camera-stream { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }

    .camera-container::after {
        content: ""; position: absolute; top: 45%; left: 50%; transform: translate(-50%, -50%);
        width: 280px; height: 380px;
        border: 3px solid rgba(255, 255, 255, 0.7);
        border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.7);
        pointer-events: none; z-index: 10; transition: border-color 0.3s;
    }
    .camera-container.success::after { border-color: #2ecc71; }
    .camera-container.error::after { border-color: #e74c3c; }

    .instruction-box { position: absolute; top: 10%; width: 90%; text-align: center; z-index: 20; color: white; }
    .instruction-title { font-size: 20px; font-weight: bold; text-shadow: 0 2px 4px black; margin-bottom: 5px; }
    .instruction-desc { font-size: 14px; opacity: 0.9; text-shadow: 0 1px 2px black; }

    .capture-btn-container { position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); z-index: 999; }
    .btn-shutter {
        width: 75px; height: 75px; background-color: white; border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.3); cursor: pointer; display: flex; justify-content: center; align-items: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5); transition: transform 0.1s;
    }
    .btn-shutter:active { transform: scale(0.9); }
    .btn-shutter ion-icon { font-size: 32px; color: #333; }

    .step-indicator { position: absolute; top: 20px; right: 20px; z-index: 30; display: flex; gap: 8px; }
    .dot { width: 10px; height: 10px; background: rgba(255,255,255,0.3); border-radius: 50%; }
    .dot.active { background: #2ecc71; box-shadow: 0 0 8px #2ecc71; }

    #loading-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.85); z-index: 1000; display: none; justify-content: center; align-items: center; flex-direction: column; color: white; }
</style>

<div class="row row-camera">
    <div class="col p-0">
        <div class="camera-container" id="cam-container">
            <div class="step-indicator">
                <div class="dot active" id="dot-1"></div>
                <div class="dot" id="dot-2"></div>
                <div class="dot" id="dot-3"></div>
                <div class="dot" id="dot-4"></div>
            </div>

            <div class="instruction-box">
                <div class="instruction-title" id="text-title">TAHAP 1: FOTO DEPAN</div>
                <div class="instruction-desc" id="text-desc">Posisikan wajah lurus di dalam bingkai</div>
            </div>

            <video id="camera-stream" autoplay playsinline></video>
            <canvas id="canvas-processor" style="display: none;"></canvas>

            <div id="loading-overlay">
                <div class="spinner-border text-light mb-2" role="status"></div>
                <h4 id="loading-text">Memproses...</h4>
            </div>

            <div class="capture-btn-container" id="shutter-container">
                <button type="button" class="btn-shutter" onclick="processStep()">
                    <ion-icon name="camera"></ion-icon>
                </button>
            </div>
        </div>

        <form action="{{ route('face.store') }}" method="POST" id="form-enroll">
            @csrf
            <input type="hidden" name="image" id="final-image">
            <input type="hidden" name="user_id" value="{{ Auth::user()->email }}">
        </form>
        <form action="{{ route('karyawan.logout') }}" method="POST" id="logout-form" style="display: none;">@csrf</form>
    </div>
</div>

<script>
    const PYTHON_API_URL = "http://127.0.0.1:5000/validasi-wajah";
    const USER_ID = "{{ Auth::user()->email }}";
    const video = document.getElementById('camera-stream');
    const canvas = document.getElementById('canvas-processor');
    const camContainer = document.getElementById('cam-container');
    const title = document.getElementById('text-title');
    const desc = document.getElementById('text-desc');
    const loadingOverlay = document.getElementById('loading-overlay');
    const loadingText = document.getElementById('loading-text');

    let currentStep = 1;
    let mainPhotoBase64 = null;

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: "user" } });
            video.srcObject = stream;
        } catch (err) { alert("Gagal akses kamera: " + err); }
    }
    startCamera();

    async function processStep() {
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0); ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const base64Img = canvas.toDataURL('image/jpeg', 0.9);
        const blob = await (await fetch(base64Img)).blob();
        const formData = new FormData();
        formData.append('foto', blob, 'capture.jpg');
        formData.append('user_id', USER_ID);
        let action = 'register_center';
        if (currentStep === 2) action = 'check_liveness_left';
        if (currentStep === 3) action = 'check_liveness_right';
        if (currentStep === 4) action = 'check_liveness_blink';
        formData.append('action', action);

        loadingOverlay.style.display = 'flex';
        loadingText.innerText = "Mengecek...";

        try {
            const response = await fetch(PYTHON_API_URL, { method: 'POST', body: formData });
            const result = await response.json();

            if (result.status === 'sukses') {
                handleSuccess(result);
            } else {
                handleError(result.pesan);
            }
        } catch (error) {
            handleError("Gagal koneksi ke app.py");
        }
    }

    function handleSuccess(result) {
        camContainer.classList.add('success');
        setTimeout(() => camContainer.classList.remove('success'), 1000);

        if (currentStep === 1) {
            mainPhotoBase64 = canvas.toDataURL('image/jpeg', 0.9);
            currentStep = 2;
            updateUI("TAHAP 2: TOLEH KIRI", "Mohon tolehkan wajah ke arah KIRI", 2);
            showNext();
        } else if (currentStep === 2) {
            currentStep = 3;
            updateUI("TAHAP 3: TOLEH KANAN", "Mohon tolehkan wajah ke arah KANAN", 3);
            showNext();
        } else if (currentStep === 3) {
            currentStep = 4;
            updateUI("TAHAP 4: KEDIPKAN MATA", "Tekan tombol saat mata TERPEJAM", 4);
            showNext();
        } else if (currentStep === 4) {
            loadingText.innerText = "Registrasi Berhasil!";
            document.getElementById('final-image').value = mainPhotoBase64;
            document.getElementById('form-enroll').submit();
        }
    }

    function showNext() {
        loadingOverlay.style.display = 'none';
    }

    function handleError(pesan) {
        loadingOverlay.style.display = 'none';
        camContainer.classList.add('error');
        title.innerText = "GAGAL"; desc.innerText = pesan; desc.style.color = "#ff4d4d";
        setTimeout(() => {
            camContainer.classList.remove('error');
            desc.style.color = "white";
            if(currentStep === 1) updateUI("TAHAP 1: FOTO DEPAN", "Posisikan wajah lurus", 1);
            if(currentStep === 2) updateUI("TAHAP 2: TOLEH KIRI", "Mohon toleh ke KIRI", 2);
            if(currentStep === 3) updateUI("TAHAP 3: TOLEH KANAN", "Mohon toleh ke KANAN", 3);
            if(currentStep === 4) updateUI("TAHAP 4: KEDIPKAN MATA", "Tekan tombol saat mata TERPEJAM", 4);
        }, 2000);
    }

    function updateUI(judul, deskripsi, stepNum) {
        title.innerText = judul; desc.innerText = deskripsi;
        document.querySelectorAll('.dot').forEach(d => d.classList.remove('active'));
        document.getElementById(`dot-${stepNum}`).classList.add('active');
    }

    function forceLogout() {
        if(confirm('Batalkan dan keluar?')) document.getElementById('logout-form').submit();
    }
</script>
@endsection
