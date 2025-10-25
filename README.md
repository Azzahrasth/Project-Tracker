# Mini Aplikasi Project Tracker

[![Laravel v11.x](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com/)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap)](https://getbootstrap.com/)
[![jQuery & AJAX](https://img.shields.io/badge/JavaScript-jQuery%20%26%20AJAX-0769AD?style=for-the-badge&logo=jquery)](https://jquery.com/)

Mini Aplikasi Project Tracker adalah solusi *Single Page Application* (SPA) sederhana yang dibuat menggunakan Laravel Blade dan AJAX untuk mengelola daftar proyek dan tugas (task) secara *realtime* tanpa perlu *page reload*.

Aplikasi ini secara otomatis menghitung *Completion Progress* dan *Status* Project berdasarkan bobot dan status setiap Task di dalamnya.

---

## Fitur Utama

Aplikasi ini dikembangkan berdasarkan prinsip *Realtime Data Manipulation* dan *Clean Code*.

1.  **CRUD Project & Task:** Fungsionalitas penuh untuk membuat, membaca, memperbarui, dan menghapus Project dan Task.
2.  **Perhitungan Progress Dinamis:** *Completion Progress* Project dihitung otomatis berdasarkan total bobot Task yang berstatus 'Done'.
    $$\text{Progress} = \frac{\text{Total Bobot Task Done}}{\text{Total Bobot Seluruh Task}} \times 100$$
3.  **Status Project Otomatis:** Status Project ditentukan berdasarkan hierarki status Task:
    * Jika **Semua** Task **Done** $\rightarrow$ Project **Done**.
    * Jika **Ada minimal satu** Task **In Progress atau Done** $\rightarrow$ Project **In Progress**.
    * Jika **Semua** Task **Draft** $\rightarrow$ Project **Draft**.
4.  **Realtime UI:** Menggunakan **AJAX (jQuery)** untuk semua operasi CRUD (Add, Edit, Delete) Project dan Task, memungkinkan pembaruan *Progress Bar* dan Status di DOM secara instan tanpa memuat ulang halaman.
5.  **Notifikasi Modern:** Menggunakan **SweetAlert2** untuk konfirmasi penghapusan dan notifikasi validasi/sukses yang profesional.

---

## 🛠️ Instalasi dan Setup Proyek

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal.

### Prasyarat

* PHP (8.1+)
* Composer
* MySQL/MariaDB
* Node.js & npm (untuk aset front-end dasar)

### Langkah-langkah Instalasi

1.  **Clone Repositori:**
    ```bash
    git clone [https://github.com/Azzahrasth/Project-Tracker.git](https://github.com/Azzahrasth/Project-Tracker.git)
    cd Project-Tracker
    ```

2.  **Instal Dependensi PHP & Laravel:**
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Edit file `.env` dan atur detail koneksi database Anda (`DB_DATABASE`, `DB_USERNAME`, dll.).

4.  **Jalankan Migrasi Database:**
    ```bash
    php artisan migrate
    ```
    *(Ini akan membuat tabel `projects` dan `tasks`.)*

5.  **Jalankan Aplikasi:**
    ```bash
    php artisan serve
    ```
    Aplikasi sekarang dapat diakses melalui browser di `http://127.0.0.1:8000`.
