<?php
// includes/footer.php
?>
    <!-- Footer Section -->
    <footer class="bg-brandBrown text-amber-100 mt-auto border-t-4 border-brandYellow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Col 1: Brand Info -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-9 h-9 bg-brandYellow rounded-full flex items-center justify-center text-brandBrown font-bold text-lg">
                            🍌
                        </div>
                        <span class="font-bold text-lg text-brandYellow">HMAN STORE</span>
                    </div>
                    <p class="text-xs leading-relaxed text-amber-200/80">
                        Penyedia cemilan renyah keripik pisang premium <strong>Banchip HMAN</strong>. Dibuat dengan bahan baku pisang berkualitas tinggi dan bumbu racikan melimpah.
                    </p>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="text-brandYellow font-semibold text-sm tracking-wider uppercase mb-4">Navigasi Cepat</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="index.php" class="hover:text-brandYellow transition-colors">Beranda</a></li>
                        <li><a href="#katalog" class="hover:text-brandYellow transition-colors">Katalog Varian Produk</a></li>
                        <li><a href="#keunggulan" class="hover:text-brandYellow transition-colors">Mengapa Banchip HMAN?</a></li>
                        <li><a href="#tentang" class="hover:text-brandYellow transition-colors">Tentang HMAN Store</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Social -->
                <div>
                    <h3 class="text-brandYellow font-semibold text-sm tracking-wider uppercase mb-4">Hubungi Kami</h3>
                    <p class="text-xs text-amber-200/80 mb-2">Himpunan Mahasiswa Administrasi Niaga (HMAN)</p>
                    <p class="text-xs text-amber-200/80 mb-2">📍 Kampus Politeknik Negeri Ujung Pandang, Makassar</p>
                    <p class="text-xs text-amber-200/80 mb-4">📱 WhatsApp Admin: 0823-5070-8383</p>
                </div>

            </div>

            <div class="border-t border-amber-900/60 mt-10 pt-6 text-center text-xs text-amber-300/70">
                <p>&copy; <?= date('Y') ?> <strong>HMAN Store – Banchip HMAN</strong>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Global Interactive Notice Modal (Untuk Penanda Fitur Belum Aktif) -->
    <div id="notice-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all text-center relative border-2 border-brandYellow">
            <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                ⚙️
            </div>
            <h3 id="modal-title" class="text-lg font-bold text-brandBrown mb-2">Fitur Dalam Pengembangan</h3>
            <p id="modal-message" class="text-sm text-slate-600 mb-6 leading-relaxed">
                Fitur ini belum diaktifkan pada Tahap 1.
            </p>
            <div class="flex justify-center">
                <button onclick="closeNoticeModal()" 
                        class="bg-brandBrown hover:bg-brandBrownLight text-brandYellow font-semibold px-6 py-2.5 rounded-lg text-xs shadow transition-all">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript Interaktivitas -->
    <script>
        // Mobile menu toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        if(btn && menu) {
            btn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }

        // Modal Handler
        function showNoticeModal(title, message) {
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-message').innerText = message;
            document.getElementById('notice-modal').classList.remove('hidden');
        }

        function closeNoticeModal() {
            document.getElementById('notice-modal').classList.add('hidden');
        }
    </script>
</body>
</html>