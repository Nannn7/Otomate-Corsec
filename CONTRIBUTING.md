# Panduan Kontribusi

Terima kasih atas minat Anda untuk berkontribusi pada proyek ini! Kami menghargai setiap bentuk kontribusi, baik itu melaporkan bug, menyarankan fitur baru, atau mengirimkan kode.

Panduan ini akan membantu Anda memulai.

## Kode Etik

Sebagai kontributor dan pemelihara proyek ini, kami berkomitmen untuk menciptakan lingkungan yang ramah, inklusif, dan saling menghormati. Kami mendorong semua kontributor untuk membaca dan mematuhi [Kode Etik Kontributor](https://www.contributor-covenant.org/version/2/0/code_of_conduct/)(Opsional, jika Anda memiliki yang spesifik, tautkan di sini).

## Bagaimana Cara Berkontribusi?

Ada beberapa cara Anda dapat berkontribusi pada proyek ini:

### Melaporkan Bug

Jika Anda menemukan bug, silakan laporkan di [halaman Issues kami](https://git.putrakuningan.com/putrakuningan/dashboard-template/issues).
Saat melaporkan bug, harap berikan informasi sebanyak mungkin:

* **Langkah-langkah untuk mereproduksi:** Deskripsikan secara jelas bagaimana kami dapat mereproduksi bug tersebut.
* **Perilaku yang diharapkan:** Apa yang seharusnya terjadi.
* **Perilaku yang diamati:** Apa yang sebenarnya terjadi.
* **Versi:** Versi proyek atau tag yang Anda gunakan.
* **Lingkungan:** Sistem operasi, browser, versi PHP/Node.js, dll.
* Sertakan tangkapan layar atau GIF jika relevan.

### Menyarankan Fitur Baru

Kami menyambut ide-ide baru! Jika Anda memiliki saran untuk fitur baru, silakan buka Issue baru di [halaman Issues kami](https://git.putrakuningan.com/putrakuningan/dashboard-template/issues) dengan label "feature request" atau "enhancement". Jelaskan ide Anda sejelas mungkin dan mengapa menurut Anda itu akan bermanfaat bagi proyek.

### Kontribusi Kode

Jika Anda ingin berkontribusi kode, ikuti langkah-langkah berikut:

1.  **Fork** repositori ini ke akun GitHub Anda.
2.  **Kloning** repositori yang sudah Anda fork ke mesin lokal Anda:
    ```bash
    git clone [https://github.com/AkunGitHubAnda/NamaRepoAnda.git](https://github.com/AkunGitHubAnda/NamaRepoAnda.git)
    cd NamaRepoAnda
    ```
3.  **Buat Cabang Baru:**
    Selalu bekerja pada cabang baru untuk fitur atau perbaikan bug Anda. Beri nama cabang Anda secara deskriptif (misal: `fitur/nama-fitur-baru` atau `perbaikan/nama-perbaikan-bug`).
    ```bash
    git checkout -b fitur/nama-fitur-baru
    ```
4.  **Lakukan Perubahan Anda:**
    Tulis kode Anda, tambahkan tes jika diperlukan, dan pastikan semua tes lulus. Pastikan kode Anda mematuhi standar coding proyek (jika ada).
5.  **Commit Perubahan Anda:**
    Gunakan pesan commit yang jelas dan deskriptif.
    ```bash
    git add .
    git commit -m "feat: Tambahkan fitur baru untuk X"
    ```
    *Tips: Pertimbangkan untuk menggunakan [Konvensi Pesan Commit](https://www.conventionalcommits.org/en/v1.0.0/) (misal: `feat:`, `fix:`, `docs:`, `chore:`, dll.) untuk konsistensi.*
6.  **Push ke Repositori Anda:**
    ```bash
    git push origin fitur/nama-fitur-baru
    ```
7.  **Buka Pull Request (PR):**
    Buka Pull Request dari cabang Anda di repositori yang Anda fork ke cabang `main` (atau `master`) di repositori utama.
    * Berikan judul PR yang jelas dan deskriptif.
    * Jelaskan secara singkat perubahan yang Anda buat dan mengapa.
    * Tautkan ke Issue terkait jika ada (misal: `Closes #123`).
    * Tunggu ulasan dari maintainer proyek. Kami akan berusaha untuk meninjau PR Anda secepat mungkin.

## Standar Kode (Opsional)

[Jika proyek Anda memiliki standar coding spesifik (misal: PSR-2 untuk PHP, ESLint untuk JS), jelaskan di sini atau tautkan ke dokumentasi terpisah.]

## Pertanyaan?

Jika Anda memiliki pertanyaan tentang bagaimana cara berkontribusi, jangan ragu untuk membuka Issue atau menghubungi kami di [alamat email/platform komunikasi lain jika ada].

Terima kasih lagi atas kontribusi Anda!