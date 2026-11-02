-- File: sql/03_peminjaman.sql

CREATE TABLE IF NOT EXISTS peminjaman (
    id SERIAL PRIMARY KEY,
    anggota_id INT NOT NULL,
    buku_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_kembali DATE DEFAULT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'dipinjam',
    CONSTRAINT fk_anggota FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
    CONSTRAINT fk_buku FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
);