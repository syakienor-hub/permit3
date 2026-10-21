<x-app-layout>
    <x-slot name="title">Batalkan Izin Kerja</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="/admin/cancellations" class="text-sm text-gray-500 hover:text-inka-navy flex items-center gap-1 w-fit">
                ← Kembali ke Daftar
            </a>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mt-2">Batalkan Izin Kerja</h2>
            <p class="text-sm text-gray-500">No. Permit: <span class="font-semibold text-inka-navy">{{ $permit->no_permit }}</span></p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 px-4 md:px-6 py-4">
            <div class="grid sm:grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-400">Pekerjaan:</span> <span class="font-semibold text-gray-700">{{ $permit->nama_pekerjaan }}</span></div>
                <div><span class="text-gray-400">Site:</span> <span class="font-semibold text-gray-700">{{ $permit->site }}</span></div>
                <div><span class="text-gray-400">Kontraktor:</span> <span class="font-semibold text-gray-700">{{ $permit->kontraktor }}</span></div>
                <div><span class="text-gray-400">Pengaju:</span> <span class="font-semibold text-gray-700">{{ optional($permit->user)->name ?? $permit->divisi_pengaju ?? '-' }}</span></div>
                <div><span class="text-gray-400">Lokasi:</span> <span class="font-semibold text-gray-700">{{ $permit->lokasi ?? '-' }}</span></div>
                <div><span class="text-gray-400">Status:</span> <span class="font-semibold text-green-600">{{ $permit->status }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
            <div class="bg-red-50 px-4 md:px-6 py-4 border-b border-red-100">
                <h3 class="text-red-800 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Peringatan Pembatalan
                </h3>
                <p class="text-sm text-red-700 mt-1">Tindakan ini tidak dapat dibatalkan. Permit akan dicabut dan pekerjaan di lapangan harus segera dihentikan.</p>
            </div>

            <form action="/admin/cancellations/{{ $permit->id }}" method="POST" class="px-4 md:px-6 py-5">
                @csrf

                @if(session('error'))
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason" rows="3" required maxlength="500"
                        class="w-full border-gray-300 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Jelaskan alasan pencabutan izin, misal: ditemukan pelanggaran APD, cuaca ekstrem...">{{ old('cancellation_reason') }}</textarea>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Tanda Tangan HSE <span class="text-red-500">*</span></p>
                            <p class="text-xs text-gray-400">Tanda tangan Anda sebagai {{ auth()->user()->role === 'manager' ? 'Manager' : 'Staff' }} HSE yang membatalkan ({{ auth()->user()->name }})</p>
                        </div>
                        <button type="button" onclick="clearSignature()"
                            class="text-xs font-semibold text-gray-400 hover:text-red-500 border border-gray-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-colors">
                            ✕ Hapus
                        </button>
                    </div>

                    <div id="signature-container"
                        class="relative border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-white hover:border-red-400 transition-colors cursor-crosshair w-full max-w-[300px] aspect-square mx-auto">
                        <canvas id="signature-canvas" class="block w-full h-full" style="touch-action: none;"></canvas>
                        <span id="signature-placeholder" class="absolute inset-0 flex items-center justify-center text-xs text-gray-300 pointer-events-none select-none text-center px-4">
                            Tanda tangan di sini
                        </span>
                    </div>

                    <input type="hidden" name="tanda_tangan" id="tanda-tangan-input">
                    <p id="signature-error" class="text-xs text-red-500 mt-2 text-center hidden">Tanda tangan wajib diisi.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:justify-end pt-4 border-t border-gray-100 gap-2.5 sm:gap-3">
                    <a href="/admin/cancellations" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-center text-gray-600 hover:border-gray-300 transition-colors">Batal</a>
                    <button type="submit" id="btn-submit"
                        class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">
                        Cabut Izin Kerja
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const canvas = document.getElementById('signature-canvas');
            const placeholder = document.getElementById('signature-placeholder');
            const hiddenInput = document.getElementById('tanda-tangan-input');
            const container = document.getElementById('signature-container');
            const form = document.querySelector('form');
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                const rect = container.getBoundingClientRect();
                if (rect.width === 0) return;
                const rawDpr = window.devicePixelRatio || 1;
                const dpr = window.matchMedia('(max-width: 767px)').matches ? Math.min(rawDpr, 1.5) : rawDpr;
                canvas.width  = rect.width  * dpr;
                canvas.height = rect.height * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, rect.width, rect.height);
                ctx.strokeStyle = '#991b1b';
                ctx.lineWidth   = 2;
                ctx.lineCap     = 'round';
                ctx.lineJoin    = 'round';
            }
            window.addEventListener('resize', resizeCanvas);
            setTimeout(resizeCanvas, 100);

            let drawing = false;
            let hasDrawn = false;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                if (e.touches) {
                    return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
                }
                return { x: e.clientX - rect.left, y: e.clientY - rect.top };
            }

            function startDraw(e) {
                e.preventDefault();
                drawing = true;
                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            function draw(e) {
                if (!drawing) return;
                e.preventDefault();
                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();

                if (!hasDrawn) {
                    hasDrawn = true;
                    placeholder.classList.add('hidden');
                    container.classList.remove('border-dashed', 'border-gray-300');
                    container.classList.add('border-solid', 'border-red-500');
                    document.getElementById('signature-error').classList.add('hidden');
                }
            }

            function stopDraw() {
                if (!drawing) return;
                drawing = false;
                ctx.beginPath();
                hiddenInput.value = window.matchMedia('(max-width: 767px)').matches ? canvas.toDataURL('image/jpeg', 0.7) : canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown',  startDraw);
            canvas.addEventListener('mousemove',  draw);
            canvas.addEventListener('mouseup',    stopDraw);
            canvas.addEventListener('mouseleave', stopDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove',  draw,      { passive: false });
            canvas.addEventListener('touchend',   stopDraw);

            window.clearSignature = function () {
                const dpr = window.devicePixelRatio || 1;
                ctx.clearRect(0, 0, canvas.width / dpr, canvas.height / dpr);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width / dpr, canvas.height / dpr);
                hasDrawn = false;
                hiddenInput.value = '';
                placeholder.classList.remove('hidden');
                container.classList.add('border-dashed', 'border-gray-300');
                container.classList.remove('border-solid', 'border-red-500');
            };

            form.addEventListener('submit', function (e) {
                if (!hasDrawn) {
                    e.preventDefault();
                    document.getElementById('signature-error').classList.remove('hidden');
                }
            });
        })();
    </script>
</x-app-layout>
