# PRD — Human Skill Exchange API

## 1. Informasi Project

**Nama Project:** Human Skill Exchange API  
**Jenis Project:** Web Services berbasis REST API  
**Kategori:** Platform pertukaran skill, waktu, pengalaman, mentoring, bantuan project, dan kolaborasi kerja  
**Model Bisnis:** Freemium — Gratis, Pro, dan Pro Max  
**Target Pengguna:** Mahasiswa, pelajar, freelancer pemula, komunitas, mentor, dan orang yang ingin membangun portofolio/personal branding  

---

## 2. Ringkasan Project

**Human Skill Exchange API** adalah web service berbasis REST API yang digunakan untuk mempertemukan pengguna berdasarkan kontribusi yang dapat mereka berikan dan kebutuhan yang sedang mereka cari.

Berbeda dari platform barter skill biasa, project ini tidak hanya berfokus pada pertukaran skill, tetapi juga mencakup:

- skill,
- waktu,
- pengalaman,
- mentoring,
- bantuan tugas/project,
- kolaborasi kerja.

Contoh sederhana:

> User A bisa membuat REST API Laravel, tetapi membutuhkan bantuan desain UI.  
> User B bisa desain UI, tetapi membutuhkan bantuan backend.  
> Sistem mempertemukan keduanya agar mereka bisa saling bertukar kontribusi.

Project ini juga membantu pengguna membangun **personal branding** melalui profil, portofolio, rating, review, dan reputasi digital.

---

## 3. Latar Belakang

Banyak orang memiliki kemampuan, waktu, pengalaman, atau keahlian tertentu, tetapi belum memiliki tempat yang terstruktur untuk menawarkannya. Di sisi lain, banyak orang membutuhkan bantuan, mentoring, partner project, atau pengalaman kerja nyata, tetapi sering terkendala biaya atau kesulitan menemukan orang yang tepat.

Saat ini, pencarian bantuan atau kolaborasi sering dilakukan secara manual melalui grup WhatsApp, Telegram, media sosial, atau jaringan pertemanan. Cara tersebut kurang terstruktur karena tidak ada sistem yang mencatat skill, kebutuhan, reputasi, progres kerja, dan hasil kolaborasi secara rapi.

Human Skill Exchange API dibuat untuk menjadi solusi pertukaran kontribusi antar pengguna. Pengguna dapat menawarkan apa yang mereka miliki dan mencari apa yang mereka butuhkan. Sistem kemudian melakukan pencocokan berdasarkan data skill, kebutuhan, waktu, mode kerja, dan reputasi.

Nilai utama project ini adalah:

> **Mengubah skill, waktu, pengalaman, dan kontribusi menjadi alat tukar untuk membangun kolaborasi dan reputasi digital.**

---

## 4. Tujuan Project

Tujuan utama project ini adalah membangun REST API yang dapat digunakan untuk:

1. Mengelola data pengguna dan profil personal branding.
2. Mengelola skill, kebutuhan, penawaran, dan jenis exchange.
3. Melakukan pencocokan pengguna berdasarkan kebutuhan dan kontribusi.
4. Mengelola exchange request antar pengguna.
5. Mencatat progres kolaborasi.
6. Mengelola review, rating, dan reputasi pengguna.
7. Menyediakan sistem paket Gratis, Pro, dan Pro Max.
8. Mendukung fitur monetisasi seperti premium profile, featured offer, mentoring room berbayar, dan komisi platform.

---

## 5. 5W + 1H

### 5.1 What — Apa?

Human Skill Exchange API adalah REST API untuk platform pertukaran kontribusi antar pengguna. Kontribusi yang ditukar dapat berupa skill, waktu, pengalaman, mentoring, bantuan project, atau kolaborasi kerja.

---

### 5.2 Who — Siapa?

Target pengguna:

- mahasiswa,
- pelajar,
- freelancer pemula,
- fresh graduate,
- mentor,
- komunitas kreatif,
- komunitas teknologi,
- organisasi kampus,
- orang yang ingin membangun portofolio,
- orang yang ingin mencari partner kolaborasi.

---

### 5.3 When — Kapan?

Sistem digunakan ketika pengguna:

