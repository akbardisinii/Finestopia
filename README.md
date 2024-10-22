# Finestopia - Sistem Manajemen Keuangan

![Finestopia](/public/landingpage.png)

![FinestopiaHome](/public/homepage.png)

## Tentang Finestopia

Finestopia adalah sistem manajemen keuangan yang inovatif, dirancang khusus untuk membantu Anda mengelola dan mengoptimalkan keuangan Anda. Aplikasi ini mendorong Anda untuk mengembangkan kebiasaan finansial yang sehat dan berkelanjutan.

Langkah pertama dalam perjalanan keuangan Anda dengan Finestopia adalah membuat anggaran yang realistis. Anda akan menetapkan jumlah pengeluaran yang direncanakan untuk berbagai kategori dalam periode waktu tertentu. Ini menjadi fondasi untuk pengelolaan keuangan Anda yang lebih baik.

Setelah anggaran ditetapkan, Finestopia membantu Anda melacak pemasukan dan pengeluaran Anda dengan mudah dan akurat. Anda dapat mencatat setiap transaksi dan melihat bagaimana pengeluaran Anda dibandingkan dengan anggaran yang telah ditetapkan.

Finestopia menyajikan laporan keuangan Anda dalam bentuk grafik interaktif yang mudah dipahami, didukung oleh teknologi Chart.js. Visualisasi data ini memungkinkan Anda untuk dengan cepat memahami pola pengeluaran, mengidentifikasi area penghematan potensial, dan membuat keputusan keuangan yang lebih cerdas.

## Fitur Utama

- Pembuatan dan pengelolaan anggaran
- Pelacakan pemasukan dan pengeluaran
- Visualisasi data keuangan dengan Chart.js
- Laporan keuangan yang detail dan mudah dipahami
- Keamanan data yang terjamin

## Aplikasi yang Diperlukan

- [NGINX](https://nginx.org/en/) atau web server lain untuk menjalankan kode PHP
- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)
- [VS Code](https://code.visualstudio.com/) atau editor teks/IDE lainnya
- [Git Bash](https://git-scm.com/downloads)

## Instalasi

1. Clone repositori ini:
   ```
   $ git clone https://github.com/akbardisinii/finestopia.git
   ```
2. Masuk ke direktori proyek:
   ```
   $ cd finestopia
   ```
3. Install dependensi:
   ```
   $ composer install
   ```
4. Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database
5. Generate key aplikasi:
   ```
   $ php artisan key:generate
   ```
6. Jalankan migrasi database:
   ```
   $ php artisan migrate
   ```
8. Jalankan server development:
   ```
   $ php artisan serve
   ```

## Teknologi yang Digunakan

- Laravel PHP Framework
- Tailwind CSS Framework
- Bootstrap CSS Framework
- Chart.js untuk visualisasi data
- jQuery
- Font Awesome untuk ikon
