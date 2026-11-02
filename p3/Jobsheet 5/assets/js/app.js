// ==========================================
// HAMBURGER MENU
// ==========================================
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!toggleBtn || !nav) {
        return;
    }

    toggleBtn.setAttribute("aria-expanded", "false");

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");

        const menuTerbuka = nav.classList.contains("nav-open");

        toggleBtn.setAttribute("aria-expanded", menuTerbuka);
    });
}

// ==========================================
// MEMPERBARUI COUNTER DATA BUKU
// ==========================================
function updateBookCounter() {
    const tabel = document.querySelector(".table-responsive table");
    const counter = document.getElementById("book-counter");

    if (!tabel || !counter) {
        return;
    }

    const semuaBaris = tabel.querySelectorAll("tbody tr");
    let jumlahTampil = 0;

    semuaBaris.forEach(function (baris) {
        if (baris.style.display !== "none") {
            jumlahTampil++;
        }
    });

    const jumlahTotal = semuaBaris.length;

    counter.textContent =
        "Menampilkan " +
        jumlahTampil +
        " dari " +
        jumlahTotal +
        " buku";
}

// ==========================================
// FILTER PENCARIAN BERDASARKAN JUDUL BUKU
// ==========================================
function initTableFilter() {
    const inputSearch = document.getElementById("search-input");
    const tabel = document.querySelector(".table-responsive table");

    if (!inputSearch || !tabel) {
        return;
    }

    // Menampilkan jumlah buku saat halaman pertama kali dibuka.
    updateBookCounter();

    inputSearch.addEventListener("input", function () {
        const keyword = inputSearch.value.toLowerCase().trim();
        const semuaBaris = tabel.querySelectorAll("tbody tr");

        semuaBaris.forEach(function (baris) {
            const kolomJudul = baris.querySelector("td:first-child");

            if (!kolomJudul) {
                return;
            }

            const judulBuku = kolomJudul.textContent.toLowerCase().trim();

            if (judulBuku.includes(keyword)) {
                baris.style.display = "";
            } else {
                baris.style.display = "none";
            }
        });

        // Counter diperbarui setelah hasil pencarian berubah.
        updateBookCounter();
    });
}

// ==========================================
// KONFIRMASI DAN HAPUS BUKU
// ==========================================
function initHapusConfirm() {
    const tombolHapus = document.querySelectorAll(".btn-hapus");

    tombolHapus.forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");

            if (!row) {
                return;
            }

            const judulCell = row.querySelector("td:first-child");
            const judulBuku = judulCell
                ? judulCell.textContent.trim()
                : "buku ini";

            const yakin = confirm(
                'Yakin ingin menghapus buku "' + judulBuku + '"?'
            );

            if (yakin) {
                row.remove();

                // Counter diperbarui setelah buku dihapus.
                updateBookCounter();
            }
        });
    });
}

// ==========================================
// FUNGSI BANTU UNTUK PESAN ERROR
// ==========================================
function tampilkanError(input, pesan) {
    hapusError(input);

    const pesanError = document.createElement("span");

    pesanError.className = "error";
    pesanError.textContent = pesan;

    input.classList.add("input-error");
    input.insertAdjacentElement("afterend", pesanError);
}

function hapusError(input) {
    const elemenSetelahInput = input.nextElementSibling;

    if (
        elemenSetelahInput &&
        elemenSetelahInput.classList.contains("error")
    ) {
        elemenSetelahInput.remove();
    }

    input.classList.remove("input-error");
}

// ==========================================
// VALIDASI SATU FIELD FORM
// ==========================================
function initValidasiForm() {
    const form = document.getElementById("form-tambah");

    if (!form) {
        return;
    }

    const namaFields = [
        "judul",
        "pengarang",
        "tahun",
        "isbn",
        "stok"
    ];

    const daftarInput = namaFields
        .map(function (nama) {
            return form.querySelector("[name='" + nama + "']");
        })
        .filter(function (input) {
            return input !== null;
        });

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let formValid = true;

        daftarInput.forEach(function (input) {
            const fieldValid = validasiSatuField(input);

            if (!fieldValid) {
                formValid = false;
            }
        });

        if (formValid) {
            alert("Data buku berhasil disimpan.");
            form.reset();
        }
    });

    daftarInput.forEach(function (input) {
        input.addEventListener("input", function () {
            validasiSatuField(input);
        });
    });
}

// ==========================================
// VALIDASI FORM TAMBAH BUKU DENGAN ARRAY
// ==========================================
function initValidasiForm() {
    const form = document.getElementById("form-tambah");

    // Pada halaman lain, misalnya index.html atau list.html,
    // form ini memang tidak ada.
    if (!form) {
        return;
    }

    const namaFields = [
        "judul",
        "pengarang",
        "tahun",
        "isbn",
        "stok"
    ];

    const daftarInput = namaFields
        .map(function (nama) {
            return form.querySelector("[name='" + nama + "']");
        })
        .filter(function (input) {
            return input !== null;
        });

    form.addEventListener("submit", function (event) {
        // Belum memakai PHP, database, atau backend.
        event.preventDefault();

        let formValid = true;

        daftarInput.forEach(function (input) {
            const fieldValid = validasiSatuField(input);

            if (!fieldValid) {
                formValid = false;
            }
        });

        if (formValid) {
            alert("Data buku berhasil disimpan.");
            form.reset();
        }
    });

    // Memvalidasi ulang field ketika pengguna mengetik.
    daftarInput.forEach(function (input) {
        input.addEventListener("input", function () {
            validasiSatuField(input);
        });
    });
}

// ==========================================
// MENJALANKAN SEMUA FITUR
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
});