- membutuhkan bantuan tetapi tidak ingin/ belum bisa membayar uang,
- ingin bertukar kemampuan,
- ingin mencari mentor,
- ingin membangun portofolio,
- ingin mencari partner project,
- ingin menawarkan waktu atau pengalaman,
- ingin meningkatkan personal branding.

---

### 5.4 Where — Di mana?

API ini dapat digunakan sebagai backend untuk:

- website pertukaran skill,
- aplikasi mobile komunitas,
- platform mentoring,
- platform freelancer pemula,
- sistem internal komunitas/kampus,
- platform personal branding,
- platform kolaborasi project.

---

### 5.5 Why — Mengapa?

Human Skill Exchange API dibuat karena banyak orang memiliki skill, waktu, dan pengalaman, tetapi belum memiliki tempat yang terstruktur untuk menawarkannya. Di sisi lain, banyak orang membutuhkan bantuan, mentoring, atau partner kolaborasi, tetapi terkendala biaya dan sulit menemukan orang yang sesuai.

Masalah utama yang diselesaikan:

1. Banyak orang membutuhkan bantuan, tetapi terbatas biaya.
2. Banyak orang memiliki kemampuan, tetapi belum tahu cara memanfaatkannya.
3. Pencarian partner kolaborasi masih manual dan tidak terstruktur.
4. Freelancer pemula sulit mendapatkan pengalaman pertama.
5. Mahasiswa membutuhkan portofolio nyata.
6. Komunitas membutuhkan sistem kolaborasi yang rapi.
7. Kepercayaan antar pengguna sulit dibangun tanpa rating dan review.

Sistem ini membantu pengguna mempertemukan kebutuhan dan kontribusi secara lebih terstruktur.

---

### 5.6 How — Bagaimana?

Alur kerja sistem:

1. User register dan login.
2. User membuat profil.
3. User menambahkan skill, pengalaman, portofolio, dan waktu tersedia.
4. User membuat offer atau penawaran bantuan.
5. User membuat need atau kebutuhan bantuan.
6. Sistem melakukan matching.
7. User melihat daftar match.
8. User mengirim exchange request.
9. User lain menerima atau menolak request.
10. Jika diterima, exchange berjalan.
11. User dapat mengirim progress atau bukti pekerjaan.
12. Kedua user mengonfirmasi exchange selesai.
13. User saling memberi review dan rating.
14. Sistem memperbarui reputasi pengguna.

---

## 6. Ruang Lingkup Project

### 6.1 In Scope

Fitur yang termasuk dalam project:

- autentikasi user,
- profil pengguna,
- skill management,
- need management,
- offer management,
- exchange type,
- rule-based matching,
- exchange request,
- status exchange,
- progress exchange,
- completion confirmation,
- review dan rating,
- reputasi pengguna,
- portfolio pengguna,
- paket Gratis, Pro, dan Pro Max,
- mentoring room berbayar,
- transaksi manual,
- admin basic.

---

### 6.2 Out of Scope

Fitur yang tidak wajib dibuat pada versi awal:

- machine learning matching,
- chat real-time,
- payment gateway otomatis,
- video conference internal,
- mobile app,
- notifikasi WhatsApp otomatis,
- AI recommendation,
- sistem dispute kompleks.

---

## 7. Jenis Exchange

Human Skill Exchange API mendukung beberapa jenis pertukaran:

| Jenis Exchange | Deskripsi | Contoh |
|---|---|---|
| Skill | Pertukaran kemampuan teknis/non-teknis | Coding ditukar desain |
| Waktu | Pertukaran bantuan berdasarkan durasi | Bantu input data 2 jam ditukar bantu presentasi 2 jam |
| Pengalaman | Berbagi pengalaman nyata | Sharing pengalaman magang ditukar desain CV |
| Mentoring | Bimbingan terstruktur | Mentoring Laravel ditukar dokumentasi Postman |
| Bantuan Project | Membantu tugas/project tertentu | Testing aplikasi ditukar laporan project |
| Kolaborasi Kerja | Kerja sama membuat project | Backend + UI/UX + content writer membuat produk bersama |

---

## 8. User Role

