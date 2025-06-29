# Dokumentasi API – Aplikasi Store

API sederhana untuk manajemen produk dan pemesanan menggunakan Laravel dan Laravel Sanctum.

## Ringkasan

API ini memungkinkan pengguna untuk:

* Registrasi, login, dan logout menggunakan token autentikasi
* Melihat, membuat, mengubah, dan menghapus produk (termasuk upload gambar)
* Melakukan pemesanan
* Melihat daftar pesanan milik user yang sedang login

Base URL:

```
http://api-store.test/api
```

Autentikasi:
Gunakan token dari Laravel Sanctum.
Endpoint yang dilindungi memerlukan header:

```
Authorization: Bearer <token>
Accept: application/json
```

---

## Cara Menjalankan Project

### Kebutuhan Sistem

* PHP >= 8.2
* Composer
* SQLite (default) atau MySQL (opsional)

### Instalasi

1. Clone repository

```bash
git clone <url-repo>
cd <nama-folder-project>
```

2. Install dependensi PHP

```bash
composer install
```

3. Salin file `.env` dan generate app key

```bash
cp .env.example .env
php artisan key:generate
```

4. Jalankan migrasi database

```bash
php artisan migrate
```

> Secara default menggunakan SQLite dan akan otomatis membuat file database jika belum ada.

5. Jalankan server Laravel

```bash
php artisan serve
```

---

## Endpoint Autentikasi

### POST /register

Mendaftarkan user baru.

**Body Request**

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

### POST /login

Login dan mendapatkan token akses.

**Body Request**

```json
{
  "email": "john@example.com",
  "password": "secret123"
}
```

### POST /logout

Logout dan menghapus token.

**Header**

```
Authorization: Bearer <token>
```

### GET /user

Mendapatkan data user yang sedang login.

**Header**

```
Authorization: Bearer <token>
```

---

## Endpoint Produk

### GET /products

Melihat semua produk (akses publik).

### GET /products/{id}

Melihat detail produk berdasarkan ID.

### POST /products

Membuat produk baru (perlu autentikasi).

**Headers**

* Authorization: Bearer <token>
* Content-Type: multipart/form-data

**Form Data**

* name: string
* description: string
* price: number
* image\_url: file (jpeg/png, max: 2MB)

### PUT /products/{id}

Mengubah produk (semua field opsional).

### DELETE /products/{id}

Menghapus produk.

---

## Endpoint Pesanan

### GET /orders

Melihat semua pesanan (hanya untuk admin).

### GET /orders/user

Melihat semua pesanan milik user yang sedang login.

### POST /orders

Membuat pesanan baru.

**Body Request**

```json
{
  "product_id": 1,
  "quantity": 2
}
```

### GET /orders/{id}

Melihat detail pesanan.

### PATCH /orders/{id}

Mengubah status pesanan.

**Body Request**

```json
{
  "status": "diproses"
}
```

Status yang diperbolehkan:

* menunggu
* diproses
* selesai
* dibatalkan

### DELETE /orders/{id}

Menghapus pesanan.

---

## Catatan Tambahan

* Selalu gunakan `Accept: application/json` untuk menghindari fallback HTML.
* Gambar disimpan melalui penyimpanan publik Laravel: `storage/app/public/products`
* Kolom `image_url` hanya menyimpan path, sedangkan `full_image_url` memberikan URL lengkap.
* Laravel Sanctum digunakan untuk mengelola token personal access.

---

## Jalankan Project (opsional)

Jika ingin menjalankan semua layanan pengembangan secara paralel, tersedia script berikut:

```bash
composer run dev
```

Namun karena proyek ini hanya API (tanpa frontend terintegrasi), cukup gunakan:

```bash
php artisan serve
```
