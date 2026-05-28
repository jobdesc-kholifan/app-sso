---
trigger: manual
---

---

name: updatecode
description: Berfungsi sebagai pedoman bagi asisten AI untuk melakukan refactoring dan update kode dengan menerapkan prinsip desain perangkat lunak (SOLID, KISS, DRY, YAGNI, SoC).

---

# Update Code & Refactoring Skill

Skill ini memberikan pedoman ketat bagi asisten AI saat mengupdate, memodifikasi, atau melakukan refactoring pada kode yang ada. Tujuannya adalah memastikan kode yang dihasilkan lebih bersih, modular, mudah dipelihara, dan skalabel.

## Kata Kunci (Trigger)

- `/updatecode`
- `refactor kode ini dengan prinsip SOLID`
- `perbaiki kode berikut dengan DRY dan SoC`
- `update kode dengan best practice`

## Prinsip Utama

Setiap kali melakukan perubahan kode menggunakan skill ini, asisten **WAJIB** mengevaluasi dan menerapkan kelima prinsip berikut:

### 1. SOLID Principles

- **S - Single Responsibility**: Setiap kelas, fungsi, atau modul hanya boleh memiliki satu tanggung jawab (satu alasan untuk berubah). Pisahkan logika yang tidak berhubungan.
- **O - Open/Closed**: Entitas perangkat lunak harus terbuka untuk ekstensi (penambahan fitur baru) tetapi tertutup untuk modifikasi kode inti yang sudah berjalan.
- **L - Liskov Substitution**: Kelas turunan harus bisa menggantikan kelas dasarnya tanpa mengganggu fungsi sistem.
- **I - Interface Segregation**: Jangan memaksa klien bergantung pada fungsi yang tidak mereka butuhkan. Pisahkan abstraksi yang besar menjadi lebih spesifik.
- **D - Dependency Inversion**: Modul level atas tidak boleh bergantung pada modul level bawah, keduanya harus bergantung pada abstraksi.

### 2. KISS (Keep It Simple, Stupid)

- Kesederhanaan adalah prioritas utama dalam pengembangan.
- Hindari kompleksitas berlebihan, _over-engineering_, atau abstraksi rumit yang tidak memberikan nilai tambah nyata.
- Pastikan kode mudah dibaca dan dipahami oleh developer lain pada pandangan pertama.

### 3. DRY (Don't Repeat Yourself)

- Hindari penulisan ulang logika atau kode yang sama di berbagai tempat.
- Jika suatu blok kode atau perhitungan muncul lebih dari sekali, ekstrak menjadi fungsi pembantu (helper) atau komponen yang _reusable_.

### 4. YAGNI (You Aren't Gonna Need It)

- Jangan menambahkan fitur, konfigurasi, atau abstraksi "hanya untuk berjaga-jaga" di masa depan.
- Tulis kode yang secara ketat hanya menyelesaikan masalah atau kebutuhan saat ini.
- Kode yang tidak digunakan hanya akan menambah beban _maintenance_.

### 5. SoC (Separation of Concerns)

- Pisahkan kode ke dalam modul atau lapisan berdasarkan fokus/perannya (misal: UI, State, API Logic, Business Logic).
- Dalam pengembangan JavaScript modular, pecah logika menjadi _State Management_, _Utility Functions_, _Event Binders_, dan _DOM Manipulators_.
- Hindari mencampurkan fungsi _query_ database dengan logika tampilan (_spaghetti code_).

## Instruksi Alur Kerja Asisten

Ketika skill `/updatecode` diaktifkan, asisten harus:

1. **Pahami Konteks**: Menganalisis file atau blok kode yang diberikan pengguna.
2. **Identifikasi Pelanggaran**: Temukan kode mana yang berulang (melanggar DRY), terlalu besar/memiliki banyak tugas (melanggar SRP & SoC), atau terlalu rumit (melanggar KISS).
3. **Rancang Struktur Baru**: Tentukan arsitektur (misal: _Object Literals_, _Class_, atau _Module Pattern_) yang cocok untuk membungkus ulang kode tersebut.
4. **Tulis Ulang**: Update kode secara parsial tanpa mengubah _core behaviour_ atau fungsionalitas aslinya.
5. **Jelaskan**: Berikan _summary_ singkat mengenai apa yang direfaktor dan prinsip mana yang dipakai untuk bagian tertentu.

## Contoh Output Ekspektasi

Jika sebelumnya seluruh fungsionalitas berada dalam satu blok panjang, asisten akan membaginya:

- Modul `State` untuk menampung variabel global.
- Modul `Utils` untuk format data.
- Modul `Events` untuk menyatukan _event listener_.
- Modul `Core` untuk proses perhitungan/bisnis utama.
