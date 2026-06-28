# Status Implementasi API — Human Skill Exchange

> Dokumen ini dibuat **2026-06-23** berdasarkan **verifikasi langsung terhadap kode** (`routes/api.php` + controller),
> bukan sekadar klaim dokumen analisis lama. Acuan kontrak: **PRD §14** (`PRD_Human_Skill_Exchange_API.md`).
>
> Legenda status:
> - ✅ **Sudah** — sudah ada sebelum sesi ini & berfungsi.
> - 🆕 **Baru** — ditambahkan/diperbaiki pada sesi 2026-06-23.
> - ⚠️ **Catatan** — ada keputusan desain / perlu diperhatikan.

---

## Ringkasan

Pada sesi ini seluruh endpoint PRD §14 yang sebelumnya **belum ada route-nya** telah diimplementasikan,
satu bug pada modul transaksi diperbaiki, dan **Admin API** dibuat dari nol.

| Modul | Sebelum | Sesudah |
|---|---|---|
| Auth, Skill, Need, Offer, Portfolio (CRUD), Exchange Type | ✅ lengkap | ✅ lengkap |
| Profil publik per-user | ❌ tidak ada | 🆕 ada |
| Matching by path (`offers/{id}/matches`, `needs/{id}/matches`) | ❌ (hanya query param) | 🆕 ada |
| Exchange request `/status` & `/complete` (named) | ❌ (hanya generic patch) | 🆕 ada |
| Progress bersarang di exchange request | ❌ (hanya query param) | 🆕 ada |
| Review/Reputation/Portfolio per-user (`users/{id}/...`) | ❌ tidak ada | 🆕 ada |
| Booking via `mentoring-rooms/{id}/book` | ❌ tidak ada | 🆕 ada |
| Transaksi `confirm` + bug kolom | ❌ rusak | 🆕 diperbaiki |
| **Admin API** | ❌ tidak ada sama sekali | 🆕 lengkap |

---

## Detail per endpoint (PRD §14)

### 14.1 Auth
| Endpoint | Status |
|---|---|
| `POST /api/register` | ✅ |
| `POST /api/login` | ✅ |
| `POST /api/logout` | ✅ |
| `GET /api/me` (+ alias `/user`) | ✅ |

### 14.2 Profile
| Endpoint | Status |
|---|---|
| `GET /api/profile` | ✅ |
| `POST /api/profile` (+ `PUT`) | ✅ |
| `GET /api/users/{id}/profile` | 🆕 `ProfileController@publicProfile` (tanpa email demi privasi) |

### 14.3 Subscription
| Endpoint | Status |
|---|---|
| `GET /api/plans` | ✅ |
| `GET /api/my-plan` | 🆕 alias → `PlanController@current` |
| `POST /api/subscribe` | 🆕 alias → `PlanController@subscribe` |
| `PATCH /api/subscribe/cancel` | 🆕 alias → `PlanController@cancel` |

⚠️ Penamaan internal lama (`/subscription` GET/POST/PATCH) **tetap dipertahankan** agar klien lama tidak rusak.

### 14.4–14.6 Skill / Need / Offer
| Endpoint | Status |
|---|---|
| CRUD `skills`, `needs`, `offers` (index/show/store/update/destroy) | ✅ |

### 14.7 Exchange Type
| Endpoint | Status |
|---|---|
| `GET /api/exchange-types` | ✅ |

### 14.8 Matching
| Endpoint | Status |
|---|---|
| `GET /api/matches` | ✅ |
| `GET /api/offers/{id}/matches` | 🆕 `MatchController@offerMatches` |
| `GET /api/needs/{id}/matches` | 🆕 `MatchController@needMatches` |

### 14.9 Exchange Request
| Endpoint | Status |
|---|---|
| `GET /api/exchange-requests` & `/{id}` | ✅ |
| `POST /api/exchange-requests` | ✅ |
| `PATCH /api/exchange-requests/{id}/status` | 🆕 `@status` |
| `PATCH /api/exchange-requests/{id}/complete` | 🆕 `@markComplete` |

### 14.10 Exchange Progress
| Endpoint | Status |
|---|---|
| `GET /api/exchange-requests/{id}/progress` | 🆕 `@indexForRequest` |
| `POST /api/exchange-requests/{id}/progress` | 🆕 `@storeForRequest` |
| `PUT /api/exchange-progress/{id}` | ✅ |
| `DELETE /api/exchange-progress/{id}` | ✅ |

