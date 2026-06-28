# Human Skill Exchange API dengan RapiDoc

Project ini adalah dokumentasi interaktif (RapiDoc + OpenAPI) untuk **Human Skill Exchange API** yang dibangun dengan **Laravel + Sanctum**. Halaman `index.html` membaca `openapi.json` dan menampilkannya dengan RapiDoc, lengkap dengan tombol Authorize untuk mencoba endpoint yang membutuhkan Bearer Token.

> Catatan: spesifikasi `openapi.json` di folder ini sudah disesuaikan dengan route Laravel asli (`routes/api.php` pada project `HumanSkillExchange`), bukan lagi versi PHP native ber-ekstensi `.php`.

## Ringkasan

Human Skill Exchange API mempertemukan pengguna berdasarkan kontribusi yang dapat mereka berikan dan kebutuhan yang sedang mereka cari. Kontribusi dapat berupa skill, waktu, pengalaman, mentoring, bantuan project, atau kolaborasi kerja.

Alur utama MVP:

```text
Register/Login
Isi Profil
Tambah Skill, Need, dan Offer
Lihat Matching
Kirim Exchange Request
Accept/Reject
Update Progress
Konfirmasi Selesai oleh dua user
Review dan Rating
Hitung Reputasi
```

## Teknologi

Backend API (project `HumanSkillExchange`):

- PHP 8.x
- Laravel
- Laravel Sanctum (Bearer Token)
- MySQL/MariaDB
- Eloquent ORM

Dokumentasi (folder ini):

- HTML, CSS, JavaScript
- OpenAPI 3.0
- RapiDoc

## Struktur Folder Dokumentasi

```text
HumanSkillExchangeAPI-main/
|
+-- index.html      (landing page + RapiDoc)
+-- style.css
+-- app.js          (tester fetch ke API Laravel)
+-- openapi.json    (spesifikasi OpenAPI, sesuai route Laravel)
+-- README.md
```

## Cara Menjalankan

1. Jalankan backend Laravel (project `HumanSkillExchange`):

   ```bash
   php artisan migrate --seed
   php artisan serve
   ```

   Secara default API tersedia di `http://127.0.0.1:8000/api`.

2. Pastikan base URL pada `app.js` (`API_BASE`) dan server di `openapi.json` sesuai dengan alamat server Laravel Anda.

3. Buka `index.html` di browser (langsung atau lewat web server statis), lalu gunakan tombol Authorize pada RapiDoc untuk memasukkan token contoh.

> Jika dokumentasi diakses dari origin berbeda dengan API, pastikan CORS Laravel mengizinkan origin tersebut (`config/cors.php`).

## Akun Contoh

Semua akun contoh memakai password:

```text
password123
```

| Nama | Email | Token |
|---|---|---|
| Fakhri | `fakhri@example.com` | `fakhri-token-123` |
| Raka | `raka@example.com` | `raka-token-123` |
| Admin | `admin@hse.test` | `admin-token-123` |

Gunakan token pada header:

```http
Authorization: Bearer fakhri-token-123
```

## Endpoint Utama

