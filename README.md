# Online Quiz Application

Aplikasi Online Quiz sederhana yang dibangun menggunakan Laravel 11, sebagai bagian dari Technical Test PT Tigace Inspirasi Indonesia.

## Fitur Utama

### Manajemen Quiz (Administrator)
- Menampilkan daftar quiz (dengan Pagination & Pencarian).
- Menambahkan, mengubah, dan menghapus quiz (Soft Deletes).
- Mengelola soal untuk setiap quiz.
- Mendukung tipe soal Pilihan Ganda dan Essay.

### Pengerjaan Soal (Peserta)
- Melihat daftar quiz yang tersedia.
- Mengerjakan soal berdasarkan tipe (Pilihan Ganda & Essay).
- Melihat skor otomatis berdasarkan pilihan ganda. (Soal essay menunggu penilaian manual).

## Bonus Implementations & Optimasi
- **Validasi Input** menggunakan Form Requests.
- **Pagination & Search** pada dashboard Admin.
- **Soft Deletes** pada tabel Quiz untuk mencegah hilangnya data secara tidak sengaja.
- **Keamanan (Time Limit Enforcement)**: Validasi durasi dari *backend session* untuk mencegah manipulasi batas waktu.
- **Ketahanan (Mencegah Double Submit)**: Proteksi dengan *Cache Lock* (`Atomic Locks`) untuk mencegah form terkirim ganda saat koneksi tidak stabil.
- **Performa (Anti N+1 Query)**: Peningkatan performa pada kalkulasi nilai akhir (Eager loading otomatis pada *relational data*).
- **Activity Log (Audit Trail)**: Merekam setiap jejak aktivitas (*login/logout*, pembuatan soal, pengerjaan kuis) beserta alamat IP secara real-time yang dapat dipantau oleh Administrator (implementasi *custom model* tanpa *package* eksternal).
- **Unit & Feature Testing (Automated Tests)**: Telah dilengkapi dengan >30 Skenario Testing (*Admin/Peserta, Timeouts, Scoring, Security/ActivityLog*) memastikan sistem bekerja bebas *bug*.
- **Seeder & Factory**: Data dummy komprehensif untuk Quiz, Pertanyaan, Opsi, Administrator, dan Peserta.
## Persyaratan Sistem
- PHP 8.2 atau lebih tinggi
- Composer
- MySQL/MariaDB

## Cara Instalasi

1. Clone repository ini.
2. Masuk ke direktori project:
   ```bash
   cd online-quiz
   ```
3. Install dependencies melalui composer:
   ```bash
   composer install
   ```
4. Install dependencies NPM dan build assets:
   ```bash
   npm install && npm run build
   ```
5. Copy file `.env.example` ke `.env`:
   ```bash
   cp .env.example .env
   ```
6. Atur konfigurasi database di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=online_quiz
   DB_USERNAME=root
   DB_PASSWORD=
   ```
7. Generate App Key:
   ```bash
   php artisan key:generate
   ```
8. Jalankan Migrasi dan Seeder (untuk data awal):
   ```bash
   php artisan migrate:fresh --seed
   ```
9. Buat symbolic link untuk folder storage (digunakan untuk menampilkan gambar soal):
   ```bash
   php artisan storage:link
   ```
10. Jalankan local server:
   ```bash
   php artisan serve
   ```

## Akses Akun Dummy (Seeder)

Aplikasi telah disiapkan dengan 2 akun dummy:

**Administrator:**
- Email: `admin@example.com`
- Password: `password`

**Peserta:**
- Email: `peserta@example.com`
- Password: `password`

## Pengujian (Testing)
Untuk menjalankan tes otomatis, gunakan perintah:
```bash
php artisan test
```
