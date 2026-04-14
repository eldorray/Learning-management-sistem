# LMS Arrahmah — Learning Management System

<p align="center">
  <img src=".github/screenshots/hero.png" alt="LMS Arrahmah Hero" width="100%" />
</p>

<p align="center">
  <strong>Platform Pembelajaran Digital yang Modern, Cepat, dan Responsif</strong><br>
  Dirancang dengan kurikulum berkualitas tinggi untuk keselesaan, kedalaman, dan penguasaan kognitif.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Alpine.js-3-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black" />
</p>

---

## Fitur Utama

### Untuk Admin
- **Dashboard Admin** — Ringkasan statistik platform secara real-time
- **Manajemen Kursus** — Buat, edit, dan kelola kursus dengan course builder visual
- **Lesson Editor** — Editor konten pelajaran yang kaya fitur
- **Direktori Siswa & Instruktur** — Kelola semua pengguna dari satu tempat
- **Analitik & Laporan** — Insight mendalam: tren pendaftaran, distribusi level, performa kursus, siswa terbaik
- **Pengaturan Aplikasi** — Kustomisasi nama, tagline, logo, dan favicon platform
- **Pencarian Global** — Cari kursus, siswa, dan instruktur seketika

### Untuk Siswa
- **Dashboard Siswa** — Pantau progres belajar, XP, dan kursus aktif
- **Katalog Kursus** — Jelajahi dan daftar kursus yang tersedia
- **Course Learning** — Tampilan belajar immersif dengan sidebar outline materi
- **Profil & XP System** — Gamifikasi belajar dengan poin pengalaman

### Desain & UX
- **"The Lucid Scholar"** — Sistem desain custom dengan palet biru-teal yang elegan
- **Full Mobile Responsive** — Dioptimalkan untuk semua ukuran layar
- **Bottom Navigation** — Navigasi bawah khusus mobile untuk siswa
- **Bottom Sheet Modals** — Modal bergaya native pada perangkat mobile
- **Dark-mode ready** (coming soon)

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend/Reactivity | Livewire 4 + Alpine.js |
| Styling | Tailwind CSS 4 |
| Build Tool | Vite 8 |
| Database | SQLite (dev) / MySQL (prod) |
| Export | Maatwebsite Excel |
| Icons | Google Material Symbols |
| Fonts | Google Fonts (Inter, dll) |

---

## Instalasi

### Prasyarat
- PHP >= 8.3
- Composer
- Node.js >= 20
- Laravel Herd atau server PHP lokal

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/eldorray/Learning-management-sistem.git
cd Learning-management-sistem

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Build asset frontend
npm run build

# 8. (Opsional) Buat symlink storage
php artisan storage:link
```

### Jalankan Development Server

```bash
# Terminal 1 — Laravel
php artisan serve

# Terminal 2 — Vite (hot reload)
npm run dev
```

Atau jika menggunakan **Laravel Herd**, cukup akses `http://lms-arrahmah.test`.

---

## Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Admin | admin@arrahmah.com | password |
| Instruktur | instructor@arrahmah.com | password |
| Siswa | student@arrahmah.com | password |

---

## Struktur Direktori

```
app/
├── Livewire/
│   ├── Admin/          # Komponen admin panel
│   │   ├── Analytics.php
│   │   ├── AppSettings.php
│   │   ├── CourseBuilder.php
│   │   ├── CourseManagement.php
│   │   ├── CourseStudents.php
│   │   ├── Dashboard.php
│   │   ├── InstructorDirectory.php
│   │   ├── LessonEditor.php
│   │   ├── Profile.php
│   │   └── StudentDirectory.php
│   ├── Student/        # Komponen student area
│   │   ├── CourseCatalog.php
│   │   ├── CourseLearning.php
│   │   ├── Dashboard.php
│   │   └── Profile.php
│   └── GlobalSearch.php
├── Models/
│   ├── Course.php
│   ├── Enrollment.php
│   ├── Lesson.php
│   ├── Module.php
│   └── User.php
resources/
└── views/
    ├── layouts/        # Layout utama (admin, student, guest)
    └── livewire/       # Blade views tiap komponen
```

---

## Screenshots

<p align="center">
  <img src=".github/screenshots/hero.png" alt="Landing Page" width="100%" />
  <em>Landing Page — Hero Section</em>
</p>

---

## Lisensi

Project ini dibuat untuk keperluan internal **LMS Ar-Rahmah**. Hak cipta dilindungi.

---

<p align="center">
  Dibuat dengan ❤️ menggunakan Laravel + Livewire
</p>
