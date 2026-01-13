from flask import Flask, request, jsonify
from flask_cors import CORS
import face_recognition
import cv2
import numpy as np
import os
import time
from PIL import Image

app = Flask(__name__)
CORS(app)

# Folder Debug
UPLOAD_FOLDER = 'debug_images'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# --- FUNGSI UTILITAS ---

def check_blur_from_file(file_path):
    try:
        img = cv2.imread(file_path, cv2.IMREAD_GRAYSCALE)
        if img is None: return 0
        return cv2.Laplacian(img, cv2.CV_64F).var()
    except:
        return 0

def check_brightness(file_path):
    try:
        img = cv2.imread(file_path)
        lab = cv2.cvtColor(img, cv2.COLOR_BGR2LAB)
        l, a, b = cv2.split(lab)
        return np.mean(l)
    except:
        return 0

def get_eye_aspect_ratio(eye_points):
    """
    Menghitung Eye Aspect Ratio (EAR) untuk mendeteksi kedipan.
    EAR = (jarak_vertikal1 + jarak_vertikal2) / (2 * jarak_horizontal)
    """
    # Titik mata (landmark face_recognition memberikan list titik berurutan)
    # P1, P2, P3, P4, P5, P6
    # Vertical 1: P2 ke P6
    # Vertical 2: P3 ke P5
    # Horizontal: P1 ke P4

    A = np.linalg.norm(np.array(eye_points[1]) - np.array(eye_points[5]))
    B = np.linalg.norm(np.array(eye_points[2]) - np.array(eye_points[4]))
    C = np.linalg.norm(np.array(eye_points[0]) - np.array(eye_points[3]))

    if C == 0: return 0
    ear = (A + B) / (2.0 * C)
    return ear

def check_face_status(face_landmarks):
    """
    Mendeteksi:
    1. Arah Wajah (Kiri/Kanan/Center)
    2. Status Mata (Terbuka/Tertutup)
    """
    status = {'direction': 'unknown', 'ratio': 0, 'eyes_closed': False, 'ear': 0}

    try:
        # --- 1. CEK ARAH WAJAH ---
        nose_bridge = face_landmarks['nose_bridge']
        left_eye = face_landmarks['left_eye']
        right_eye = face_landmarks['right_eye']

        left_eye_center = np.mean(left_eye, axis=0)
        right_eye_center = np.mean(right_eye, axis=0)
        nose_center = np.mean(nose_bridge, axis=0)

        dist_left = np.linalg.norm(left_eye_center - nose_center)
        dist_right = np.linalg.norm(right_eye_center - nose_center)

        if dist_right != 0:
            ratio = dist_left / dist_right
            status['ratio'] = ratio
            if ratio < 0.5: status['direction'] = 'right'
            elif ratio > 2.0: status['direction'] = 'left'
            elif 0.6 <= ratio <= 1.6: status['direction'] = 'center'

        # --- 2. CEK MATA (EAR) ---
        ear_left = get_eye_aspect_ratio(left_eye)
        ear_right = get_eye_aspect_ratio(right_eye)
        avg_ear = (ear_left + ear_right) / 2.0
        status['ear'] = avg_ear

        # Threshold Kedip: Biasanya di bawah 0.20 atau 0.25 artinya merem
        if avg_ear < 0.21:
            status['eyes_closed'] = True

        return status

    except:
        return status

# --- ENDPOINT UTAMA ---

@app.route('/validasi-wajah', methods=['POST'])
def validasi_wajah():
    if 'foto' not in request.files:
        return jsonify({'status': 'error', 'pesan': 'Tidak ada file foto'}), 400

    file = request.files['foto']

    # Action: register_center, check_liveness_left, check_liveness_right, check_liveness_blink
    requested_action = request.form.get('action', 'register_center')
    user_id = request.form.get('user_id', 'unknown')

    # Simpan File
    safe_user_id = "".join([c for c in user_id if c.isalnum() or c in ('@', '.', '_', '-')])
    filename = f"{safe_user_id}_{requested_action}_{int(time.time())}.jpg"
    save_path = os.path.join(UPLOAD_FOLDER, filename)

    print(f"DEBUG: Action: {requested_action}")

    try:
        file.save(save_path)
    except:
        return jsonify({'status': 'error', 'pesan': 'Gagal simpan file'}), 500

    # Cek Blur & Brightness (Standard)
    score_blur = check_blur_from_file(save_path)
    if score_blur < 35.0:
        try: os.remove(save_path)
        except: pass
        return jsonify({'status': 'gagal', 'pesan': 'Foto buram.'})

    score_bright = check_brightness(save_path)
    if score_bright < 40:
        try: os.remove(save_path)
        except: pass
        return jsonify({'status': 'gagal', 'pesan': 'Terlalu gelap.'})

    # PROSES GEOMETRI
    try:
        img_cv = cv2.imread(save_path)
        rgb_image = cv2.cvtColor(img_cv, cv2.COLOR_BGR2RGB)
        clean_image = np.array(Image.fromarray(rgb_image))

        face_locations = face_recognition.face_locations(clean_image)
        if len(face_locations) == 0:
            return jsonify({'status': 'gagal', 'alasan': 'no_face', 'pesan': 'Wajah tidak ditemukan.'})

        face_landmarks_list = face_recognition.face_landmarks(clean_image, face_locations)

        if len(face_landmarks_list) > 0:
            status = check_face_status(face_landmarks_list[0])
            print(f"DEBUG STATUS: {status}")

            # --- LOGIKA TANTANGAN ---
            if requested_action == 'check_liveness_left':
                if status['direction'] == 'left':
                    return jsonify({'status': 'sukses', 'pesan': 'Gerakan Kiri OK!'})
                else: return jsonify({'status': 'gagal', 'pesan': 'Mohon toleh ke KIRI.'})

            elif requested_action == 'check_liveness_right':
                if status['direction'] == 'right':
                    return jsonify({'status': 'sukses', 'pesan': 'Gerakan Kanan OK!'})
                else: return jsonify({'status': 'gagal', 'pesan': 'Mohon toleh ke KANAN.'})

            elif requested_action == 'check_liveness_blink':
                if status['eyes_closed']:
                    return jsonify({'status': 'sukses', 'pesan': 'Kedipan Terdeteksi!'})
                else:
                    # Jika mata terbuka (EAR > 0.21)
                    return jsonify({'status': 'gagal', 'pesan': 'Mohon KEDIPKAN mata anda.'})

            elif requested_action == 'register_center':
                if status['direction'] != 'center':
                    return jsonify({'status': 'gagal', 'pesan': 'Mohon hadap LURUS.'})
                if status['eyes_closed']: # Pastikan pas foto utama matanya melek
                    return jsonify({'status': 'gagal', 'pesan': 'Jangan memejamkan mata.'})

    except Exception as e:
        print(f"ERROR: {e}")
        return jsonify({'status': 'error', 'pesan': 'Gagal proses.'}), 500

    # --- ENCODING (Hanya untuk register_center) ---
    if requested_action != 'register_center':
         return jsonify({'status': 'sukses', 'pesan': 'Gerakan OK'})

    try:
        face_encoding = face_recognition.face_encodings(clean_image, known_face_locations=face_locations)[0]
        return jsonify({'status': 'sukses', 'face_encoding': face_encoding.tolist()})
    except:
        return jsonify({'status': 'error', 'pesan': 'Gagal encoding.'}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
