# Sistem Manajemen Inventori (TALL Stack)

Sistem Manajemen Inventori skala enterprise yang dibangun menggunakan **Laravel 11, Filament v3, Tailwind CSS, dan MySQL**. Aplikasi ini dirancang untuk mencatat pergerakan barang, menganalisis ketersediaan stok, hingga memprediksi kebutuhan restock barang di multi-gudang.

## 🚀 Fitur Utama
- **Manajemen Katalog & Multi-Gudang**: Kelola data barang dan distribusikan stoknya di berbagai gudang berbeda.
- **Low Stock Alert**: Peringatan otomatis apabila stok barang berada di bawah batas minimum (ditandai dengan _badge_ merah).
- **Scanner QR Code**: Generate QR Code secara otomatis untuk setiap SKU, dan gunakan fitur _Kamera Scanner_ langsung di dalam aplikasi untuk melakukan mutasi (Barang Masuk/Keluar).
- **Stock Transfer**: Pindahkan stok antar gudang dengan pencatatan otomatis yang akurat.
- **Opname Stok (Stock Take)**: Pencocokan stok fisik dan sistem. Selisih akan otomatis di-adjust.
- **RBAC (Role Based Access Control)**: Memisahkan hak akses antara `Admin` (Bebas akses) dan `Staff Gudang` (Terbatas).
- **Dashboard & Analitik Visual**:
  - Tren Mutasi Stok (Line Chart)
  - Distribusi per Kategori (Pie Chart)
  - Top 5 Barang Fast-Moving (Bar Chart)
  - Deteksi "Dead Stock" (Tabel peringatan barang mengendap)
- **Security & Reliability**:
  - **Audit Trail**: Merekam aktivitas user (Login, Logout, Gagal Login, Edit Data) beserta IP dan User Agent.
  - **Soft Deletes**: Data krusial yang terhapus tidak hilang permanen, bisa di-restore.
  - **Export & Import (CSV/Excel)**: Mendukung bulk-import barang dan eksport riwayat mutasi untuk pelaporan.

## 🛠️ Prasyarat
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL / MariaDB

## 📦 Panduan Instalasi (Development)
1. Clone repositori ini:
   ```bash
   git clone https://github.com/username/Sistem-Manajemen-Inventaris.git
   cd Sistem-Manajemen-Inventaris
   ```
2. Salin konfigurasi environment dan sesuaikan kredensial database Anda:
   ```bash
   cp .env.example .env
   ```
   *(Pastikan `DB_CONNECTION=mysql` dan isi `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dengan benar).*

3. Install dependensi PHP dan Node:
   ```bash
   composer install
   npm install && npm run build
   ```

4. Generate Key Aplikasi:
   ```bash
   php artisan key:generate
   ```

5. Jalankan Migrasi dan Seeder (Untuk mendapatkan data Dummy 100 barang dan akun admin):
   ```bash
   php artisan migrate:fresh --seed
   php artisan db:seed --class=DummyDataSeeder
   ```

6. Jalankan Server:
   ```bash
   php artisan serve
   ```

## 🔑 Akun Uji Coba (Dummy)
Jika Anda menggunakan seeder bawaan, Anda dapat login ke `http://localhost:8000/admin` menggunakan kredensial:
- **Admin**: `admin@inventory.test` | Password: `password`
- **Staff Gudang**: `staff@inventory.test` | Password: `password`

---
Dibuat dengan ❤️ menggunakan Laravel & FilamentPHP.
