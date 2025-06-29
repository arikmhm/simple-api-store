# Dokumentasi API – Aplikasi Toko Sederhana

API ini dirancang untuk mendukung aplikasi web toko sederhana, menyediakan fungsionalitas manajemen pengguna, produk, dan pesanan. Dibangun dengan Laravel, API ini memanfaatkan Laravel Sanctum untuk autentikasi dan mendukung otorisasi berbasis role (customer dan admin).

## Daftar Isi

1.  [Ringkasan](#1-ringkasan)
2.  [Cara Menjalankan Project](#2-cara-menjalankan-project)
    -   [Kebutuhan Sistem](#kebutuhan-sistem)
    -   [Instalasi](#instalasi)
    -   [Konfigurasi Database](#konfigurasi-database)
    -   [Seeding Database (User Admin & Customer)](#seeding-database-user-admin--customer)
    -   [Menjalankan Server](#menjalankan-server)
3.  [Autentikasi](#3-autentikasi)
    -   [POST /register](#post-register)
    -   [POST /login](#post-login)
    -   [POST /logout](#post-logout)
    -   [GET /user](#get-user)
4.  [Endpoint Produk](#4-endpoint-produk)
    -   [GET /products](#get-products)
    -   [GET /products/{id}](#get-productsid)
    -   [POST /products](#post-products)
    -   [PUT /products/{id}](#put-productsid)
    -   [PATCH /products/{id}](#patch-productsid)
    -   [DELETE /products/{id}](#delete-productsid)
5.  [Endpoint Pesanan](#5-endpoint-pesanan)
    -   [POST /orders](#post-orders)
    -   [GET /orders/user](#get-ordersuser)
    -   [GET /orders](#get-orders)
    -   [PATCH /orders/{id}](#patch-ordersid)
    -   [DELETE /orders/{id}](#delete-ordersid)
6.  [Catatan Tambahan](#6-catatan-tambahan)
    -   [Autentikasi Laravel Sanctum](#autentikasi-laravel-sanctum)
    -   [Role-Based Access Control (RBAC)](#role-based-access-control-rbac)
    -   [Penanganan File Gambar](#penanganan-file-gambar)
    -   [Format Respons](#format-respons)
    -   [HTTP Status Codes](#http-status-codes)

---

## 1. Ringkasan

API ini menyediakan fungsionalitas inti untuk aplikasi toko online, termasuk:

-   **Autentikasi & Otorisasi**: Registrasi, login, logout, dan manajemen akses berdasarkan role (customer/admin) menggunakan Laravel Sanctum.
-   **Manajemen Produk**: Operasi CRUD (Create, Read, Update, Delete) untuk produk, termasuk upload gambar. Endpoint baca (GET) bersifat publik, sementara operasi tulis/hapus memerlukan hak akses admin.
-   **Manajemen Pesanan**: Pengguna dapat membuat pesanan, melihat riwayat pesanan mereka sendiri. Admin dapat melihat semua pesanan, serta memperbarui status dan menghapus pesanan.

**Base URL**:

```

[http://api-store.test/api](https://www.google.com/search?q=http://api-store.test/api)

```

(Sesuaikan jika domain lokal berbeda)

## 2. Cara Menjalankan Project

### Kebutuhan Sistem

-   PHP >= 8.2 (Sesuai Laravel 11/12)
-   Composer
-   MySQL (database yang digunakan)

### Instalasi

1.  **Clone repository** API Laravel:
    ```bash
    git clone <url-repo-backend>
    cd <nama-folder-project>
    ```
2.  **Instal dependensi PHP**:
    ```bash
    composer install
    ```
3.  **Salin file `.env` dan generate app key**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

### Konfigurasi Database

1.  **Buat database MySQL** baru di server MySQL (misalnya `nama_database_`).
2.  **Buka file `.env`** di root proyek Laravel .
3.  **Sesuaikan konfigurasi database** untuk terhubung ke MySQL :
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1       # Host MySQL (mis. localhost, 127.0.0.1)
    DB_PORT=3306            # Port MySQL (default 3306)
    DB_DATABASE=nama_database_ # Ganti dengan nama database yang  buat
    DB_USERNAME=root        # Username MySQL
    DB_PASSWORD=            # Password MySQL  (biarkan kosong jika tidak ada password)
    ```

### Seeding Database (User Admin & Customer)

Untuk membersihkan database dan mengisi dengan user default (admin dan customer):

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan menonaktifkan foreign key checks sementara, membersihkan tabel, menjalankan migrasi, dan kemudian menjalankan seeder.

Ini akan membuat user berikut:

-   **Admin**:
    -   Email: `admin@gmail.com`
    -   Password: `admin123`
    -   Role: `admin`
-   **Customer**:
    -   Email: `customer@gmail.com`
    -   Password: `customer123`
    -   Role: `customer`

### Menjalankan Server

```bash
php artisan serve
```

API akan tersedia di `http://127.0.0.1:8000` atau `http://localhost:8000`. Jika menggunakan Valet/Herd, domain lokal mungkin `http://api-store.test`.

## 3\. Autentikasi

Semua endpoint yang dilindungi memerlukan token dari Laravel Sanctum di header permintaan.

**Header Autentikasi**:

```
Authorization: Bearer <token>
Accept: application/json
```

### POST /register

Mendaftarkan user baru dengan role default `customer`.

-   **Endpoint**: `/register`
-   **Method**: `POST`
-   **Akses**: Publik
-   **Body Request (JSON)**:
    ```json
    {
        "name": "John Doe",
        "email": "john@example.com",
        "password": "secret123",
        "password_confirmation": "secret123"
    }
    ```
-   **Respons (201 Created)**:
    ```json
    {
        "message": "User registered successfully",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "customer"
            // ... other user details
        },
        "token": "sanctum_token_string"
    }
    ```

### POST /login

Login user dan mendapatkan token akses.

-   **Endpoint**: `/login`
-   **Method**: `POST`
-   **Akses**: Publik
-   **Body Request (JSON)**:
    ```json
    {
        "email": "john@example.com",
        "password": "secret123"
    }
    ```
-   **Respons (200 OK)**:
    ```json
    {
        "message": "User logged in successfully",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "customer" // atau "admin"
            // ... other user details
        },
        "token": "sanctum_token_string"
    }
    ```
-   **Respons (422 Unprocessable Content)**: Jika kredensial tidak valid.

### POST /logout

Logout user dan menghapus token yang digunakan.

-   **Endpoint**: `/logout`
-   **Method**: `POST`
-   **Akses**: Terautentikasi (customer atau admin)
-   **Header**: `Authorization: Bearer <token>`
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Logged out successfully"
    }
    ```

### GET /user

Mendapatkan data user yang sedang login.

-   **Endpoint**: `/user`
-   **Method**: `GET`
-   **Akses**: Terautentikasi (customer atau admin)
-   **Header**: `Authorization: Bearer <token>`
-   **Respons (200 OK)**:
    ```json
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "customer" // atau "admin"
        // ... other user details
    }
    ```

## 4\. Endpoint Produk

### GET /products

Melihat semua produk.

-   **Endpoint**: `/products`
-   **Method**: `GET`
-   **Akses**: Publik
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Product list fetched successfully",
        "products": [
            {
                "id": 1,
                "name": "Product A",
                "description": "Description of Product A",
                "price": 10000.0,
                "image_url": "products/image_a.png", // Path relatif di storage
                "created_at": "...",
                "updated_at": "...",
                "full_image_url": "[http://api-store.test/storage/products/image_a.png](http://api-store.test/storage/products/image_a.png)" // URL lengkap gambar
            }
            // ... more products
        ]
    }
    ```

### GET /products/{id}

Melihat detail produk berdasarkan ID.

-   **Endpoint**: `/products/{id}` (ganti `{id}` dengan ID produk)
-   **Method**: `GET`
-   **Akses**: Publik
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Product fetched successfully",
        "product": {
            "id": 1,
            "name": "Product A",
            "description": "Description of Product A",
            "price": 10000.0,
            "image_url": "products/image_a.png",
            "created_at": "...",
            "updated_at": "...",
            "full_image_url": "[http://api-store.test/storage/products/image_a.png](http://api-store.test/storage/products/image_a.png)"
        }
    }
    ```
-   **Respons (404 Not Found)**: Jika produk tidak ditemukan.

### POST /products

Membuat produk baru.

-   **Endpoint**: `/products`
-   **Method**: `POST`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`, `Content-Type: multipart/form-data`
-   **Form Data**:
    -   `name`: `string` (required)
    -   `description`: `string` (required)
    -   `price`: `number` (required, min: 0)
    -   `image_url`: `file` (required, jpeg/png/jpg, max: 2MB)
-   **Respons (201 Created)**:
    ```json
    {
        "message": "Product created successfully",
        "product": {
            "id": 2,
            "name": "New Product",
            "description": "A new item for sale.",
            "price": 15000.0,
            "image_url": "products/new_image.png",
            "full_image_url": "[http://api-store.test/storage/products/new_image.png](http://api-store.test/storage/products/new_image.png)",
            "created_at": "...",
            "updated_at": "..."
        }
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (422 Unprocessable Content)**: Jika validasi input gagal.

### PUT /products/{id}

Mengubah detail produk.

-   **Endpoint**: `/products/{id}` (ganti `{id}` dengan ID produk)
-   **Method**: `PUT`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`, `Content-Type: multipart/form-data`
-   **Form Data**:
    -   `_method`: `PUT` (String literal "PUT" diperlukan untuk spoofing method di Laravel saat menggunakan `multipart/form-data`).
    -   `name`: `string` (optional)
    -   `description`: `string` (optional)
    -   `price`: `number` (optional, min: 0)
    -   `image_url`: `file` (optional, jpeg/png/jpg, max: 2MB - akan mengganti gambar lama)
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Product updated successfully",
        "product": {
            "id": 1,
            "name": "Updated Product A",
            // ... updated details
            "full_image_url": "[http://api-store.test/storage/products/updated_image.png](http://api-store.test/storage/products/updated_image.png)"
        }
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (404 Not Found)**: Jika produk tidak ditemukan.
-   **Respons (422 Unprocessable Content)**: Jika validasi input gagal.

### PATCH /products/{id}

Mengubah detail produk secara parsial.

-   **Endpoint**: `/products/{id}` (ganti `{id}` dengan ID produk)
-   **Method**: `PATCH`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`, `Content-Type: application/json` (jika tanpa file) atau `multipart/form-data` (jika ada file)
-   **Body Request (JSON) atau Form Data**:
    -   Jika `Content-Type` adalah `application/json`:
        ```json
        {
            "name": "Partial Update"
        }
        ```
    -   Jika `Content-Type` adalah `multipart/form-data` (untuk update gambar):
        -   `_method`: `PATCH` (String literal "PATCH" diperlukan untuk spoofing method)
        -   `name`: `string` (optional)
        -   `image_url`: `file` (optional, akan mengganti gambar lama)
-   **Respons (200 OK)**: Sama seperti PUT.
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (404 Not Found)**: Jika produk tidak ditemukan.
-   **Respons (422 Unprocessable Content)**: Jika validasi input gagal.

### DELETE /products/{id}

Menghapus produk.

-   **Endpoint**: `/products/{id}` (ganti `{id}` dengan ID produk)
-   **Method**: `DELETE`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Product deleted successfully"
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (404 Not Found)**: Jika produk tidak ditemukan.

## 5\. Endpoint Pesanan

### POST /orders

Membuat pesanan baru.

-   **Endpoint**: `/orders`
-   **Method**: `POST`
-   **Akses**: Terautentikasi (customer atau admin)
-   **Header**: `Authorization: Bearer <user_token>`
-   **Body Request (JSON)**:
    ```json
    {
        "product_id": 1,
        "quantity": 2
    }
    ```
-   **Respons (201 Created)**:
    ```json
    {
        "message": "Order created successfully",
        "order": {
            "id": 1,
            "user_id": 1,
            "product_id": 1,
            "quantity": 2,
            "total_price": 20000.0,
            "status": "menunggu",
            "created_at": "...",
            "updated_at": "...",
            "user": { "id": 1, "name": "John Doe" }, // Relasi user
            "product": {
                "id": 1,
                "name": "Product A",
                "price": 10000.0,
                "image_url": "products/image_a.png", // Path relatif
                "full_image_url": "[http://api-store.test/storage/products/image_a.png](http://api-store.test/storage/products/image_a.png)" // URL lengkap
            }
        }
    }
    ```
-   **Respons (401 Unauthorized)**: Jika tidak ada token.
-   **Respons (422 Unprocessable Content)**: Jika validasi input gagal (misalnya `product_id` tidak ada).

### GET /orders/user

Melihat semua pesanan milik user yang sedang login.

-   **Endpoint**: `/orders/user`
-   **Method**: `GET`
-   **Akses**: Terautentikasi (customer atau admin)
-   **Header**: `Authorization: Bearer <user_token>`
-   **Respons (200 OK)**:
    ```json
    {
        "message": "User orders fetched successfully",
        "orders": [
            {
                "id": 1,
                "user_id": 1,
                "product_id": 1,
                "quantity": 2,
                "total_price": 20000.0,
                "status": "menunggu",
                "created_at": "...",
                "updated_at": "...",
                "user": { "id": 1, "name": "John Doe" },
                "product": {
                    "id": 1,
                    "name": "Product A",
                    "price": 10000.0,
                    "image_url": "products/image_a.png",
                    "full_image_url": "[http://api-store.test/storage/products/image_a.png](http://api-store.test/storage/products/image_a.png)"
                }
            }
            // ... more orders
        ]
    }
    ```
-   **Respons (401 Unauthorized)**: Jika tidak ada token.

### GET /orders

Melihat semua pesanan di sistem.

-   **Endpoint**: `/orders`
-   **Method**: `GET`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`
-   **Respons (200 OK)**:
    ```json
    {
        "message": "All orders fetched successfully",
        "orders": [
            {
                "id": 1,
                "user_id": 1,
                "product_id": 1,
                "quantity": 2,
                "total_price": 20000.0,
                "status": "menunggu",
                "created_at": "...",
                "updated_at": "...",
                "user": { "id": 1, "name": "John Doe" },
                "product": {
                    "id": 1,
                    "name": "Product A",
                    "price": 10000.0,
                    "image_url": "products/image_a.png",
                    "full_image_url": "[http://api-store.test/storage/products/image_a.png](http://api-store.test/storage/products/image_a.png)"
                }
            }
            // ... more orders
        ]
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (401 Unauthorized)**: Jika tidak ada token.

### PATCH /orders/{id}

Mengubah status pesanan.

-   **Endpoint**: `/orders/{id}` (ganti `{id}` dengan ID pesanan)
-   **Method**: `PATCH`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`, `Content-Type: application/json`
-   **Body Request (JSON)**:
    ```json
    {
        "status": "diproses"
    }
    ```
    Status yang diperbolehkan: `menunggu`, `diproses`, `selesai`, `dibatalkan`.
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Order status updated successfully",
        "order": {
            "id": 1
            // ... order details with new status
        }
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (401 Unauthorized)**: Jika tidak ada token.
-   **Respons (422 Unprocessable Content)**: Jika status tidak valid.

### DELETE /orders/{id}

Menghapus pesanan.

-   **Endpoint**: `/orders/{id}` (ganti `{id}` dengan ID pesanan)
-   **Method**: `DELETE`
-   **Akses**: Admin
-   **Header**: `Authorization: Bearer <admin_token>`
-   **Respons (200 OK)**:
    ```json
    {
        "message": "Order deleted successfully",
        "order_id": 1 // ID pesanan yang dihapus
    }
    ```
-   **Respons (403 Forbidden)**: Jika user bukan admin.
-   **Respons (401 Unauthorized)**: Jika tidak ada token.
-   **Respons (404 Not Found)**: Jika pesanan tidak ditemukan.

## 6\. Catatan Tambahan

### Autentikasi Laravel Sanctum

-   API ini menggunakan Personal Access Tokens dari Laravel Sanctum.
-   Token harus disertakan dalam header `Authorization` dengan skema `Bearer`.
-   Semua permintaan API juga disarankan menyertakan header `Accept: application/json` untuk memastikan respons dalam format JSON.

### Role-Based Access Control (RBAC)

-   Aplikasi ini memiliki 2 role utama: `customer` dan `admin`.
-   Role `admin` memiliki akses ke operasi sensitif seperti membuat/mengedit/menghapus produk dan melihat/mengelola semua pesanan.
-   Middleware `admin` diterapkan pada rute-rute yang memerlukan hak akses administratif. Jika pengguna non-admin mencoba mengakses rute ini, API akan mengembalikan `403 Forbidden`.

### Penanganan File Gambar

-   Gambar produk diupload ke penyimpanan publik Laravel (`storage/app/public/products`).
-   URL gambar lengkap (`full_image_url`) dihasilkan melalui accessor di `Product` model dan secara otomatis disertakan dalam respons produk.
-   Saat memperbarui produk, gambar lama akan dihapus dari penyimpanan jika gambar baru diupload.

### Format Respons

-   Sebagian besar respons API memiliki struktur JSON yang konsisten, berisi `message` (string konfirmasi) dan properti data utama (misalnya `user`, `product`, `order`, `products`, `orders`).
-   Respons error umumnya akan memiliki `message` yang menjelaskan masalah, dan `errors` jika terkait dengan validasi.

### HTTP Status Codes

API ini menggunakan kode status HTTP str untuk mengindikasikan hasil permintaan:

-   `200 OK`: Permintaan berhasil.
-   `201 Created`: Sumber daya baru berhasil dibuat (untuk `POST`).
-   `401 Unauthorized`: Autentikasi gagal atau token tidak valid/tidak ada.
-   `403 Forbidden`: User terautentikasi tetapi tidak memiliki hak akses yang diperlukan.
-   `404 Not Found`: Sumber daya tidak ditemukan.
-   `422 Unprocessable Content`: Validasi input gagal.
-   `500 Internal Server Error`: Terjadi kesalahan tak terduga di server.

---
