# Realtime Chat Laravel

Aplikasi chat berbasis web menggunakan Laravel. Project ini dibuat untuk tugas Project 2.

## Fitur

- Login dan register user
- Private chat antar user
- Group chat
- Membuat group baru
- Status online/offline user
- Chat realtime tanpa refresh
- Menggunakan Laravel Reverb

## Teknologi

- Laravel
- Laravel Breeze
- Laravel Reverb
- MySQL
- Blade
- JavaScript
- Vite

## Cara Menjalankan Project

1. Clone repository

```bash
git clone https://github.com/nasywaalifta/realtime-chat.git
```

2. Masuk ke folder project

```bash
cd realtime-chat
```

3. Install dependency

```bash
composer install
npm install
```

4. Copy file `.env`

```bash
cp .env.example .env
```

5. Generate key

```bash
php artisan key:generate
```

6. Atur database di file `.env`

```env
DB_DATABASE=realtime_chat
DB_USERNAME=root
DB_PASSWORD=
```

7. Jalankan migration

```bash
php artisan migrate
```

8. Jalankan project

```bash
php artisan serve
npm run dev
php artisan reverb:start
```

## Cara Demo

Login menggunakan dua akun berbeda, lalu coba kirim pesan melalui private chat atau group chat. Pesan akan muncul secara realtime tanpa refresh halaman.

## Pembuat

Nasywa Alifta