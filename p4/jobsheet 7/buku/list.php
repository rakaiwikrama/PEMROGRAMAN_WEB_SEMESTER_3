<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Daftar Buku';
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarBuku = $_SESSION['buku'] ?? [];
?>

<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php
            echo htmlspecialchars(
                $flash['type'],
                ENT_QUOTES,
                'UTF-8'
            );
        ?>">
            <?php
            echo htmlspecialchars(
                $flash['pesan'],
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </p>
    <?php endif; ?>

    <form
        method="post"
        action="../reset_session.php"
        onsubmit="return confirm(
            'Reset seluruh data session? Data buku, anggota, dan login akan dihapus.'
        );"
    >
        <p>
            <button type="submit" class="btn-hapus">
                Reset Data
            </button>
        </p>
    </form>

    <div class="search-box">
        <label for="search-input">Cari Judul Buku</label>
        <input
            type="text"
            id="search-input"
            placeholder="Ketik judul buku..."
        >
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="5">
                            Belum ada data buku.
                            Silakan tambah lewat menu "Tambah Buku".
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['judul'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $buku['pengarang'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </td>

                            <td><?php echo (int) $buku['tahun']; ?></td>
                            <td><?php echo (int) $buku['stok']; ?></td>

                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>