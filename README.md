# InstaApp

## Deskripsi

InstaApp adalah aplikasi media sosial sederhana yang terinspirasi dari Instagram. Aplikasi ini dikembangkan menggunakan Laravel sebagai Backend REST API dan Vue.js sebagai Frontend. Aplikasi menyediakan fitur autentikasi pengguna, posting gambar dan teks, like, komentar, story, follow pengguna, serta pengelolaan profil.

## Fitur

### Authentication

- Register
- Login
- Logout
- Autentikasi menggunakan Laravel Sanctum

### User

- Melihat profil pengguna
- Mengubah profil
- Mengubah avatar
- Mengubah bio
- Follow dan unfollow pengguna

### Post

- Membuat postingan
- Upload gambar
- Menambahkan caption
- Menampilkan daftar postingan
- Melihat detail postingan

### Like

- Like postingan
- Unlike postingan

### Comment

- Menambahkan komentar
- Reply komentar
- Like komentar
- Menghapus komentar milik sendiri

### Story

- Membuat story
- Melihat story pengguna lain

### Authorization

- Hanya pengguna yang telah login dapat mengakses fitur aplikasi.
- Setiap request API yang membutuhkan autentikasi menggunakan Laravel Sanctum.
- Hanya pemilik postingan yang dapat menghapus postingannya.
- Hanya pemilik komentar yang dapat menghapus komentarnya.
- Hak akses pengguna dibatasi sesuai dengan kepemilikan data.

## Teknologi

### Backend

- PHP
- Laravel
- Laravel Sanctum
- MySQL
- REST API

### Frontend

- Vue 3
- Vue Router
- Axios
- Tailwind CSS
- Vite

## Struktur Project

```
InstaApp
├── backend
│   ├── app
│   ├── routes
│   ├── database
│   ├── public
│   └── ...
└── frontend
    ├── src
    ├── public
    ├── components
    └── ...
```

## Instalasi

### Clone Repository

```bash
git clone https://github.com/sugengharianto123/InstaApp.git
cd InstaApp
```

### Backend

Masuk ke folder backend.

```bash
cd backend
```

Install dependency Laravel.

```bash
composer install
```

Salin file environment.

```bash
cp .env.example .env
```

Generate application key.

```bash
php artisan key:generate
```

Konfigurasi database pada file `.env`.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instaapp
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi database.

```bash
php artisan migrate
```

Buat symbolic link untuk penyimpanan file.

```bash
php artisan storage:link
```

Jalankan server Laravel.

```bash
php artisan serve
```

Backend berjalan pada:

```
http://127.0.0.1:8000
```

### Frontend

Masuk ke folder frontend.

```bash
cd frontend
```

Install dependency.

```bash
npm install
```

Jalankan development server.

```bash
npm run dev
```

Frontend berjalan pada:

```
http://localhost:5173
```

## API Endpoint

Base URL:

```
http://localhost:8000/api
```

### Authentication

```
POST   /login
POST   /register
POST   /logout
GET    /user
```

### Post

```
GET    /posts
POST   /posts
GET    /posts/{post}
DELETE /posts/{post}
```

### Like

```
POST   /posts/{post}/like
```

### Comment

```
POST   /posts/{post}/comments
DELETE /comments/{comment}
POST   /comments/{comment}/like
```

### User

```
GET    /users/suggested
GET    /users/me
GET    /users/me/posts
PUT    /users/me
GET    /users/{id}/followers
GET    /users/{id}/following
```

### Story

```
GET    /stories
POST   /stories
DELETE /stories/{story}
POST   /stories/cleanup
```

### Follow

```
POST   /users/{user}/follow
GET    /users/{user}/follow-status
```

## Screenshot

Tambahkan screenshot aplikasi pada bagian berikut:

- Halaman Login
- Halaman Register
- Halaman Home
- Halaman Profile
- Halaman Create Post
- Halaman Story
- Halaman Comment

## Pemenuhan Requirement

| Requirement                 | Status  |
| ---------------------------- | ------- |
| Register dan Login          | Selesai |
| Posting teks dan gambar      | Selesai |
| Like                         | Selesai |
| Komentar                     | Selesai |
| Autentikasi pengguna         | Selesai |
| Hak akses terhadap post      | Selesai |
| Hak akses terhadap komentar  | Selesai |
| Frontend dan Backend (API)   | Selesai |
| UI/UX aplikasi               | Selesai |

## Repository

[https://github.com/sugengharianto123/InstaApp](https://github.com/sugengharianto123/InstaApp)

## Lisensi

Repository ini dibuat untuk keperluan pembelajaran dan pengembangan aplikasi web.
