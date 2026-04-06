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

def check_face_status(face_landmarks):
    status = {'direction': 'unknown', 'eyes_closed': False}
    status['direction'] = check_face_orientation(face_landmarks)
    ear = (get_eye_aspect_ratio(face_landmarks['left_eye']) + get_eye_aspect_ratio(face_landmarks['right_eye'])) / 2.0
    status['eyes_closed'] = ear < 0.21
    return status

@app.route('/validasi-wajah', methods=['POST'])
def validasi_wajah():
    if 'foto' not in request.files:
        return jsonify({'status': 'error', 'pesan': 'Tidak ada file foto'}), 400

    file = request.files['foto']
    action = request.form.get('action', 'register_center')
    target_embedding_raw = request.form.get('target_embedding')

    # Tangkap email dan nama dari Flutter untuk keperluan Log
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
        print(f"Blur Score: {score_blur:.2f} (Min 15)")
        print(f"Bright Score: {score_bright:.2f} (Min 30)")

        if score_blur < 15.0:
            print("GAGAL: Foto Buram")
            return jsonify({'status': 'gagal', 'pesan': 'Foto Buram. Pegang HP dengan stabil.'})
        if score_bright < 30:
            print("GAGAL: Foto Gelap")
            return jsonify({'status': 'gagal', 'pesan': 'Terlalu Gelap. Cari tempat terang.'})

        img = face_recognition.load_image_file(save_path)
        face_locations = face_recognition.face_locations(img)

        if len(face_locations) == 0:
            print("GAGAL: Wajah tidak ditemukan")
            return jsonify({'status': 'gagal', 'pesan': 'Wajah tidak ditemukan.'})

        if len(face_locations) > 1:
            print("GAGAL: Banyak wajah")
            return jsonify({'status': 'gagal', 'pesan': 'Terdeteksi lebih dari 1 wajah. Harap sendiri.'})

        face_landmarks = face_recognition.face_landmarks(img, face_locations)[0]
        orientation = check_face_orientation(face_landmarks)

        if action == 'verify_face':

            if orientation != "Center":
                print(f"GAGAL: Wajah miring ({orientation})")
                return jsonify({'status': 'gagal', 'pesan': 'Wajah miring. Harap hadap LURUS ke kamera.'})

            current_encoding = face_recognition.face_encodings(img, face_locations)[0]

            print("Mengambil data seluruh wajah dari server (1:N)...")
            try:
                url_get_wajah = "http://127.0.0.1:8000/api/semua-wajah"
                response = requests.get(url_get_wajah, timeout=5)
                semua_karyawan = response.json()
            except Exception as e:
                print(f"Gagal konek ke Laravel: {e}")
                return jsonify({'status': 'error', 'pesan': 'Gagal mengambil data dari server.'}), 500

            best_match_name = "Tidak Dikenal"
            best_match_email = ""
            lowest_distance = 1.0 # Set nilai maksimal

            # Looping untuk mencari tersangka yang paling mirip
            for karyawan in semua_karyawan:
                if karyawan.get('face_embedding'):
                    try:
                        db_encoding = np.array(json.loads(karyawan['face_embedding']))
                        dist = face_recognition.face_distance([db_encoding], current_encoding)[0]

                        # Simpan jika jaraknya lebih dekat dari rekor sebelumnya
                        if dist < lowest_distance:
                            lowest_distance = dist
                            best_match_name = karyawan['nama_lengkap']
                            best_match_email = karyawan['email']
                    except Exception as e:
                        print(f"Error parse array: {e}")
                        continue

            print(f"JARAK TERDEKAT: {lowest_distance:.4f} dengan {best_match_name}")

            if lowest_distance <= STRICT_THRESHOLD:
                if best_match_email == email_user:
                    print(f"HASIL: COCOK! Ini benar {nama_user}")
                    return jsonify({'status': 'sukses', 'pesan': 'Wajah sesuai!', 'score': lowest_distance})
                else:
                    print(f"KECURANGAN: Akun {nama_user} dipakai oleh {best_match_name}")
                    pesan_alert = f"Indikasi titip absen! Akun {nama_user} mencoba absen, namun sistem mendeteksi wajah tersebut adalah {best_match_name}."

                    try:
                        url_laravel = "http://127.0.0.1:8000/api/lapor-kecurangan"
                        payload = {'email': email_user, 'pesan': pesan_alert}
                        requests.post(url_laravel, data=payload, timeout=3)
                    except: pass

                    return jsonify({'status': 'gagal', 'pesan': f'Akses ditolak! Anda terdeteksi sebagai {best_match_name}.'})
            else:
                print("HASIL: DITOLAK. Wajah tidak dikenal.")
                pesan_alert = f"Peringatan! Akun {nama_user} mencoba absen menggunakan wajah yang Tidak Dikenal dalam sistem."

                try:
                    url_laravel = "http://127.0.0.1:8000/api/lapor-kecurangan"
                    payload = {'email': email_user, 'pesan': pesan_alert}
                    requests.post(url_laravel, data=payload, timeout=3)
                except: pass

                return jsonify({'status': 'gagal', 'pesan': 'Wajah tidak dikenali dalam sistem.'})

        status = check_face_status(face_landmarks)
        if action == 'register_center':
             print(f"Register: Encoding Wajah Berhasil")
             encoding = face_recognition.face_encodings(img, face_locations)[0]
             return jsonify({'status': 'sukses', 'face_encoding': encoding.tolist()})

        print(f"DEBUG STATUS: {status}")
        return jsonify({'status': 'sukses', 'pesan': 'Gerakan Diterima.'})

    except Exception as e:
        print(f"ERROR SYSTEM: {e}")
        return jsonify({'status': 'error', 'pesan': 'Server Error.'}), 500
    finally:
        if os.path.exists(save_path): os.remove(save_path)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)
