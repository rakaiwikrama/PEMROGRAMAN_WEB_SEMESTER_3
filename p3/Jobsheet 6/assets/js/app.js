// Salinan tabel awal untuk simulasi pemuatan ulang.
let dataTabelAwal = null;
let sedangMemuat = false;

// ==========================================
// HAMBURGER MENU
// ==========================================
function initNavToggle() {
    const tombol = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");

    if (!tombol || !nav) {
        return;
    }

    tombol.setAttribute(
        "aria-expanded",
        String(nav.classList.contains("nav-open"))
    );

    tombol.addEventListener("click", function () {
        const terbuka = nav.classList.toggle("nav-open");

        tombol.setAttribute("aria-expanded", String(terbuka));
    });
}

// ==========================================
// COUNTER BUKU
// ==========================================
function updateBookCounter() {
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );
    const counter = document.getElementById("book-counter");

    if (!tbody || !counter) {
        return;
    }

    const semuaBaris = tbody.querySelectorAll("tr");
    let jumlahTampil = 0;

    semuaBaris.forEach(function (baris) {
        if (baris.style.display !== "none") {
            jumlahTampil++;
        }
    });

    counter.textContent =
        "Menampilkan " +
        jumlahTampil +
        " dari " +
        semuaBaris.length +
        " buku";
}

// ==========================================
// TERAPKAN FILTER BERDASARKAN JUDUL
// ==========================================
function terapkanFilterBuku() {
    const input = document.getElementById("search-input");
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );

    if (!tbody) {
        return;
    }

    const keyword = input
        ? input.value.toLowerCase().trim()
        : "";

    tbody.querySelectorAll("tr").forEach(function (baris) {
        const kolomJudul = baris.querySelector("td");

        if (!kolomJudul) {
            return;
        }

        const judul = kolomJudul.textContent.toLowerCase().trim();

        baris.style.display = judul.includes(keyword) ? "" : "none";
    });

    updateBookCounter();
}

// ==========================================
// INISIALISASI PENCARIAN
// ==========================================
function initTableFilter() {
    const input = document.getElementById("search-input");
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );

    if (!input || !tbody) {
        return;
    }

    input.addEventListener("input", terapkanFilterBuku);

    terapkanFilterBuku();
}

// ==========================================
// KONFIRMASI HAPUS
// Listener tetap bekerja setelah isi tabel diganti.
// ==========================================
function initHapusConfirm() {
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );

    if (!tbody) {
        return;
    }

    tbody.addEventListener("click", function (event) {
        if (!(event.target instanceof Element)) {
            return;
        }

        const tombol = event.target.closest(".btn-hapus");

        if (!tombol || !tbody.contains(tombol)) {
            return;
        }

        const baris = tombol.closest("tr");

        if (!baris) {
            return;
        }

        const kolomJudul = baris.querySelector("td");
        const judul = kolomJudul
            ? kolomJudul.textContent.trim()
            : "buku ini";

        const yakin = confirm(
            'Yakin ingin menghapus buku "' + judul + '"?'
        );

        if (yakin) {
            baris.remove();
            updateBookCounter();
        }
    });
}

// ==========================================
// SIMPAN SALINAN DATA TABEL HTML AWAL
// Dipanggil sekali sebelum filter diterapkan.
// ==========================================
function initDataBuku() {
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );

    if (!tbody) {
        return;
    }

    dataTabelAwal = tbody.innerHTML;
}

// ==========================================
// MUAT DAFTAR BUKU
// Versi simulasi: mengembalikan data HTML awal.
// Belum mengambil JSON atau API menggunakan fetch.
// ==========================================
async function muatDaftarBuku() {
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );
    const loading = document.getElementById("loading");
    const tombol = document.getElementById("reload-books-btn");

    if (!tbody || dataTabelAwal === null || sedangMemuat) {
        return;
    }

    sedangMemuat = true;

    if (tombol) {
        tombol.disabled = true;
        tombol.textContent = "Memuat...";
    }

    if (loading) {
        loading.style.display = "block";
    }

    tbody.setAttribute("aria-busy", "true");
    tbody.innerHTML = "";
    updateBookCounter();

    try {
        // Jeda untuk demonstrasi indikator loading.
        // Ini bukan permintaan ke server.
        await new Promise(function (resolve) {
            setTimeout(resolve, 300);
        });

        tbody.innerHTML = dataTabelAwal;

        // Kata kunci yang sedang diketik tetap diterapkan.
        terapkanFilterBuku();
    } finally {
        sedangMemuat = false;
        tbody.setAttribute("aria-busy", "false");

        if (loading) {
            loading.style.display = "none";
        }

        if (tombol) {
            tombol.disabled = false;
            tombol.textContent = "Muat Ulang";
        }
    }
}

