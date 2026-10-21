<x-app-layout>
    <x-slot name="title">Manajemen Akun Divisi</x-slot>

<div class="max-w-7xl mx-auto space-y-6" x-data="userManagement()">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Akun Divisi</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pendaftaran dan status akun Divisi</p>
        </div>
        <div>
            <a href="/superadmin/users/create" class="inline-flex items-center gap-2 bg-inka-navy text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-opacity-90 transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-inka-navy">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Akun Divisi
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
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-sm font-medium">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Filters & Search --}}
        <div class="p-5 border-b border-gray-100 bg-gray-50/50">
            <form action="{{ url('superadmin/users') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                
                {{-- Search --}}
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..." 
                        class="block w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:border-inka-navy focus:ring-inka-navy/20">
                </div>

                {{-- Status Filter --}}
                <div class="sm:w-48">
                    <select name="status" class="block w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm focus:border-inka-navy focus:ring-inka-navy/20" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                
                {{-- Submit (Hidden, triggered by JS or Enter) --}}
                <button type="submit" class="hidden">Filter</button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 min-w-[760px]">
                <thead class="bg-gray-50/50 text-xs uppercase text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Divisi</th>
                        <th class="px-6 py-4 font-semibold">Username</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Tanggal Dibuat</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->username }}</td>
                        <td class="px-6 py-4">{{ $user->email ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                
                                {{-- Edit Btn --}}
                                <a href="/superadmin/users/{{ $user->id }}/edit" title="Edit Akun"
                                    class="p-2 text-gray-400 hover:text-inka-navy hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                
                                {{-- Delete Btn --}}
                                <form action="/superadmin/users/{{ $user->id }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Akun">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                
                                {{-- Reset Password Btn --}}
                                <button type="button" @click="openResetModal({{ $user->id }}, '{{ $user->name }}')" title="Reset Password"
                                    class="p-2 text-gray-400 hover:text-inka-navy hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                                
                                {{-- Toggle Active/Inactive --}}
                                <form action="{{ url('superadmin/users/' . $user->id . '/status') }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" title="Nonaktifkan" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" onclick="return confirm('Nonaktifkan akun ini? Akun tidak akan bisa login.')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        </button>
                                    @else
                                        <button type="submit" title="Aktifkan" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" onclick="return confirm('Aktifkan akun ini?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    @endif
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 mb-1">Belum ada akun Divisi</h3>
                            <p class="text-sm">Silakan tambah akun baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif

    </div>



    {{-- MODAL: Reset Password --}}
    <div x-show="showResetModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="showResetModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showResetModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showResetModal" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                <form :action="resetFormUrl" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Reset Password</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">Reset password untuk akun <strong x-text="resetUserName"></strong>.</p>
                                    
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                    <input type="password" name="password" required minlength="6" class="block w-full border-gray-300 rounded-xl focus:ring-inka-navy focus:border-inka-navy sm:text-sm px-3 py-2 border" placeholder="Minimal 6 karakter">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-semibold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Reset Password
                        </button>
                        <button type="button" @click="showResetModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userManagement', () => ({
            showResetModal: false,
            resetFormUrl: '',
            resetUserName: '',

            openResetModal(userId, userName) {
                this.resetFormUrl = `{{ url('superadmin/users') }}/${userId}/reset-password`;
                this.resetUserName = userName;
                this.showResetModal = true;
            }
        }))
    })
</script>
</x-app-layout>
