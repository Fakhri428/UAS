# Product Requirement Document (PRD) — Koukan Ecosystem

## 1. Informasi Project
* **Nama Platform:** Koukan
* **Tagline:** *Exchange Skills, Build Connections.*
* **Jenis Project:** Aplikasi Web Responsif (Koukan Web Platform)
* **Kategori:** Platform Pertukaran Kontribusi (Skill, Waktu, & Kolaborasi)
* **Model Bisnis:** Koukan Freemium (Koukan Free, Koukan Pro, Koukan Pro Max)

---

## 2. Ringkasan Eksekutif
**Koukan (交換)** adalah platform web yang mempertemukan pengguna untuk saling bertukar keahlian, waktu, dan bantuan project tanpa melibatkan uang. 

Platform ini dirancang agar pengguna bisa saling membantu menyelesaikan kebutuhan mereka menggunakan keahlian yang dimiliki melalui sistem **Koukan Match**, sekaligus membangun portofolio nyata dan reputasi digital yang diukur melalui **Koukan Score**.

---

## 3. Alur Utama Pengguna (Koukan User Journey)
1. **Koukan Join:** Pengguna mendaftar akun dan melengkapi profil (menentukan keahlian & portofolio).
2. **Koukan Post:** Pengguna mempublikasikan **Koukan Offer** (bantuan yang bisa diberikan) atau **Koukan Need** (bantuan yang dicari).
3. **Koukan Match:** Pengguna masuk ke halaman pencocokan untuk melihat rekomendasi pengguna lain yang memiliki kecocokan dua arah (simbiosis mutualisme).
4. **Koukan Exchange:** Pengguna mengirim permintaan barter. Jika disetujui, halaman **Koukan Workspace** akan aktif untuk melacak progres kerja bersama.
5. **Koukan Review:** Setelah pekerjaan selesai, kedua pengguna memberikan rating untuk membentuk skor reputasi publik (**Koukan Score**).

---

## 4. Ruang Lingkup Fitur (MVP)

### 4.1 Fitur Utama (In-Scope)
* **Koukan Profile:** Halaman profil publik yang menampilkan portofolio, badge paket, list keahlian, dan akumulasi nilai **Koukan Score**.
* **Koukan Board (Dashboard):** Halaman manajemen untuk membuat, mengedit, dan menghapus postingan *Koukan Offer* dan *Koukan Need*.
* **Koukan Match Engine:** Sistem filter dan pencocokan otomatis berbasis kecocokan kategori, lokasi, dan mode kerja antara *Offer* dan *Need*.
* **Koukan Room (Workspace):** Halaman khusus dua user yang sedang melakukan barter untuk melacak status transaksi hingga selesai.
* **Koukan Review System:** Form penilaian bintang dan feedback setelah aktivitas barter dinyatakan selesai.

### 4.2 Di Luar Ruang Lingkup (Out-of-Scope Phase 1)
* **Koukan Chat:** Fitur pesan instan real-time internal (sementara diarahkan ke kontak sosial media/WhatsApp di profil).
* **Koukan Pay:** Otomatisasi pembayaran paket premium (sementara menggunakan sistem *upload* bukti transfer manual ke Admin).

---

## 5. Arsitektur Halaman Web (Koukan Sitemap)
* `/` — **Koukan Home:** Landing page berisi informasi umum, edukasi konsep barter skill, dan tombol daftar.
* `/login` & `/register` — **Koukan Auth**
* `/dashboard` — **Koukan Board:** Pusat aktivitas user, grafik performa, dan rekomendasi kecocokan terbaru.
* `/explore` — **Koukan Market:** Tempat menjelajahi direktori postingan *Koukan Offer* dan *Koukan Need* secara publik.
* `/workspace/{koukan_id}` — **Koukan Room:** Ruang kolaborasi dan pelacakan status barter yang sedang berjalan.
* `/user/{username}` — **Koukan ID:** Profil publik digital tempat memamerkan reputasi dan portofolio.

---

## 6. Desain Database Inti

### 6.1 Tabel: `users`
* `id` (PK)
* `name`, `email`, `password`
* `koukan_plan` (enum: 'free', 'pro', 'pro_max')
* `koukan_score` (decimal, rata-rata rating reputasi dari user lain)

### 6.2 Tabel: `koukan_posts`
* `id` (PK)
* `user_id` (FK)
* `koukan_post_type` (enum: 'offer', 'need')
* `title`, `category`, `description`
* `work_mode` (enum: 'online', 'offline')

### 6.3 Tabel: `koukan_exchanges`
* `id` (PK)
* `sender_id` (FK User yang mengajukan)
* `receiver_id` (FK User yang menerima)
* `koukan_post_id` (FK ke tabel koukan_posts)
* `koukan_status` (enum: 'pending', 'accepted', 'in_progress', 'completed', 'cancelled')
* `confirmed_by_sender` (boolean)
* `confirmed_by_receiver` (boolean)

---

## 7. Indikator Keberhasilan (Success Metrics)
1. Setiap akun baru berhasil terdaftar dan memiliki **Koukan ID** (profil publik) yang rapi.
2. **Koukan Match Engine** berhasil menampilkan kecocokan silang secara akurat di halaman dashboard.
3. Alur status pada **Koukan Exchange** berpindah lancar dari `pending` hingga otomatis menjadi `completed` saat kedua pihak melakukan konfirmasi di **Koukan Room**.
4. Nilai **Koukan Score** pada profil user otomatis ter-update secara *real-time* setelah review dikirimkan.