// ==========================================
// TOMBOL MUAT ULANG
// ==========================================
function initMuatUlang() {
    const tombol = document.getElementById("reload-books-btn");

    if (!tombol) {
        return;
    }

    tombol.addEventListener("click", async function () {
        try {
            await muatDaftarBuku();
        } catch (error) {
            console.error("Gagal memuat ulang daftar buku:", error);
            alert("Daftar buku gagal dimuat ulang.");
        }
    });
}

// ==========================================
// PESAN ERROR FORM
// ==========================================
function tampilkanError(input, pesan) {
    hapusError(input);

    const elemenError = document.createElement("span");

    elemenError.className = "error";
    elemenError.textContent = pesan;
    elemenError.setAttribute("aria-live", "polite");

    input.classList.add("input-error");
    input.setAttribute("aria-invalid", "true");
    input.insertAdjacentElement("afterend", elemenError);
}

function hapusError(input) {
    const elemenBerikutnya = input.nextElementSibling;

    if (
        elemenBerikutnya &&
        elemenBerikutnya.classList.contains("error")
    ) {
        elemenBerikutnya.remove();
    }

    input.classList.remove("input-error");
    input.removeAttribute("aria-invalid");
}

// ==========================================
// VALIDASI SATU FIELD
// ==========================================
function validasiSatuField(input) {
    const nilaiAsli = input.value;
    const nilai = nilaiAsli.trim();

    const label = input.labels && input.labels[0]
        ? input.labels[0].textContent.trim()
        : input.name;

    if (input.validity.badInput) {
        tampilkanError(input, label + " harus berupa angka.");
        return false;
    }

    if (input.required && nilai === "") {
        tampilkanError(input, label + " wajib diisi.");
        return false;
    }

    // ISBN kosong valid, tetapi spasi saja bukan ISBN kosong.
    if (input.name === "isbn") {
        if (
            nilaiAsli !== "" &&
            !/^[0-9-]+$/.test(nilaiAsli)
        ) {
            tampilkanError(
                input,
                "ISBN hanya boleh berisi angka dan tanda hubung (-)."
            );
            return false;
        }

        hapusError(input);
        return true;
    }

    // Field opsional lainnya boleh kosong.
    if (nilai === "") {
        hapusError(input);
        return true;
    }

    if (input.name === "tahun") {
        const tahun = Number(nilai);

        if (
            !Number.isInteger(tahun) ||
            tahun < 1900 ||
            tahun > 2026
        ) {
            tampilkanError(
                input,
                "Tahun harus berupa bilangan bulat antara 1900–2026."
            );
            return false;
        }
    }

    if (input.name === "stok") {
        const stok = Number(nilai);

        if (!Number.isInteger(stok) || stok < 0) {
            tampilkanError(
                input,
                "Stok harus berupa bilangan bulat minimal 0."
            );
            return false;
        }
    }

    hapusError(input);
    return true;
}

// ==========================================
// VALIDASI FORM DENGAN ARRAY
// ==========================================
function initValidasiForm() {
    const form = document.getElementById("form-tambah");

    if (!form) {
        return;
    }

    // Pesan validasi ditangani oleh JavaScript.
    form.noValidate = true;

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
        let inputErrorPertama = null;

        daftarInput.forEach(function (input) {
            const valid = validasiSatuField(input);

            if (!valid) {
                formValid = false;

                if (!inputErrorPertama) {
                    inputErrorPertama = input;
                }
            }
        });

        if (!formValid) {
            if (inputErrorPertama) {
                inputErrorPertama.focus();
            }

            return;
        }

        alert(
            "Validasi berhasil. Ini simulasi; data belum disimpan."
        );

        form.reset();

        daftarInput.forEach(function (input) {
            hapusError(input);
        });
    });

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
    initDataBuku();
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    initMuatUlang();
});