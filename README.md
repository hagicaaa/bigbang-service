# Instalasi

1. Pastikan anda sudah menginstall Composer dan Node.js sebelum menginstall aplikasi ini. Jika belum, mohon untuk mengunduh terlebih dahulu, lalu install. Pastikan juga anda sudah menginstall XAMPP atau aplikasi sejenis.\
Composer: \
https://getcomposer.org/download/ \
Node.js: \
https://nodejs.org/en/download
2. Jika sudah, download atau clone repo ini dengan cara mengetikkan perintah di terminal `git clone https://github.com/hagicaaa/bigbang-service.git`. Untuk melakukan clone, pastikan git sudah terinstall di komputer anda. Jika belum, anda dapat mendownload git pada link https://git-scm.com/downloads
3. Setelah itu, buka folder bigbang-service, buka terminal di folder tersebut, lalu ketikkan "composer install" untuk menginstall library yang dibutuhkan untuk Laravel.
4. Lalu hapus ".example" dari file ".env.example", lalu edit file tersebut.
5. Sesuaikan konfigurasi sesuai konfigurasi database pada komputer anda. Buat database "bigbang-service" jika database belum dibuat. Database tersebut biarkan kosong karena nanti akan terisi menggunakan command.
6. Jika pada ".env" belum terdapat app key, lakukan generate dengan perintah "php artisan key:generate" pada terminal.
7. Jika sudah, lakukan migrasi database dengan melakukan perintah "php artisan migrate --seed" pada terminal.
8. Konfigurasi selesai. Aktifkan server dengan mengetikkan perintah "php artisan serve" pada terminal.
9. Selanjutnya untuk mengaktifkan server WhatsApp API, buka folder whatsapp-server didalam folder bigbang-service.
10. Buka terminal di folder tersebut, ketikkan "npm install" untuk menginstall library Baileys agar dapat mengirim pesan WhatsApp.
11. Lalu aktifkan server dengan mengetikkan "node app" pada terminal. QR Code akan muncul di terminal.
12. Scan QR Code tersebut menggunakan smartphone yang nomor WhatsApp nya akan digunakan untuk mengirim pesan.
13. Selesai. 

# User Login
Gunakan user login dibawah ini, untuk mengakses aplikasi.\
admin@bigbang.com \
tech@bigbang.com \
qc@bigbang.com \
cashier@bigbang.com \
Password(ALL) : 12341234
