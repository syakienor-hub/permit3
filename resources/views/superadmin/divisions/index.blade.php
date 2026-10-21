<x-app-layout>
    <x-slot name="title">Kelola Divisi</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Divisi</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar nama divisi yang dapat didaftarkan</p>
            </div>
            <div>
                <a href="/superadmin/divisions/create" class="inline-flex items-center gap-2 bg-inka-navy text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-opacity-90 transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-inka-navy">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Divisi Baru
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Main Content --}}
        <div class="table-card">
            <div class="overflow-x-auto">
                <table class="data-table min-w-[560px]">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Nama Divisi</th>
                            <th>Tgl Dibuat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($divisions as $index => $division)
                        <tr class="transition-colors group">
                            <td>{{ $index + 1 }}</td>
                            <td class="font-medium text-gray-900">{{ $division->name }}</td>
                            <td class="text-gray-500">{{ $division->created_at->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/superadmin/divisions/{{ $division->id }}/edit" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Divisi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="/superadmin/divisions/{{ $division->id }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Divisi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-1">Belum ada Divisi</h3>
                                <p class="text-sm">Silakan tambah divisi baru.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
