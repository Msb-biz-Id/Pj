<?= view('errors/html/_shell', [
    'code'      => $code ?? 500,
    'pageTitle' => $code === 404 ? 'Halaman tidak ditemukan' : 'Terjadi kesalahan',
    'pageDesc'  => 'Permintaan tidak dapat diproses. Coba lagi atau kembali ke beranda.',
]) ?>