| Role | Deskripsi |
|---|---|
| Guest | Pengunjung belum login, hanya bisa melihat informasi umum |
| User Gratis | Pengguna paket gratis dengan batasan fitur |
| User Pro | Pengguna berbayar dengan fitur lebih lengkap |
| User Pro Max | Pengguna berbayar tertinggi dengan prioritas dan fitur maksimal |
| Admin | Mengelola user, exchange, review, transaksi, dan verifikasi |

---

## 9. Paket Langganan

### 9.1 Tabel Paket

| Fitur | Gratis | Pro | Pro Max |
|---|---:|---:|---:|
| Membuat akun | ✅ | ✅ | ✅ |
| Membuat profil | ✅ | ✅ | ✅ |
| Menambahkan skill | Maks. 3 | Maks. 10 | Unlimited |
| Menambahkan kebutuhan | Maks. 3 | Maks. 10 | Unlimited |
| Membuat offer | Maks. 2 | Maks. 10 | Unlimited |
| Matching user | Basic | Advanced | Priority |
| Exchange request | Maks. 5/bulan | Maks. 30/bulan | Unlimited |
| Review & rating | ✅ | ✅ | ✅ |
| Upload portofolio | Maks. 2 item | Maks. 10 item | Unlimited |
| Profil publik | Basic | Custom link | Custom link + tema |
| Badge profil | ❌ | Pro Badge | Verified/Trusted Badge |
| Featured profile | ❌ | 3 hari/bulan | 15 hari/bulan |
| Featured offer | ❌ | 3 offer/bulan | 10 offer/bulan |
| Statistik profil | ❌ | Basic analytics | Advanced analytics |
| Export portofolio PDF | ❌ | ✅ | ✅ |
| Prioritas pencarian | ❌ | Sedang | Tinggi |
| Mentoring room berbayar | ❌ | ✅ | ✅ |
| Komisi platform | Tidak tersedia | Standar | Lebih rendah |

---

### 9.2 Harga Rekomendasi

| Paket | Harga |
|---|---:|
| Gratis | Rp0 |
| Pro | Rp19.000/bulan |
| Pro Max | Rp59.000/bulan |

Alternatif harga mahasiswa:

| Paket | Harga |
|---|---:|
| Gratis | Rp0 |
| Pro | Rp10.000/bulan |
| Pro Max | Rp25.000/bulan |

---

## 10. Model Monetisasi

Human Skill Exchange API menggunakan model freemium. Fitur dasar dapat digunakan gratis, sedangkan fitur profesional dan monetisasi tersedia pada paket Pro dan Pro Max.

Sumber pendapatan:

1. **Langganan Pro dan Pro Max**  
   User membayar biaya bulanan untuk mendapatkan fitur lebih lengkap.

2. **Featured Offer**  
   User membayar agar penawaran tampil lebih tinggi.

3. **Featured Profile**  
   User membayar agar profil lebih sering muncul di hasil pencarian.

4. **Verified Badge**  
   User membayar untuk mendapatkan badge terverifikasi.

5. **Mentoring Room Berbayar**  
   User Pro dan Pro Max dapat membuka sesi mentoring berbayar.

6. **Komisi Platform**  
   Platform mengambil komisi dari mentoring room atau paid project.

7. **Paket Komunitas**  
   Platform dapat dijual ke komunitas, kampus, bootcamp, atau organisasi.

---

## 11. Alur Sistem

### 11.1 Alur Utama

```text
User Register/Login
        ↓
Isi Profil
        ↓
Input Skill yang Dimiliki
        ↓
Input Skill yang Dibutuhkan
        ↓
Buat Offer / Need
        ↓
Sistem Matching
        ↓
Daftar Match Ditampilkan
        ↓
User Kirim Exchange Request
        ↓
User Lain Accept / Reject
        ↓
Jika Accept → Exchange Berjalan
        ↓
Upload Progress / Bukti
        ↓
Kedua User Konfirmasi Selesai
        ↓
Review & Rating
        ↓
Reputasi User Terbentuk
```

---

### 11.2 Alur Status Exchange

```text
matched
↓
pending
↓
accepted
↓
in_progress
↓
completed
↓
reviewed
```

Status alternatif:

```text
pending → rejected
accepted → cancelled
in_progress → cancelled / disputed
```

---

## 12. Sistem Matching

### 12.1 Metode Matching

Versi awal menggunakan **rule-based matching**, bukan machine learning.

Sistem mencocokkan data berdasarkan:

