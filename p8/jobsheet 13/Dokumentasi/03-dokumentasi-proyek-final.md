# 3. Dokumentasi Proyek: README, ERD, & Manual Pengguna

Bab ini membahas dokumen-dokumen **penutup** proyek — bukan kode
program, tapi tulisan yang menjelaskan proyek secara keseluruhan untuk
pembaca yang berbeda-beda: developer lain, penguji, dan pengguna akhir.

## 3.1 `README.md` sebagai "Kartu Identitas" Proyek

Ingat pola `README.md` yang **selalu ada** di setiap jobsheet sejak
awal (dibaca sekilas di setiap bab pembuka dokumentasi ini). Yang
berbeda di jobsheet-13: `README.md` ini bukan lagi "catatan perubahan
dari jobsheet sebelumnya" — ia ditulis ulang total sebagai **snapshot
akhir** proyek: siapa pun yang baru pertama kali membuka proyek ini
(tanpa membaca 12 jobsheet sebelumnya) harus bisa memahami **apa**
aplikasi ini, **teknologi** apa yang dipakai, dan **bagaimana**
menjalankannya, hanya dari membaca `README.md` ini saja.

## 3.2 ERD Final: Empat Tabel yang Saling Terhubung

```
buku            anggota           users              peminjaman
------          --------          ------             -----------
id (PK)         id (PK)           id (PK)             id (PK)
judul           nama              nama                buku_id (FK -> buku.id)
pengarang       no_anggota (UQ)   username (UQ)        anggota_id (FK -> anggota.id)
tahun           alamat            password (hash)      tanggal_pinjam
isbn            no_hp             role                 tanggal_kembali
stok                                                    status
kategori
```

**ERD** (*Entity Relationship Diagram*) adalah diagram yang
menggambarkan **seluruh** tabel dalam database dan bagaimana mereka
saling terhubung — versi "peta lengkap" dari skema yang sudah kamu
bangun **bertahap** sejak
[dokumentasi jobsheet-08](../../jobsheet-08/Dokumentasi/02-skema-database-sql.md)
(tabel `buku`, `anggota`), lewat
[dokumentasi jobsheet-10](../../jobsheet-10/Dokumentasi/02-skema-users-dan-registrasi.md)
(tabel `users`), sampai
[dokumentasi jobsheet-12](../../jobsheet-12/Dokumentasi/02-skema-peminjaman-dan-relasi.md)
(tabel `peminjaman`). Notasi yang dipakai di sini sudah kamu kenal:

