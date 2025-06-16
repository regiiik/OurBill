# OurBill

**OurBill** adalah proyek yang menggabungkan frontend berbasis Laravel dan backend berbasis Python untuk membuat aplikasi yang fungsional. Aplikasi ini dirancang untuk membantu mengelola tagihan dan melakukan tugas OCR (Optical Character Recognition).

### Struktur Proyek:
Repository ini berisi dua bagian utama:

1. **Frontend (Laravel)**
   - Folder **Laravel** berisi bagian frontend aplikasi, yang bertanggung jawab untuk antarmuka pengguna dan tampilan aplikasi. Dibangun menggunakan **Laravel**, sebuah framework PHP.
   - Frontend memungkinkan pengguna untuk berinteraksi dengan aplikasi, melihat tagihan mereka, dan menggunakan berbagai fitur.

2. **Backend (Python - Uvicorn)**
   - Folder **ocr-kw** berisi bagian backend aplikasi yang dijalankan menggunakan **Uvicorn**, server ASGI cepat untuk Python. Bagian ini menangani pemrosesan dan pengenalan data tagihan menggunakan teknologi **OCR**, yang membantu aplikasi membaca dan memahami teks dari dokumen atau gambar yang dipindai.

### Instruksi Instalasi:
Untuk menjalankan proyek ini secara lokal, Anda perlu mengikuti langkah-langkah berikut:

#### 1. Menyiapkan Frontend (Laravel):
   - Install **PHP** dan **Composer**.
   - Kloning repository ini ke mesin lokal Anda.
   - Arahkan ke folder Laravel dan jalankan perintah berikut:
     ```bash
     composer install
     php artisan serve
     ```
   - Ini akan memulai server Laravel, dan Anda dapat mengakses bagian frontend aplikasi di `http://localhost:8000`.

#### 2. Menyiapkan Backend (Python - Uvicorn):
   - Install **Python** dan **Uvicorn**.
   - Arahkan ke folder **ocr-kw** dan buat virtual environment:
     ```bash
     python -m venv venv
     ```
   - Aktifkan virtual environment:
     - Untuk Windows:
       ```bash
       venv\Scripts\activate
       ```
     - Untuk Mac/Linux:
       ```bash
       source venv/bin/activate
       ```
   - Install paket Python yang dibutuhkan:
     ```bash
     pip install -r requirements.txt
     ```
   - Mulai server dengan Uvicorn:
     ```bash
     uvicorn main:app --reload
     ```
   - Backend akan berjalan di `http://localhost:8001`.

### Teknologi yang Digunakan:
- **Frontend**: Laravel (PHP)
- **Backend**: Python, Uvicorn, OCR

### Lisensi:
Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.