- skill yang ditawarkan,
- skill yang dibutuhkan,
- kategori,
- jenis exchange,
- waktu tersedia,
- mode online/offline,
- lokasi jika offline,
- rating/reputasi.

---

### 12.2 Contoh Rule Matching

```text
Jika skill yang ditawarkan User A cocok dengan kebutuhan User B,
dan skill yang ditawarkan User B cocok dengan kebutuhan User A,
maka sistem memberi match score tinggi.
```

Contoh:

```text
Fakhri:
Punya: Laravel REST API
Butuh: UI Design

Raka:
Punya: UI Design
Butuh: Laravel REST API

Hasil: MATCH
```

---

### 12.3 Contoh Skor Matching

| Faktor | Bobot |
|---|---:|
| Skill/need cocok dua arah | 60 |
| Kategori cocok | 10 |
| Waktu cocok | 10 |
| Mode kerja cocok | 10 |
| Rating/reputasi baik | 5 |
| Lokasi cocok jika offline | 5 |
| Total | 100 |

---

## 13. Fitur Utama

### 13.1 Auth

Fitur autentikasi untuk register, login, logout, dan mendapatkan data user login.

### 13.2 User Profile

User dapat membuat profil berisi bio, lokasi, mode kerja, waktu tersedia, portofolio, dan link sosial.

### 13.3 Skill Management

User dapat menambahkan skill yang dimiliki beserta kategori dan level.

### 13.4 Need Management

User dapat menambahkan bantuan yang sedang dibutuhkan.

### 13.5 Offer Management

User dapat membuat penawaran bantuan berdasarkan skill, waktu, pengalaman, mentoring, bantuan project, atau kolaborasi.

### 13.6 Matching

Sistem menampilkan rekomendasi pengguna yang cocok berdasarkan rule-based matching.

### 13.7 Exchange Request

User dapat mengirim permintaan exchange kepada user lain.

### 13.8 Exchange Progress

User dapat mencatat progres pekerjaan atau mengunggah bukti pekerjaan.

### 13.9 Completion Confirmation

Exchange dianggap selesai jika kedua user sama-sama mengonfirmasi selesai.

### 13.10 Review dan Rating

Setelah exchange selesai, user dapat saling memberi review dan rating.

### 13.11 Reputation

Sistem menghitung reputasi berdasarkan jumlah exchange selesai, rating, review, dan skill yang sering digunakan.

### 13.12 Portfolio

User dapat menampilkan hasil pekerjaan sebagai personal branding.

### 13.13 Mentoring Room

User Pro dan Pro Max dapat membuka sesi mentoring berbayar.

### 13.14 Subscription

Sistem mengelola paket Gratis, Pro, dan Pro Max.

### 13.15 Transaction

Sistem mencatat transaksi langganan, mentoring, atau paid project secara manual.

### 13.16 Admin

Admin dapat mengelola user, review, exchange, transaksi, dan verifikasi.

---

## 14. Daftar REST API

### 14.1 Auth API

```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/me
```

---

### 14.2 Profile API

```http
GET  /api/profile
POST /api/profile
PUT  /api/profile
GET  /api/users/{id}/profile
```

---

### 14.3 Subscription API

```http
GET   /api/plans
GET   /api/my-plan
POST  /api/subscribe
PATCH /api/subscribe/cancel
```

---

### 14.4 Skill API

```http
GET    /api/skills
POST   /api/skills
GET    /api/skills/{id}
PUT    /api/skills/{id}
DELETE /api/skills/{id}
```

---

### 14.5 Need API

```http
GET    /api/needs
POST   /api/needs
GET    /api/needs/{id}
PUT    /api/needs/{id}
DELETE /api/needs/{id}
```

---

### 14.6 Offer API

```http
GET    /api/offers
POST   /api/offers
GET    /api/offers/{id}
PUT    /api/offers/{id}
DELETE /api/offers/{id}
```

---

### 14.7 Exchange Type API

```http
GET /api/exchange-types
```

---

### 14.8 Matching API

```http
GET /api/matches
GET /api/offers/{id}/matches
GET /api/needs/{id}/matches
```

---

### 14.9 Exchange Request API

