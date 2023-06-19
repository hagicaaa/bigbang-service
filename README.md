# Instalasi

0. Pastikan anda sudah menginstall Composer dan NPM sebelum menginstall aplikasi ini. Jika belum, mohon untuk mengunduh terlebih dahulu, lalu install. Pastikan juga anda sudah menginstall XAMPP atau aplikasi sejenis.\
Composer: \
https://getcomposer.org/download/ \
NPM: \
https://nodejs.org/en/download
1. Jika sudah, download atau clone repo ini.
2. Setelah itu, buka folder bigbang-service, buka terminal di folder tersebut, lalu ketikkan "composer install" untuk menginstall library yang dibutuhkan untuk Laravel
3. Lalu hapus ".example" dari file ".env.example", lalu edit file tersebut
4. Sesuaikan konfigurasi sesuai konfigurasi database pada komputer anda. Buat database "bigbang-service" jika database belum dibuat.
5. Jika pada ".env" belum terdapat app key, lakukan generate dengan perintah "php artisan key:generate" pada terminal.
6. Jika sudah, lakukan migrasi database dengan melakukan perintah "php artisan migrate --seed" pada terminal.
7. Konfigurasi selesai. Aktifkan server dengan mengetikkan perintah "php artisan serve" pada terminal.
8. Selanjutnya untuk mengaktifkan server WhatsApp API, buka folder whatsapp-server pada folder bigbang-service.
9. Buka terminal di folder tersebut, ketikkan "npm install" untuk menginstall library Baileys agar dapat mengirim pesan WhatsApp.
10. Lalu aktifkan server dengan mengetikkan "node app" pada terminal. QR Code akan muncul di terminal.
11. Scan QR Code tersebut menggunakan smartphone yang nomor WhatsApp nya akan digunakan untuk mengirim pesan.
12. Selesai. 

# User Login
Gunakan user login dibawah ini, untuk mengakses aplikasi.\
admin@bigbang.com \
tech@bigbang.com \
qc@bigbang.com \
cashier@bigbang.com \
Password(ALL) : 12341234