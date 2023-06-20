# Instalasi

1. Pastikan anda sudah menginstall Composer dan Node.js sebelum menginstall aplikasi ini. Jika belum, mohon untuk mengunduh terlebih dahulu, lalu install. Pastikan juga anda sudah menginstall XAMPP atau aplikasi sejenis.\
Composer: \
https://getcomposer.org/download/ \
Node.js: \
https://nodejs.org/en/download \
XAMPP : \
https://www.apachefriends.org/download.html
2. Jika sudah, download atau clone repo ini dengan cara mengetikkan perintah di terminal `git clone https://github.com/hagicaaa/bigbang-service.git`. Untuk melakukan clone, pastikan git sudah terinstall di komputer anda. Jika belum, anda dapat mendownload git pada link https://git-scm.com/downloads \
Atau jika tidak ingin menginstall git, anda dapat mendownload file seperti biasa dengan cara klik Code -> Download ZIP.
3. Setelah itu, buka folder `bigbang-service`, buka terminal di folder tersebut, lalu ketikkan `composer install` untuk menginstall library yang dibutuhkan untuk Laravel. Atau ketikkan `php composer.phar install` jika menginstall Composer secara lokal. Tekan Enter, tunggu hingga proses selesai. 
4. Lalu hapus `.example` dari nama file `.env.example`, lalu edit file tersebut.
5. Sesuaikan konfigurasi dengan konfigurasi database pada komputer anda (samakan username dan password database anda, ada pada Line 15 file .env). Isi juga pada `DATABASE_NAME`=`bigbang-service` (Line 14 file .env). Buat database `bigbang-service` jika database belum dibuat. Biarkan database tersebut kosong karena nanti akan diisi menggunakan command.
6. Jika pada `.env` belum terdapat app key (Line 2 file .env), lakukan generate dengan perintah `php artisan key:generate` pada terminal.
7. Jika sudah, lakukan migrasi database dengan melakukan perintah `php artisan migrate --seed` pada terminal agar database `bigbang-service` terisi berikut dengan data usernya. Tekan Enter, tunggu hingga proses selesai.
8. Konfigurasi selesai. Aktifkan server dengan mengetikkan perintah `php artisan serve` pada terminal.
9. Selanjutnya untuk mengaktifkan server WhatsApp API, buka folder `whatsapp-server` didalam folder `bigbang-service`. Langkah ini penting karena jika server WhatsApp tidak menyala, aplikasi tidak dapat berjalan dengan baik.
10. Buka terminal di folder tersebut, ketikkan `npm install` untuk menginstall library Baileys agar dapat mengirim pesan WhatsApp. Tekan Enter, tunggu hingga proses selesai.
11. Lalu aktifkan server dengan mengetikkan `node app` pada terminal. QR Code akan muncul di terminal.
12. Scan QR Code tersebut menggunakan smartphone yang nomor WhatsApp nya akan digunakan untuk mengirim pesan.
13. Tunggu hingga pada terminal muncul tulisan `open connection`.
14. Selesai. Aplikasi siap digunakan.

# User Login
Gunakan user dibawah ini untuk login ke aplikasi.\
admin@bigbang.com \
tech@bigbang.com \
qc@bigbang.com \
cashier@bigbang.com \
Password(ALL) : 12341234