```http
GET   /api/exchange-requests
POST  /api/exchange-requests
GET   /api/exchange-requests/{id}
PATCH /api/exchange-requests/{id}/status
PATCH /api/exchange-requests/{id}/complete
```

---

### 14.10 Exchange Progress API

```http
GET    /api/exchange-requests/{id}/progress
POST   /api/exchange-requests/{id}/progress
PUT    /api/exchange-progress/{id}
DELETE /api/exchange-progress/{id}
```

---

### 14.11 Review API

```http
POST /api/reviews
GET  /api/users/{id}/reviews
GET  /api/users/{id}/reputation
```

---

### 14.12 Portfolio API

```http
GET    /api/portfolios
POST   /api/portfolios
GET    /api/portfolios/{id}
PUT    /api/portfolios/{id}
DELETE /api/portfolios/{id}
GET    /api/users/{id}/portfolios
```

---

### 14.13 Mentoring Room API

```http
GET    /api/mentoring-rooms
POST   /api/mentoring-rooms
GET    /api/mentoring-rooms/{id}
PUT    /api/mentoring-rooms/{id}
DELETE /api/mentoring-rooms/{id}
POST   /api/mentoring-rooms/{id}/book
```

---

### 14.14 Transaction API

```http
GET   /api/transactions
POST  /api/transactions
GET   /api/transactions/{id}
PATCH /api/transactions/{id}/confirm
```

---

### 14.15 Admin API

```http
GET   /api/admin/users
GET   /api/admin/exchanges
GET   /api/admin/reviews
GET   /api/admin/transactions
PATCH /api/admin/users/{id}/verify
PATCH /api/admin/reviews/{id}/hide
```

---

## 15. API Minimal untuk Versi Tugas

Jika ingin dibuat lebih sederhana untuk tugas Web Services, minimal endpoint yang dibuat adalah:

```http
POST /api/register
POST /api/login
GET  /api/profile
POST /api/profile

GET  /api/skills
POST /api/skills

GET  /api/needs
POST /api/needs

GET  /api/offers
POST /api/offers

GET  /api/matches

POST /api/exchange-requests
PATCH /api/exchange-requests/{id}/status
PATCH /api/exchange-requests/{id}/complete

POST /api/reviews
GET  /api/users/{id}/reputation
```

Endpoint minimal tersebut sudah cukup untuk menunjukkan alur utama:

> Register → Profil → Skill/Need/Offer → Matching → Request → Accept/Reject → Complete → Review → Reputasi.

---

## 16. Contoh Request dan Response

### 16.1 Register

**Request:**

```http
POST /api/register
```

```json
{
  "name": "Fakhri",
  "email": "fakhri@example.com",
  "password": "password123"
}
```

**Response:**

```json
{
  "message": "Register berhasil",
  "user": {
    "id": 1,
    "name": "Fakhri",
    "email": "fakhri@example.com"
  }
}
```

---

### 16.2 Membuat Offer

**Request:**

```http
POST /api/offers
```

```json
{
  "title": "Saya bisa bantu membuat REST API Laravel",
  "type": "skill",
  "category": "Programming",
  "description": "Saya bisa bantu membuat API login, CRUD, dan dokumentasi Postman.",
  "exchange_expectation": "Saya membutuhkan bantuan desain UI."
}
```

**Response:**

```json
{
  "message": "Offer berhasil dibuat",
  "offer": {
    "id": 1,
    "title": "Saya bisa bantu membuat REST API Laravel",
    "type": "skill"
  }
}
```

---

### 16.3 Matching

**Request:**

```http
GET /api/offers/1/matches
```

**Response:**

```json
{
  "offer_id": 1,
  "matches": [
    {
      "user_id": 2,
      "name": "Raka",
      "match_score": 95,
      "reason": "Raka membutuhkan Laravel REST API dan menawarkan UI Design yang dibutuhkan Fakhri."
    }
  ]
}
```

---

### 16.4 Exchange Request

**Request:**

```http
POST /api/exchange-requests
```

```json
{
  "to_user_id": 2,
  "offer_id": 1,
  "need_id": 1,
  "message": "Halo, saya bisa bantu Laravel API dan saya butuh bantuan UI Design."
}
```

**Response:**

```json
{
  "message": "Exchange request berhasil dikirim",
  "exchange_request": {
    "id": 1,
    "status": "pending"
  }
}
```