### 14.11 Review
| Endpoint | Status |
|---|---|
| `POST /api/reviews` | ✅ |
| `GET /api/users/{id}/reviews` | 🆕 `ReviewController@forUser` (review `is_hidden` disembunyikan) |
| `GET /api/users/{id}/reputation` | 🆕 `ReputationController@forUser` |

### 14.12 Portfolio
| Endpoint | Status |
|---|---|
| CRUD `portfolios` | ✅ |
| `GET /api/users/{id}/portfolios` | 🆕 `PortfolioController@forUser` |

### 14.13 Mentoring Room
| Endpoint | Status |
|---|---|
| CRUD `mentoring-rooms` | ✅ |
| `POST /api/mentoring-rooms/{id}/book` | 🆕 `MentoringBookingController@book` |

### 14.14 Transaction
| Endpoint | Status |
|---|---|
| `GET /api/transactions` | 🆕 diperbaiki — **scope per-user** (admin lihat semua), format `BaseApiController` |
| `GET /api/transactions/{id}` | 🆕 diperbaiki — cek kepemilikan |
| `POST /api/transactions` | 🆕 diperbaiki — validasi sesuai kolom asli (`type, reference_id, amount, platform_fee, payment_method`) |
| `PATCH /api/transactions/{id}/confirm` | 🆕 `@confirm` |

⚠️ **Bug diperbaiki:** sebelumnya `TransactionController@store` & model `Transaction` memakai kolom
`currency` dan `meta` yang **tidak ada** di tabel `transactions` → setiap pembuatan transaksi pasti error SQL.
`index` lama juga membocorkan transaksi semua user. Keduanya sudah dibereskan.

### 14.15 Admin API — 🆕 dibuat dari nol (`Api\AdminController`)
| Endpoint | Status |
|---|---|
| `GET /api/admin/users` | 🆕 |
| `GET /api/admin/exchanges` | 🆕 |
| `GET /api/admin/reviews` (termasuk yang hidden) | 🆕 |
| `GET /api/admin/transactions` | 🆕 |
| `PATCH /api/admin/users/{id}/verify` | 🆕 set `is_verified = true` |
| `PATCH /api/admin/reviews/{id}/hide` | 🆕 set `is_hidden = true` |

Gating: setiap method mengecek `user->role === 'admin'`, jika bukan → `403`.

---

## Perubahan skema (di luar SQL dump awal)

Dua kolom ditambahkan untuk mendukung fitur admin PRD §14.15. Sesuai prinsip migration per-tabel,
keduanya ditulis di file migration tabel terkait (tinggal diedit bila perlu):

| Tabel | Kolom baru | File migration | Alasan |
|---|---|---|---|
| `users` | `is_verified` BOOLEAN default false | `2026_06_15_035002_add_role_and_plan_id_to_users_table.php` | badge verifikasi admin |
| `reviews` | `is_hidden` BOOLEAN default false | `2026_06_15_035009_create_reviews_table.php` | sembunyikan review oleh admin |

Model diperbarui: `User` (fillable + cast `is_verified`), `Review` (fillable + cast `is_hidden`),
`Transaction` (fillable diselaraskan dengan kolom tabel sebenarnya).

---

## Akun seed untuk pengujian

| Nama | Email | Role | Token (Bearer) |
|---|---|---|---|
| Fakhri | fakhri@example.com | user | `fakhri-token-123` |
| Raka | raka@example.com | user | `raka-token-123` |
| Admin Human Skill | admin@hse.test | admin | `admin-token-123` |

Password semua akun: `password123`.

Contoh:
```bash
curl -H "Authorization: Bearer admin-token-123" http://127.0.0.1:8000/api/admin/users
curl -H "Authorization: Bearer fakhri-token-123" http://127.0.0.1:8000/api/users/2/profile
```

---

## Yang BELUM dikerjakan / di luar scope sesi ini

- **Dokumentasi API (rapidoc/OpenAPI)** — masih ditunda (lihat `ANALISIS_PRD_IMPLEMENTASI.md`).
- **Sisi Web/Blade** (gap di `ANALISIS_PRD_DAN_WEB.md`) — fokus sesi ini pada REST API, bukan UI web.
- **Penamaan project final** (satu kata) — belum diputuskan.
- Notifikasi email/realtime lanjutan di luar yang sudah ada (`BookingApproved/DeclinedNotification`).
