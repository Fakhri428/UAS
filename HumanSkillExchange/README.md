# Human Skill Exchange API

Project Laravel 12 untuk praktikum Web Service. API ini memigrasikan project PHP native Human Skill Exchange ke Laravel + Jetstream + Sanctum.

## Teknologi

- Laravel 12
- Jetstream Livewire
- Laravel Sanctum API Token
- MySQL
- Vite + Tailwind
- OpenAPI + RapiDoc

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Pastikan konfigurasi database di `.env` sesuai:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=human_skill_exchange_laravel
DB_USERNAME=root
DB_PASSWORD=root
```

Buat database:

```bash
mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS human_skill_exchange_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Build frontend:

```bash
npm run build
```

Jalankan server:

```bash
php artisan serve
```

Buka dokumentasi:

```text
http://127.0.0.1:8000
http://127.0.0.1:8000/docs
http://127.0.0.1:8000/api-docs
```

Spesifikasi OpenAPI tersedia di:

```text
http://127.0.0.1:8000/openapi.json
```

## Akun Contoh

Semua akun contoh memakai password:

```text
password123
```

| Nama | Email | Token contoh |
|---|---|---|
| Fakhri | `fakhri@example.com` | `fakhri-token-123` |
| Raka | `raka@example.com` | `raka-token-123` |
| Admin | `admin@hse.test` | `admin-token-123` |

Gunakan token pada header:

```http
Authorization: Bearer fakhri-token-123
Accept: application/json
```

Login juga akan membuat token Sanctum baru:

```http
POST /api/login
Content-Type: application/json
Accept: application/json

{
  "email": "fakhri@example.com",
  "password": "password123"
}
```

## Endpoint Utama

| Method | Endpoint | Fungsi | Token |
|---|---|---|---|
| POST | `/api/register` | Register user baru | Tidak |
| POST | `/api/login` | Login dan mendapatkan Bearer Token | Tidak |
| POST | `/api/logout` | Logout token aktif | Ya |
| GET | `/api/me` | Melihat user login | Ya |
| GET | `/api/plans` | Melihat paket Gratis, Pro, Pro Max | Tidak |
| GET | `/api/subscription` | Melihat paket aktif | Ya |
| POST | `/api/subscription` | Mengaktifkan paket | Ya |
| PATCH | `/api/subscription` | Membatalkan subscription | Ya |
| GET | `/api/profile` | Melihat profil login atau user lain | Ya |
| POST/PUT | `/api/profile` | Membuat atau memperbarui profil | Ya |
| GET/POST/PUT/DELETE | `/api/skills` | CRUD skill | Ya |
| GET/POST/PUT/DELETE | `/api/needs` | CRUD need | Ya |
| GET/POST/PUT/DELETE | `/api/offers` | CRUD offer | Ya |
| GET/POST/PUT/DELETE | `/api/portfolios` | CRUD portfolio | Ya |
| GET | `/api/exchange_types` | Melihat jenis exchange | Tidak |
| GET | `/api/matches` | Rule-based matching | Ya |
| GET/POST/PATCH | `/api/exchange_requests` | Exchange request dan status | Ya |
| GET/POST/PUT/DELETE | `/api/exchange_progress` | Progress exchange | Ya |
| GET/POST | `/api/reviews` | Review dan rating | Ya |
| GET | `/api/reputation` | Menghitung reputasi user | Ya |

Alias Laravel-style juga tersedia:

```text
/api/exchange-types
/api/exchange-requests
/api/exchange-progress
```

## Contoh Request

Melihat match:

```bash
curl -H "Accept: application/json" \
  -H "Authorization: Bearer fakhri-token-123" \
  http://127.0.0.1:8000/api/matches
```

Membuat offer:

```bash
curl -X POST http://127.0.0.1:8000/api/offers \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer fakhri-token-123" \
  -d '{
    "title": "Saya bisa bantu membuat REST API Laravel",
    "type": "skill",
    "category": "Programming",
    "description": "Saya bisa bantu API login, CRUD, validasi, dan dokumentasi.",
    "exchange_expectation": "Saya membutuhkan bantuan desain UI dashboard.",
    "available_duration": "4 jam per minggu"
  }'
```

## Verifikasi

```bash
php artisan test
npm run build
php artisan route:list --path=api
```


Tampilan
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/28fec556-8a53-4ad2-bbec-c6480ada41bd" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/770cf948-52e5-4fe3-818a-16efd3272941" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/a682e63e-b0f5-4ea7-a814-1eb2ce8eb633" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/e0a46ea7-5f43-4ecf-8551-387bd31014bb" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/50788f4e-dda8-452a-ad9b-c162276f4f03" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/191bfe1c-a35c-4a71-8c8d-ff0123db72fb" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/3386696f-a562-41ac-9413-69cf4d3a3b8a" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/1526e227-c3ab-4737-8e58-a4176c81d1da" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/9547c3b1-547a-4487-9fc7-851cbaacf1ff" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/1498e1c1-147f-4207-a686-0e7f192813a8" />









