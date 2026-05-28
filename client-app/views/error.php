<?php $this->layout('layout', ['title' => 'OAuth Error Callback']) ?>

<div class="bg-surface border border-rose-500 rounded-2xl p-6 md:p-8 shadow-sm max-w-md mx-auto text-center transition-colors duration-300">
    <div class="w-12 h-12 rounded-full bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto mb-4 border border-rose-500/20">
        <i class="bx bx-error-alt text-2xl"></i>
    </div>
    <h2 class="text-xl font-extrabold text-rose-500 mb-2">OAuth Provider Error!</h2>
    <p class="text-muted text-sm leading-relaxed mb-6">
        <?= htmlspecialchars($error_message ?? 'Terjadi kesalahan saat otentikasi.') ?>
    </p>
    <a href="/" class="w-full inline-flex items-center justify-center gap-2 py-2.5 bg-rose-600 text-white rounded-xl font-bold hover:bg-rose-700 transition shadow-lg shadow-rose-600/20 text-sm">
        Kembali ke Beranda
    </a>
</div>