| Method | Endpoint | Fungsi | Token |
|---|---|---|---|
| POST | `/api/register` | Register user baru | Tidak |
| POST | `/api/login` | Login dan mendapatkan token | Tidak |
| POST | `/api/logout` | Logout user login | Ya |
| GET | `/api/me` (alias `/api/user`) | Melihat user login | Ya |
| GET | `/api/plans` | Melihat paket Gratis, Pro, Pro Max | Tidak |
| GET | `/api/subscription` (alias `/api/my-plan`) | Melihat paket aktif | Ya |
| POST | `/api/subscription` (alias `/api/subscribe`) | Mengaktifkan paket | Ya |
| PATCH | `/api/subscription` (alias `/api/subscribe/cancel`) | Membatalkan subscription | Ya |
| GET | `/api/profile` | Melihat profil login atau user lain | Ya |
| POST/PUT | `/api/profile` | Membuat atau memperbarui profil | Ya |
| GET | `/api/users/{id}/profile` | Profil publik user lain | Ya |
| GET/POST/PUT/DELETE | `/api/skills` (+ `/api/skills/{id}`) | CRUD skill | Ya |
| GET/POST/PUT/DELETE | `/api/needs` (+ `/api/needs/{id}`) | CRUD need | Ya |
| GET/POST/PUT/DELETE | `/api/offers` (+ `/api/offers/{id}`) | CRUD offer | Ya |
| GET/POST/PUT/DELETE | `/api/portfolios` (+ `/api/portfolios/{id}`) | CRUD portfolio | Ya |
| GET | `/api/exchange-types` (alias `/api/exchange_types`) | Melihat jenis exchange | Tidak |
| GET | `/api/matches` | Rule-based matching | Ya |
| GET | `/api/offers/{id}/matches`, `/api/needs/{id}/matches` | Match per offer/need | Ya |
| GET/POST | `/api/exchange-requests` (+ `/{id}`) | List/detail dan kirim exchange request | Ya |
| PATCH | `/api/exchange-requests/{id}/status` | Accept/reject/in_progress/cancelled | Ya |
| PATCH | `/api/exchange-requests/{id}/complete` | Konfirmasi selesai | Ya |
| GET/POST | `/api/exchange-requests/{id}/progress` | Progress bersarang | Ya |
| GET/POST/PUT/DELETE | `/api/exchange-progress` (+ `/{id}`) | CRUD progress | Ya |
| POST | `/api/reviews` | Review dan rating | Ya |
| GET | `/api/reviews?user_id=2`, `/api/users/{id}/reviews` | Melihat review user | Ya |
| GET | `/api/reputation`, `/api/users/{id}/reputation` | Menghitung reputasi user | Ya |
| GET/POST/PUT/DELETE | `/api/mentoring-rooms` (+ `/{id}`) | CRUD ruang mentoring | Ya |
| POST | `/api/mentoring-rooms/{id}/book` | Booking ruang mentoring | Ya |
| GET/POST/PUT | `/api/mentoring-bookings` (+ `/{id}`) | Booking mentoring | Ya |
| GET/POST | `/api/transactions` (+ `/{id}`) | Transaksi pembayaran | Ya |
| PATCH | `/api/transactions/{id}/confirm` | Konfirmasi pembayaran | Ya |
| GET | `/api/admin/users`, `/exchanges`, `/reviews`, `/transactions` | Data admin | Ya (admin) |
| PATCH | `/api/admin/users/{id}/verify`, `/api/admin/reviews/{id}/hide` | Aksi admin | Ya (admin) |

## Contoh Request

### Login

```http
POST /api/login
Content-Type: application/json
```

```json
{
  "email": "fakhri@example.com",
  "password": "password123"
}
```

### Membuat Offer

```http
POST /api/offers
Content-Type: application/json
Authorization: Bearer fakhri-token-123
```

```json
{
  "title": "Saya bisa bantu membuat REST API Laravel",
  "type": "skill",
  "category": "Programming",
  "description": "Saya bisa bantu API login, CRUD, dan dokumentasi.",
  "exchange_expectation": "Saya membutuhkan bantuan desain UI dashboard.",
  "available_duration": "4 jam per minggu"
}
```

### Melihat Matching

```http
GET /api/matches
Authorization: Bearer fakhri-token-123
```

### Mengirim Exchange Request

```http
POST /api/exchange-requests
Content-Type: application/json
Authorization: Bearer fakhri-token-123
```

```json
{
  "to_user_id": 2,
  "offer_id": 1,
  "need_id": 2,
  "message": "Halo, saya bisa bantu Laravel REST API dan butuh bantuan UI Design."
}
```

### Accept atau Reject Request

```http
PATCH /api/exchange-requests/1/status
Content-Type: application/json
Authorization: Bearer raka-token-123
```

```json
{
  "status": "accepted"
}
```

### Konfirmasi Selesai

```http
PATCH /api/exchange-requests/1/complete
Authorization: Bearer fakhri-token-123
```

Exchange menjadi `completed` setelah kedua user menjalankan endpoint complete.

### Review

```http
POST /api/reviews
Content-Type: application/json
Authorization: Bearer fakhri-token-123
```

```json
{
  "exchange_request_id": 1,
  "reviewed_user_id": 2,
  "rating": 5,
  "comment": "Raka komunikatif dan desain UI-nya rapi."
}
```

## Paket dan Limit

| Fitur | Gratis | Pro | Pro Max |
|---|---:|---:|---:|
| Skill | 3 | 10 | Unlimited |
| Need | 3 | 10 | Unlimited |
| Offer | 2 | 10 | Unlimited |
| Exchange request per bulan | 5 | 30 | Unlimited |

Limit ini diterapkan di controller Laravel pada endpoint `skills`, `needs`, `offers`, dan `exchange-requests` (lihat `BaseApiController::enforcePlanLimit` dan `ExchangeRequestController`).

## Catatan Keamanan

Autentikasi memakai Laravel Sanctum (personal access token) dengan password ter-hash. Untuk produksi, tambahkan HTTPS, rate limiting, masa berlaku token, audit log, dan role-based access control yang lebih lengkap.

## Dokumentasi RapiDoc

File `openapi.json` dibaca oleh halaman `index.html` melalui RapiDoc. Gunakan tombol Authorize di RapiDoc, lalu masukkan token contoh:

```text
fakhri-token-123
```

Setelah itu endpoint yang membutuhkan token dapat dicoba langsung dari browser (pastikan server Laravel berjalan dan CORS mengizinkan origin dokumentasi).
