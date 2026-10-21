<x-app-layout>
    <x-slot name="title">Dashboard Super Admin</x-slot>

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Super Admin</h1>
            <p class="text-base text-gray-500 mt-1">Ringkasan statistik akun Divisi pada sistem</p>
        </div>
        <div>
            <a href="{{ url('superadmin/users/create') }}" class="inline-flex items-center justify-center gap-2 bg-inka-navy text-white px-4 py-2.5 rounded-xl text-base font-semibold hover:bg-opacity-90 transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-inka-navy w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Akun Divisi
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-base font-medium">{{ session('success') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-base font-medium">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Widgets Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        {{-- Widget 1: Total Divisi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:shadow-md transition-shadow">
            <div>
                <p class="text-base font-medium text-gray-500 mb-1">Total Divisi</p>
                <h3 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $counts['total'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        {{-- Widget 2: Akun Aktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:shadow-md transition-shadow">
            <div>
                <p class="text-base font-medium text-gray-500 mb-1">Akun Aktif</p>
                <h3 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $counts['aktif'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- Widget 3: Akun Nonaktif --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between group hover:shadow-md transition-shadow">
            <div>
                <p class="text-base font-medium text-gray-500 mb-1">Akun Nonaktif</p>
                <h3 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $counts['nonaktif'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

    </div>

    {{-- Section: Akun Divisi Terbaru --}}
    <div class="table-card">
        
        <div class="px-4 md:px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-gray-50/50">
            <h2 class="text-xl font-bold text-gray-900">Akun Divisi Terbaru</h2>
            <a href="{{ url('superadmin/users') }}" class="text-base font-semibold text-inka-navy hover:underline">Lihat Semua</a>
        </div>

        @if($users->isEmpty())
            <div class="p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum ada akun Divisi</h3>
                <p class="text-base text-gray-500 mb-5">Tambahkan akun Divisi agar dapat mengajukan permit.</p>
                <a href="{{ url('superadmin/users/create') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-xl text-base font-semibold hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Akun Divisi
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[720px]">
                    <thead>
                        <tr>
                            <th>Nama Divisi</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="transition-colors">
                            <td class="font-medium text-gray-900">{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email ?? '—' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-green-50 text-green-700 border-green-200/70">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-red-50 text-red-700 border-red-200/70">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
</x-app-layout>
