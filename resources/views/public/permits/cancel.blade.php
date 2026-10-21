<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batalkan Permit — INKA Madiun</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logok3.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center px-4 py-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-2xl w-full p-6 sm:p-8">
        <div class="mb-5">
            <a href="{{ $successUrl }}" class="text-sm text-gray-500 hover:text-inka-navy">← Kembali ke Status Permit</a>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 mt-2">Batalkan Izin Kerja</h1>
            <p class="text-sm text-gray-500 mt-1">No. Permit: <span class="font-semibold text-inka-navy">{{ $permit->no_permit }}</span></p>
        </div>

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

        <div class="rounded-xl border border-gray-100 bg-gray-50/60 px-4 py-3 mb-5 text-sm">
            <div class="grid sm:grid-cols-2 gap-2">
                <div><span class="text-gray-400">Pekerjaan:</span> <span class="font-semibold text-gray-700">{{ $permit->nama_pekerjaan }}</span></div>
                <div><span class="text-gray-400">Site:</span> <span class="font-semibold text-gray-700">{{ $permit->site }}</span></div>
                <div><span class="text-gray-400">Kontraktor:</span> <span class="font-semibold text-gray-700">{{ $permit->kontraktor }}</span></div>
                <div><span class="text-gray-400">Divisi:</span> <span class="font-semibold text-gray-700">{{ $permit->divisi_pengaju ?? '-' }}</span></div>
            </div>
        </div>

        @if(in_array($permit->status, ['Closed', 'Cancelled']))
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-5">
                <h2 class="font-bold text-red-800">Permit tidak dapat dibatalkan</h2>
                <p class="text-sm text-red-700 mt-1">
                    Permit ini sudah berstatus <strong>{{ $permit->status }}</strong>.
                    @if($permit->cancelled_at)
                        Dibatalkan pada {{ $permit->cancelled_at->format('d/m/Y H:i') }}.
                    @endif
                </p>
                @if($permit->cancellation_reason)
                    <p class="text-sm text-red-700 mt-2"><strong>Alasan:</strong> {{ $permit->cancellation_reason }}</p>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-red-100 bg-red-50 px-4 py-3 mb-5">
                <p class="text-sm text-red-700"><strong>Perhatian:</strong> pembatalan bersifat permanen. Jika pekerjaan sedang berjalan, hentikan pekerjaan dan hubungi HSE.</p>
            </div>

            <form action="{{ $cancelUrl }}" method="POST" id="cancel-form" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pemohon <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pemohon" required maxlength="255"
                        value="{{ old('nama_pemohon', $permit->penanggung_jawab ?? $permit->divisi_pengaju) }}"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="telepon" maxlength="50"
                        value="{{ old('telepon', $permit->telepon) }}"
                        class="w-full border-gray-300 rounded-xl focus:border-inka-navy focus:ring-inka-navy text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea name="cancellation_reason" rows="3" required maxlength="500"
                        class="w-full border-gray-300 rounded-xl focus:border-red-500 focus:ring-red-500 text-sm"
                        placeholder="Jelaskan alasan pembatalan, misal: pekerjaan selesai lebih awal, cuaca ekstrem, perubahan rencana...">{{ old('cancellation_reason') }}</textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Tanda Tangan Pemohon <span class="text-red-500">*</span></p>
                            <p class="text-xs text-gray-400">Tanda tangan di sini sebagai bukti pembatalan.</p>
                        </div>
                        <button type="button" onclick="clearSignature()"
                            class="text-xs font-semibold text-gray-400 hover:text-red-500 border border-gray-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-colors">
                            Hapus
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

                <label class="flex items-start gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="konfirmasi" value="1" required class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span>Saya yakin membatalkan permit ini dan memahami bahwa tindakan ini tidak dapat dibatalkan kembali.</span>
                </label>

                <div class="flex flex-col sm:flex-row sm:justify-end gap-2.5 sm:gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ $successUrl }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-center text-gray-600 hover:border-gray-300 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">
                        Cabut Izin Kerja
                    </button>
                </div>
            </form>
        @endif
    </div>

    <script>
        (function () {
            const canvas = document.getElementById('signature-canvas');
            if (!canvas) return;

            const placeholder = document.getElementById('signature-placeholder');
            const hiddenInput = document.getElementById('tanda-tangan-input');
            const container = document.getElementById('signature-container');
            const form = document.getElementById('cancel-form');
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                const rect = container.getBoundingClientRect();
                if (rect.width === 0) return;
                const rawDpr = window.devicePixelRatio || 1;
                const dpr = window.matchMedia('(max-width: 767px)').matches ? Math.min(rawDpr, 1.5) : rawDpr;
                canvas.width = rect.width * dpr;
                canvas.height = rect.height * dpr;
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, rect.width, rect.height);
                ctx.strokeStyle = '#991b1b';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
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

            canvas.addEventListener('mousedown', startDraw);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDraw);
            canvas.addEventListener('mouseleave', stopDraw);
            canvas.addEventListener('touchstart', startDraw, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDraw);

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
</body>
</html>
