from flask import Flask, request, jsonify
from flask_cors import CORS
import face_recognition
import cv2
import numpy as np
import os
import time
import json
import requests
from PIL import Image

app = Flask(__name__)
CORS(app)

UPLOAD_FOLDER = 'debug_images'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# Threshold kecocokan wajah (Semakin rendah semakin ketat)
STRICT_THRESHOLD = 0.40

def check_blur_from_file(file_path):
    try:
        img = cv2.imread(file_path, cv2.IMREAD_GRAYSCALE)
        if img is None: return 0
        return cv2.Laplacian(img, cv2.CV_64F).var()
    except: return 0

def check_brightness(file_path):
    try:
        img = cv2.imread(file_path)
        if img is None: return 0
        return np.mean(cv2.split(cv2.cvtColor(img, cv2.COLOR_BGR2LAB))[0])
    except: return 0

def is_fake_attack(file_path):
    """
    Logika Anti-Spoofing Sederhana:
    Mengecek apakah gambar kemungkinan besar berasal dari layar HP (Foto dalam Foto).
    Layar digital biasanya memiliki tekstur Moire atau distribusi cahaya yang tidak natural.
    """
    try:
        img = cv2.imread(file_path)
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

        # 1. Analisis Frekuensi (Moire Pattern)
        laplacian_var = cv2.Laplacian(gray, cv2.CV_64F).var()

        # 2. Analisis Histogram
        # Layar HP cenderung memiliki puncak warna yang sangat kontras di area tertentu
        hist = cv2.calcHist([gray], [0], None, [256], [0, 256])
        peak_ratio = np.max(hist) / np.sum(hist)

        # Jika varians terlalu rendah (gambar terlalu smooth/flat)
        # atau peak ratio terlalu tinggi (warna tidak natural seperti layar), indikasi fake.
        if laplacian_var < 100 and peak_ratio > 0.15:
            return True
        return False
    except:
        return False

def get_eye_aspect_ratio(eye_points):
    A = np.linalg.norm(np.array(eye_points[1]) - np.array(eye_points[5]))
    B = np.linalg.norm(np.array(eye_points[2]) - np.array(eye_points[4]))
    C = np.linalg.norm(np.array(eye_points[0]) - np.array(eye_points[3]))
    return (A + B) / (2.0 * C) if C != 0 else 0

def check_face_orientation(face_landmarks):
    try:
        nose_tip = face_landmarks['nose_tip']
        left_eye = np.mean(face_landmarks['left_eye'], axis=0)
        right_eye = np.mean(face_landmarks['right_eye'], axis=0)
        nose = np.mean(nose_tip, axis=0)

        dist_l = np.linalg.norm(left_eye - nose)
        dist_r = np.linalg.norm(right_eye - nose)
        ratio = dist_l / dist_r

        if ratio > 1.5: return "Toleh Kanan"
        if ratio < 0.6: return "Toleh Kiri"
        return "Center"
    except:
        return "Unknown"