---

### 16.5 Complete Exchange

**Request:**

```http
PATCH /api/exchange-requests/1/complete
```

**Response jika baru satu user yang konfirmasi:**

```json
{
  "message": "Menunggu konfirmasi dari user lain",
  "completed_by_me": true,
  "completed_by_partner": false,
  "status": "in_progress"
}
```

**Response jika kedua user sudah konfirmasi:**

```json
{
  "message": "Exchange selesai",
  "status": "completed"
}
```

---

### 16.6 Review

**Request:**

```http
POST /api/reviews
```

```json
{
  "exchange_request_id": 1,
  "reviewed_user_id": 2,
  "rating": 5,
  "comment": "Raka komunikatif dan desain UI-nya rapi."
}
```

**Response:**

```json
{
  "message": "Review berhasil dikirim",
  "review": {
    "rating": 5,
    "comment": "Raka komunikatif dan desain UI-nya rapi."
  }
}
```

---

## 17. Desain Database Awal

### 17.1 users

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Nama user |
| email | varchar | Email user |
| password | varchar | Password terenkripsi |
| role | enum | user/admin |
| plan_id | bigint | Paket user |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.2 plans

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Gratis/Pro/Pro Max |
| price | integer | Harga paket |
| max_skills | integer/null | Batas skill |
| max_needs | integer/null | Batas need |
| max_offers | integer/null | Batas offer |
| max_exchange_requests | integer/null | Batas request bulanan |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.3 profiles

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Relasi ke users |
| bio | text | Bio user |
| location | varchar | Lokasi |
| work_mode | enum | online/offline/hybrid |
| available_time | varchar | Waktu tersedia |
| portfolio_url | varchar | Link portofolio |
| social_url | varchar | Link sosial media |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.4 skills

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Pemilik skill |
| name | varchar | Nama skill |
| category | varchar | Kategori skill |
| level | enum | beginner/intermediate/advanced |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.5 needs

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Pemilik need |
| title | varchar | Judul kebutuhan |
| category | varchar | Kategori |
| description | text | Deskripsi kebutuhan |
| exchange_offer | text | Imbalan/kontribusi yang ditawarkan |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.6 offers

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Pemilik offer |
| title | varchar | Judul offer |
| type | enum | skill/time/experience/mentoring/project/collaboration |
| category | varchar | Kategori |
| description | text | Deskripsi offer |
| exchange_expectation | text | Kebutuhan yang diharapkan |
| available_duration | varchar | Durasi tersedia |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.7 exchange_requests

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| from_user_id | bigint | Pengirim request |
| to_user_id | bigint | Penerima request |
| offer_id | bigint | Relasi offer |
| need_id | bigint | Relasi need |
| message | text | Pesan request |
| status | enum | pending/accepted/rejected/in_progress/completed/cancelled |
| completed_by_from_user | boolean | Konfirmasi selesai pengirim |
| completed_by_to_user | boolean | Konfirmasi selesai penerima |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.8 exchange_progress

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| exchange_request_id | bigint | Relasi exchange |
| user_id | bigint | Pembuat progress |
| progress_note | text | Catatan progress |
| file_url | varchar | Link file/bukti |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.9 reviews

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| exchange_request_id | bigint | Relasi exchange |
| reviewer_id | bigint | User pemberi review |
| reviewed_user_id | bigint | User yang direview |
| rating | integer | Rating 1-5 |
| comment | text | Komentar review |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.10 portfolios

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | Pemilik portofolio |
| title | varchar | Judul portofolio |
| description | text | Deskripsi |
| file_url | varchar | Link file/gambar |
| project_url | varchar | Link project |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.11 mentoring_rooms

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| mentor_id | bigint | User mentor |
| title | varchar | Judul mentoring |
| description | text | Deskripsi |
| duration_minutes | integer | Durasi mentoring |
| price | integer | Harga mentoring |
| schedule | datetime | Jadwal mentoring |
| status | enum | open/booked/completed/cancelled |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

### 17.12 transactions

| Field | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | User pembayar |
| type | enum | subscription/mentoring/paid_project |
| reference_id | bigint | ID referensi transaksi |
| amount | integer | Nominal transaksi |
| platform_fee | integer | Komisi platform |
| status | enum | pending/paid/rejected/cancelled |
| payment_method | varchar | Metode pembayaran |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diubah |

