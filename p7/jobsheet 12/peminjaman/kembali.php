<?php
require __DIR__ . '/../includes/auth.php';
$page_title = "Pengembalian Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$keyword = trim($_GET['q'] ?? '');

// Menambahkan a.no_hp pada SELECT serta kalkulasi tanggal jatuh tempo
$sqlDasar = "SELECT p.id, b.judul, a.nama, a.no_hp, p.tanggal_pinjam, 
                    (p.tanggal_pinjam + INTERVAL '14 days')::date AS tanggal_jatuh_tempo
             FROM peminjaman p
             JOIN buku b ON b.id = p.buku_id
             JOIN anggota a ON a.id = p.anggota_id
             WHERE p.status = 'dipinjam'";

if ($keyword !== '') {
    $stmt = $pdo->prepare($sqlDasar . " AND (b.judul ILIKE :kw OR a.nama ILIKE :kw OR a.no_hp ILIKE :kw) ORDER BY p.tanggal_pinjam");
    $stmt->execute(['kw' => '%' . $keyword . '%']);
} else {
    $stmt = $pdo->query($sqlDasar . " ORDER BY p.tanggal_pinjam");
}
$daftarAktif = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Pengembalian Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo e($flash['pesan']); ?></p>
            <?php endif; ?>

            <div class="search-box">
                <form method="get" action="kembali.php">
                    <span>
                        <label for="search-input">Cari anggota/buku/no.hp</label><br>
                        <input type="text" id="search-input" name="q" value="<?php echo e($keyword); ?>" placeholder="Nama anggota, judul buku, atau no HP...">
                    </span>
                    <button type="submit">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>No. HP</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarAktif)): ?>
                    <tr>
                        <td colspan="7">Tidak ada peminjaman aktif.</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarAktif as $trx): 
                            $isTerlambat = strtotime(date('Y-m-d')) > strtotime($trx['tanggal_jatuh_tempo']);
                        ?>
                        <tr <?php echo $isTerlambat ? 'style="background-color: #ffe6e6;"' : ''; ?>>
                            <td><?php echo e($trx['nama']); ?></td>
                            <td><?php echo e($trx['no_hp'] ?? '-'); ?></td>
                            <td><?php echo e($trx['judul']); ?></td>
                            <td><?php echo $trx['tanggal_pinjam']; ?></td>
                            <td><?php echo $trx['tanggal_jatuh_tempo']; ?></td>
                            <td>
                                <?php if ($isTerlambat): ?>
                                    <span style="color: red; font-weight: bold;">Terlambat</span>
                                <?php else: ?>
                                    <span style="color: green;">Tepat Waktu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" action="proses_kembali.php">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo $trx['id']; ?>">
                                    <button type="submit">Kembalikan</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>