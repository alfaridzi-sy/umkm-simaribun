# Sistem UMKM2M

Sistem UMKM2M adalah platform yang memfasilitasi interaksi antara admin, UMKM (Usaha Mikro, Kecil, dan Menengah), dan pembeli. Sistem ini memungkinkan admin untuk mendaftarkan akun UMKM, UMKM untuk menambahkan item yang ingin dijual, dan pembeli untuk memilih dan memesan item.

## Fitur Utama

**Pendaftaran Akun UMKM**
    - Admin dapat mendaftarkan akun UMKM baru.
    - Setiap UMKM akan memiliki akses untuk mengelola data item yang dijual.

**Manajemen Item**
    - UMKM dapat menambahkan, mengedit, dan menghapus item yang ingin dijual.
    - Item dapat mencakup nama, deskripsi, harga, dan gambar.

**Pemilihan dan Pemesanan Item**
    - Pembeli dapat melihat daftar item yang tersedia.
    - Pembeli dapat memilih item yang diinginkan dan melakukan pemesanan.

## Instalasi

**Clone Repository**
    git clone https://github.com/alfaridzi-sy/umkm-simarimbun.git

**Install Dependensi**
    Pastikan Anda telah menginstal semua dependensi yang diperlukan. Jika Anda menggunakan composer:
    - cd umkm-simarimbun
    - composer install

**Konfigurasi Database**
    Konfigurasikan database sesuai dengan pengaturan sistem Anda. Edit file konfigurasi yang diperlukan.

**Menjalankan Aplikasi**
    Setelah semua konfigurasi selesai, jalankan aplikasi dengan:
    - php artisan key:generate --ansi
    - php artisan migrate --seed 
    - php artisan serve

## Penggunaan

**Login Sebagai Admin**
	- Akses halaman admin untuk mendaftarkan UMKM.
	- Setelah pendaftaran, admin dapat mengelola data UMKM dan item.
**Login sebagai UMKM**
	- UMKM dapat login dan mulai menambahkan item mereka ke dalam sistem.
	- Mereka dapat mengelola item yang terdaftar, termasuk mengubah harga dan deskripsi.
**Login sebagai Pembeli**
    - Pembeli dapat menelusuri item yang tersedia.
	- Mereka dapat memilih item, menambahkan ke keranjang, dan melakukan pemesanan.

## Struktur Direktori
	- /config - File konfigurasi
	- /public - File statis, seperti gambar dan CSS
	- /resources/views - Template tampilan

## Kontribusi

Jika Anda ingin berkontribusi pada proyek ini, silakan buat pull request atau buka isu untuk diskusi lebih lanjut.
