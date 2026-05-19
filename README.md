# Realtime Chat Laravel

Project ini merupakan aplikasi chat berbasis web yang dibuat menggunakan Laravel. Aplikasi ini memiliki fitur chat antar user, group chat, status online/offline, dan realtime chat menggunakan Laravel Reverb.

## Fitur Aplikasi

- Login dan register user
- Private chat antar user
- Group chat
- Membuat group baru
- Menampilkan status online dan offline user
- Mengirim pesan tanpa refresh halaman
- Realtime chat menggunakan Laravel Reverb
- Tampilan sederhana dan mudah digunakan

## Teknologi yang Digunakan

- Laravel
- Laravel Breeze
- Laravel Reverb
- MySQL
- Blade Template
- JavaScript
- Vite
- Git dan GitHub

## Struktur Fitur

### 1. Autentikasi User

User dapat melakukan register dan login sebelum masuk ke halaman chat. Fitur autentikasi menggunakan Laravel Breeze.

### 2. Private Chat

User dapat memilih user lain untuk melakukan percakapan secara pribadi.

### 3. Group Chat

User dapat membuat group baru dan mengirim pesan di dalam group tersebut.

### 4. Status Online dan Offline

Aplikasi menampilkan status user apakah sedang online atau offline berdasarkan aktivitas terakhir user.

### 5. Realtime Chat

Pesan dapat muncul secara realtime tanpa harus melakukan refresh halaman. Fitur ini menggunakan WebSocket dengan Laravel Reverb.

## Cara Menjalankan Project

1. Clone repository

```bash
git clone https://github.com/nasywaalifta/realtime-chat.git