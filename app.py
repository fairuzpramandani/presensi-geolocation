from flask import Flask, request, jsonify
from flask_cors import CORS
import face_recognition
import cv2
import numpy as np
import os
import time
import json
from PIL import Image

app = Flask(__name__)
CORS(app)

UPLOAD_FOLDER = 'debug_images'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# --- KONFIGURASI TINGKAT KEAMANAN ---
STRICT_THRESHOLD = 0.40  # Semakin kecil semakin ketat

# --- FUNGSI HELPER ---
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

# --- ROUTE UTAMA ---
@app.route('/validasi-wajah', methods=['POST'])
def validasi_wajah():
    if 'foto' not in request.files:
        return jsonify({'status': 'error', 'pesan': 'Tidak ada file foto'}), 400

    file = request.files['foto']
    action = request.form.get('action', 'register_center')
    target_embedding_raw = request.form.get('target_embedding')
    
    filename = f"strict_{action}_{int(time.time())}.jpg"
    save_path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(save_path)

    try:
        # 1. QUALITY CONTROL & LOGGING
        score_blur = check_blur_from_file(save_path)
        score_bright = check_brightness(save_path)
        
        # INI BAGIAN YANG MEMBUAT TERMINAL "CEREWET"
        print(f"\n--- ANALISIS WAJAH ({action}) ---")
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

        # Ambil Landmarks
        face_landmarks = face_recognition.face_landmarks(img, face_locations)[0]
        orientation = check_face_orientation(face_landmarks)

        # 2. LOGIKA ABSENSI (VERIFIKASI)
        if action == 'verify_face' and target_embedding_raw:
            
            # Cek apakah wajah menghadap lurus
            if orientation != "Center":
                print(f"GAGAL: Wajah miring ({orientation})")
                return jsonify({'status': 'gagal', 'pesan': 'Wajah miring. Harap hadap LURUS ke kamera.'})

            target_encoding = np.array(json.loads(target_embedding_raw))
            current_encoding = face_recognition.face_encodings(img, face_locations)[0]
            
            # --- HITUNG SKOR KEMIRIPAN ---
            distance = face_recognition.face_distance([target_encoding], current_encoding)[0]
            
            # PRINT HASIL KE TERMINAL
            print(f"JARAK WAJAH: {distance:.4f}")
            print(f"BATAS AMBANG: {STRICT_THRESHOLD}")

            # JIKA JARAK LEBIH KECIL DARI 0.40 -> BERARTI COCOK
            if distance <= STRICT_THRESHOLD:
                print(f"HASIL: COCOK (Jarak {distance:.4f})")
                return jsonify({'status': 'sukses', 'pesan': 'Wajah sesuai!', 'score': distance})
            else:
                print(f"HASIL: DITOLAK (Jarak {distance:.4f} > {STRICT_THRESHOLD})")
                return jsonify({'status': 'gagal', 'pesan': 'Wajah tidak cocok / berbeda dengan data.'})

        # 3. LOGIKA REGISTRASI
        status = check_face_status(face_landmarks)
        if action == 'register_center':
             print(f"Register: Encoding Wajah Berhasil")
             encoding = face_recognition.face_encodings(img, face_locations)[0]
             return jsonify({'status': 'sukses', 'face_encoding': encoding.tolist()})

        # Logika Gerakan (Liveness)
        print(f"DEBUG STATUS: {status}")
        return jsonify({'status': 'sukses', 'pesan': 'Gerakan Diterima.'})

    except Exception as e:
        print(f"ERROR SYSTEM: {e}")
        return jsonify({'status': 'error', 'pesan': 'Server Error.'}), 500
    finally:
        if os.path.exists(save_path): os.remove(save_path)

if __name__ == '__main__':
    # Debug=False agar log tidak double
    app.run(host='0.0.0.0', port=5000, debug=False)