---

## 18. Non-Functional Requirements

### 18.1 Security

- Password harus dienkripsi.
- Endpoint tertentu hanya dapat diakses oleh user yang sudah login.
- User hanya boleh mengubah data miliknya sendiri.
- Admin memiliki akses khusus.
- Validasi input wajib dilakukan.

### 18.2 Performance

- API harus dapat memberikan response dalam waktu yang wajar.
- Query matching harus dibatasi agar tidak terlalu berat.
- Pagination digunakan untuk data list.

### 18.3 Scalability

- Struktur database harus memungkinkan penambahan fitur seperti chat, AI matching, payment gateway, dan notifikasi.

### 18.4 Maintainability

- Kode backend harus dipisahkan berdasarkan controller, model, service, dan resource.
- Dokumentasi API dibuat menggunakan Postman atau Swagger.

---

## 19. Tech Stack Rekomendasi

| Bagian | Teknologi |
|---|---|
| Backend | Laravel REST API / Express.js |
| Database | MySQL / PostgreSQL |
| Auth | Laravel Sanctum / JWT |
| API Testing | Postman |
| Dokumentasi API | Postman Documentation / Swagger |
| Deployment Backend | Render / Railway / VPS |
| Frontend Opsional | Next.js / React / Flutter |

Untuk tugas Web Services, pilihan paling aman:

> **Laravel REST API + MySQL + Laravel Sanctum + Postman Documentation**

---

## 20. Success Metrics

Keberhasilan project dapat diukur dari:

1. User dapat register dan login.
2. User dapat membuat profil.
3. User dapat menambahkan skill, need, dan offer.
4. Sistem dapat menampilkan hasil matching.
5. User dapat mengirim exchange request.
6. User lain dapat accept/reject request.
7. Exchange dapat berjalan dan diselesaikan.
8. User dapat memberi review dan rating.
9. Reputasi user dapat dihitung.
10. Paket Gratis, Pro, dan Pro Max dapat dibedakan fiturnya.
11. API terdokumentasi dengan request dan response yang jelas.

---

## 21. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Matching kurang akurat | User mendapat rekomendasi kurang cocok | Gunakan bobot matching yang jelas |
| User tidak menyelesaikan exchange | Kepercayaan turun | Gunakan status, progress, dan review |
| Review palsu | Reputasi tidak valid | Review hanya bisa diberikan setelah exchange completed |
| Fitur terlalu banyak | Project sulit selesai | Buat versi minimal terlebih dahulu |
| Payment rumit | Implementasi lama | Gunakan transaksi manual untuk versi awal |
| Penyalahgunaan platform | User tidak nyaman | Admin dapat hide review dan suspend user |

---

## 22. Roadmap Pengembangan

### Phase 1 — MVP

- Auth
- Profile
- Skill
- Need
- Offer
- Rule-based Matching
- Exchange Request
- Complete Exchange
- Review dan Reputation

### Phase 2 — Monetisasi

- Paket Gratis, Pro, Pro Max
- Featured Offer
- Featured Profile
- Portfolio Builder
- Mentoring Room
- Transaction Manual

### Phase 3 — Fitur Lanjutan

- Chat internal
- Notifikasi email/WhatsApp
- Payment gateway
- AI matching
- Dispute system
- Community workspace
- Mobile app

---

## 23. Kesimpulan

Human Skill Exchange API adalah project REST API yang simple namun memiliki konsep yang menarik dan berpotensi menghasilkan uang. Project ini tidak hanya menjadi sistem tukar skill, tetapi juga menjadi platform pertukaran kontribusi manusia berbasis skill, waktu, pengalaman, mentoring, bantuan project, dan kolaborasi kerja.

Dengan sistem matching, exchange request, progress, completion, review, dan reputasi, project ini dapat digunakan sebagai sarana kolaborasi sekaligus personal branding. Model freemium dengan paket Gratis, Pro, dan Pro Max juga membuat project ini memiliki potensi monetisasi yang jelas.

Judul akhir yang direkomendasikan:

> **Human Skill Exchange API: REST API Platform Pertukaran Skill, Waktu, Pengalaman, dan Kolaborasi Berbasis Matching dan Reputasi**

