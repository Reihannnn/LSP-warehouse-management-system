@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Profile Saya
        </h1>

        <p class="text-gray-500">
            Kelola informasi akun dan keamanan akun Anda.
        </p>
    </div>

    {{-- Informasi Profil --}}
    <div class="bg-white shadow rounded-xl mb-6">

        <div class="border-b px-6 py-4">
            <h2 class="text-xl font-semibold">
                Informasi Profil
            </h2>
        </div>

        <div class="p-6">

            @if (session('status') === 'profile-updated')
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
                    Profil berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role
                        </label>

                        <input
                            type="text"
                            value="{{ ucfirst($user->role) }}"
                            readonly
                            class="w-full rounded-lg border-gray-300 bg-gray-100">
                    </div>

                    {{-- Dibuat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bergabung Sejak
                        </label>

                        <input
                            type="text"
                            value="{{ $user->created_at->format('d M Y') }}"
                            readonly
                            class="w-full rounded-lg border-gray-300 bg-gray-100">
                    </div>

                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </div>

    {{-- Ganti Password --}}
    <div class="bg-white shadow rounded-xl mb-6">

        <div class="border-b px-6 py-4">
            <h2 class="text-xl font-semibold">
                Ganti Password
            </h2>
        </div>

        <div class="p-6">

            @if (session('status') === 'password-updated')
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
                    Password berhasil diperbarui.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block mb-2">
                        Password Saat Ini
                    </label>

                    <input
                        type="password"
                        name="current_password"
                        class="w-full rounded-lg border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block mb-2">
                        Password Baru
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full rounded-lg border-gray-300">
                </div>

                <div class="mb-4">
                    <label class="block mb-2">
                        Konfirmasi Password Baru
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full rounded-lg border-gray-300">
                </div>

                <button
                    type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-lg">
                    Update Password
                </button>

            </form>

        </div>

    </div>

</div>
@endsection