@app.route('/validasi-wajah', methods=['POST'])
def validasi_wajah():
    if 'foto' not in request.files:
        return jsonify({'status': 'error', 'pesan': 'Tidak ada file foto'}), 400

    file = request.files['foto']
    action = request.form.get('action', 'register_center')
    email_user = request.form.get('email', 'Unknown Email')
    nama_user = request.form.get('nama', 'User')

    filename = f"strict_{action}_{int(time.time())}.jpg"
    save_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(save_path)

    try:
        score_blur = check_blur_from_file(save_path)
        score_bright = check_brightness(save_path)

        print(f"\n--- ANALISIS WAJAH ({action}) ---")
        print(f"User Login: {nama_user} ({email_user})")

        # 1. CEK KUALITAS (BLUR & TERANG)
        if score_blur < 15.0:
            return jsonify({'status': 'gagal', 'pesan': 'Foto Buram. Pastikan kamera fokus.'})
        if score_bright < 30:
            return jsonify({'status': 'gagal', 'pesan': 'Terlalu Gelap. Cari tempat terang.'})

        # 2. CEK ANTI-SPOOFING (CEK FOTO HP/LAYAR)
        if is_fake_attack(save_path):
            print("KECURANGAN TERDETEKSI: Presentation Attack (Foto di dalam Layar)")
            return jsonify({'status': 'gagal', 'pesan': 'Kecurangan terdeteksi! Gunakan wajah asli, bukan foto/layar HP.'})

        # 3. DETEKSI WAJAH
        img = face_recognition.load_image_file(save_path)
        face_locations = face_recognition.face_locations(img)

        if len(face_locations) == 0:
            return jsonify({'status': 'gagal', 'pesan': 'Wajah tidak terdeteksi. Pastikan wajah terlihat jelas.'})
        if len(face_locations) > 1:
            return jsonify({'status': 'gagal', 'pesan': 'Terdeteksi lebih dari 1 orang.'})

        face_landmarks = face_recognition.face_landmarks(img, face_locations)[0]
        orientation = check_face_orientation(face_landmarks)

        # 4. PROSES VERIFIKASI (1:N)
        if action == 'verify_face':
            if orientation != "Center":
                return jsonify({'status': 'gagal', 'pesan': 'Wajah miring. Harap hadap lurus ke kamera.'})

            current_encoding = face_recognition.face_encodings(img, face_locations)[0]

            print("Mencocokkan dengan database...")
            try:
                # Gunakan IP lokal PC Anda (192.168.100.8) agar stabil
                url_get_wajah = "http://127.0.0.1:8000/api/semua-wajah"
                response = requests.get(url_get_wajah, timeout=10)
                semua_karyawan = response.json()
            except Exception as e:
                return jsonify({'status': 'error', 'pesan': 'Server Laravel tidak merespon.'}), 500

            best_match_email = ""
            best_match_name = "Tidak Dikenal"
            lowest_distance = 1.0

            for karyawan in semua_karyawan:
                if karyawan.get('face_embedding'):
                    try:
                        db_encoding = np.array(json.loads(karyawan['face_embedding']))
                        dist = face_recognition.face_distance([db_encoding], current_encoding)[0]
                        if dist < lowest_distance:
                            lowest_distance = dist
                            best_match_name = karyawan['nama_lengkap']
                            best_match_email = karyawan['email']
                    except: continue

            print(f"Hasil Compare: {best_match_name} (Dist: {lowest_distance:.4f})")

            # CEK APAKAH WAJAH COCOK DENGAN AKUN YANG LOGIN
            if lowest_distance <= STRICT_THRESHOLD:
                if best_match_email == email_user:
                    return jsonify({'status': 'sukses', 'pesan': 'Presensi Berhasil!'})
                else:
                    # KECURANGAN: Wajah Orang Lain (Titip Absen)
                    pesan_alert = f"Indikasi titip absen! Akun {nama_user} mencoba absen menggunakan wajah {best_match_name}."
                    try:
                        requests.post("http://127.0.0.1:8000/api/lapor-kecurangan",
                                      data={'email': email_user, 'pesan': pesan_alert}, timeout=3)
                    except: pass
                    return jsonify({'status': 'gagal', 'pesan': f'Wajah Tidak Sesuai! Terdeteksi sebagai {best_match_name}.'})
            else:
                # WAJAH ASING
                return jsonify({'status': 'gagal', 'pesan': 'Wajah tidak dikenali atau belum terdaftar.'})

        # PROSES REGISTER
        if action == 'register_center':
             encoding = face_recognition.face_encodings(img, face_locations)[0]
             return jsonify({'status': 'sukses', 'face_encoding': encoding.tolist()})

        return jsonify({'status': 'sukses', 'pesan': 'Wajah Terverifikasi.'})

    except Exception as e:
        print(f"SYSTEM ERROR: {e}")
        return jsonify({'status': 'error', 'pesan': 'Internal Server Error.'}), 500
    finally:
        if os.path.exists(save_path): os.remove(save_path)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)
