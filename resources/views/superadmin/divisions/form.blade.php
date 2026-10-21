<x-app-layout>
    <x-slot name="title">{{ isset($division) ? 'Edit Divisi' : 'Tambah Divisi' }}</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ isset($division) ? 'Edit Divisi' : 'Tambah Divisi Baru' }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ isset($division) ? 'Ubah nama divisi' : 'Tambahkan nama divisi ke dalam daftar' }}</p>
            </div>
            <a href="/superadmin/divisions" class="inline-flex items-center gap-2 text-gray-500 hover:text-inka-navy transition-colors font-medium text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ isset($division) ? url('superadmin/divisions/' . $division->id) : url('superadmin/divisions') }}" method="POST">
                @csrf
                @if(isset($division))
                    @method('PUT')
                @endif
                <div class="p-6 md:p-8 space-y-6">
                    
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl flex items-start gap-3 mb-6">
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

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Divisi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $division->name ?? '') }}" required 
                            class="block w-full border border-gray-200 rounded-xl focus:ring-inka-navy focus:border-inka-navy sm:text-sm px-4 py-3 bg-gray-50 focus:bg-white transition-colors" 
                            placeholder="Contoh: Divisi Teknik">
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="px-6 md:px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="/superadmin/divisions" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-inka-navy text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