- **`(PK)`** — *Primary Key*, ingat dari
  [dokumentasi jobsheet-08 §2.4](../../jobsheet-08/Dokumentasi/02-skema-database-sql.md#24-mendefinisikan-kolom-nama-tipe-dan-batasan).
- **`(UQ)`** — *Unique*, batasan `UNIQUE` yang sudah kamu kenal dari
  file yang sama.
- **`(FK -> tabel.kolom)`** — *Foreign Key*, ingat dari
  [dokumentasi jobsheet-12 §1.2](../../jobsheet-12/Dokumentasi/01-konsep-dasar-integrasi-dan-transaksi.md#12-foreign-key-menghubungkan-antar-tabel).

Menuliskan ERD **satu kali secara utuh** di README (dibanding
menjelaskannya terpencar di berbagai jobsheet) memudahkan siapa pun
memahami **struktur data proyek secara keseluruhan** dalam sekali baca
— berguna terutama saat proyek perlu diserahterimakan ke orang lain,
atau dipresentasikan (ingat catatan di [README.md](../README.md)
jobsheet ini tentang persiapan presentasi UAS).

## 3.3 Matriks Fitur per Role

```markdown
| Fitur | Tamu (tanpa login) | Petugas (login) |
|---|---|---|
| Lihat Beranda & statistik | Ya | Ya |
| Tambah/Edit/Hapus Buku | Tidak | Ya |
| ...
```

Tabel ini merangkum **satu kali** seluruh aturan otorisasi yang sudah
kamu bangun bertahap sejak
[dokumentasi jobsheet-10](../../jobsheet-10/Dokumentasi/README.md)
(guard `auth.php`, ingat dari
[dokumentasi jobsheet-10 §4.7](../../jobsheet-10/Dokumentasi/04-guard-auth-php.md#47-halaman-mana-saja-yang-memakai-guard-ini)) —
alih-alih pembaca harus menelusuri kode satu per satu untuk tahu
halaman mana yang butuh login, tabel ini menjawabnya dalam sekali
lihat. Ini contoh nyata **dokumentasi yang berfungsi sebagai
ringkasan/indeks**, bukan mengulang penjelasan kode secara detail
(yang sudah ada di dokumentasi jobsheet masing-masing).

## 3.4 Instruksi Instalasi yang Berurutan

```bash
createdb simpus_mini
psql -d simpus_mini -f sql/01_buku_anggota.sql
psql -d simpus_mini -f sql/02_users.sql
psql -d simpus_mini -f sql/03_peminjaman.sql
```

Perhatikan catatan di [README.md](../README.md) jobsheet ini:
*"urutan penting karena `peminjaman` mereferensikan `buku`/`anggota`."*
Ingat dari
[dokumentasi jobsheet-12 §1.2](../../jobsheet-12/Dokumentasi/01-konsep-dasar-integrasi-dan-transaksi.md#12-foreign-key-menghubungkan-antar-tabel),
`REFERENCES buku(id)` di tabel `peminjaman` **mensyaratkan** tabel
`buku` sudah ada lebih dulu — kalau `03_peminjaman.sql` dijalankan
**sebelum** `01_buku_anggota.sql`, PostgreSQL akan **menolak**
membuat tabel `peminjaman` karena tabel yang dirujuknya belum ada
sama sekali. Urutan penomoran file (`01_`, `02_`, `03_`) sengaja
dipilih untuk mencerminkan urutan eksekusi yang benar ini.

## 3.5 `docs/manual-pengguna.md`: Dokumentasi untuk Pengguna Akhir, Bukan Developer

Ini jenis dokumentasi yang **berbeda tujuannya** dari seluruh
dokumentasi jobsheet yang sudah kamu baca (termasuk dokumen yang
sedang kamu baca ini!). Bandingkan:

| | Dokumentasi Jobsheet (seperti yang kamu baca ini) | `manual-pengguna.md` |
|---|---|---|
| Pembaca sasaran | Mahasiswa/developer yang ingin memahami **kode** | Petugas perpustakaan yang memakai **aplikasinya** |
| Isinya | Penjelasan `<?php ... ?>`, query SQL, konsep pemrograman | Langkah klik-per-klik: "Klik menu X, isi Y, klik Z" |
| Contoh | *"`password_hash()` mengubah teks jadi hash..."* | *"Isi Nama, Username, Password, klik **Daftar**."* |

Perhatikan potongan `manual-pengguna.md`:
```markdown
## 4. Meminjamkan Buku

1. Klik menu **Peminjaman Baru**.
2. Pilih **Anggota** dan **Buku** (hanya buku dengan stok tersedia
   yang muncul di pilihan), klik **Simpan Peminjaman**.
3. Kembali ke **Beranda** — kartu "Sedang Dipinjam" bertambah, dan
   stok buku terkait di **Daftar Buku** berkurang 1.
```

Tidak ada satu pun istilah pemrograman di sini — murni instruksi
penggunaan dari sudut pandang pengguna, mencerminkan **hasil akhir**
dari kode yang sudah kamu bedah tuntas di
[dokumentasi jobsheet-12 §3](../../jobsheet-12/Dokumentasi/03-peminjaman-baru-dan-transaction.md)
(transaction, `FOR UPDATE`, dst.) — tanpa pengguna aplikasi perlu tahu
**apa pun** tentang bagaimana itu diimplementasikan di baliknya.

## 3.6 Placeholder `[Screenshot]`

```markdown
> Catatan: karena disusun di luar environment yang punya PostgreSQL
> aktif, dokumen ini berisi langkah bertekstur lengkap sebagai
> pengganti tangkapan layar. Saat dijalankan di lab..., sisipkan
> screenshot nyata pada tiap langkah bertanda **[Screenshot]**.
```

Ini contoh jujur tentang keterbatasan **saat menulis dokumentasi**:
manual pengguna yang ideal biasanya menyertakan tangkapan layar
sungguhan di tiap langkah (memudahkan pengguna mencocokkan apa yang
mereka lihat di layar), tapi menulis dokumen ini dilakukan di
lingkungan yang **tidak bisa** benar-benar menjalankan aplikasinya
(tidak ada PostgreSQL aktif, ingat catatan yang sama muncul di
[README.md](../README.md) jobsheet ini). Alih-alih berpura-pura sudah
lengkap, penanda `[Screenshot: ...]` secara eksplisit menandai
**bagian yang belum selesai**, dengan instruksi jelas apa yang perlu
dilengkapi nanti — kebiasaan menulis dokumentasi yang jujur dan mudah
ditindaklanjuti, dibanding menyembunyikan bahwa sesuatu belum lengkap.

Lanjut ke: [Rangkuman & Latihan Lanjutan](04-rangkuman-latihan.md)
