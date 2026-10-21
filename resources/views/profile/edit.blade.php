<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Profil</h3>
                    <p class="mt-1 text-sm text-gray-600">Update nama dan email akun Anda.</p>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                            <input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                        </div>

                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">Simpan</button>
                            @if (session('status') === 'profile-updated')
                                <p class="text-sm text-gray-600">Tersimpan.</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
