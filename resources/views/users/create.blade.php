@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">

    <div class="bg-white rounded-xl shadow">

        <div class="border-b px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800">
                Tambah User
            </h1>

            <p class="text-gray-500 text-sm mt-1">
                Tambahkan akun pegawai baru.
            </p>
        </div>

        <div class="p-6">

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300">
                    <ul class="list-disc list-inside text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}"
                  method="POST">

                @csrf

                {{-- Nama --}}
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        Nama Pegawai
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>

                    <small class="text-gray-500">
                        Minimal 6 karakter.
                    </small>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-5">
                    <label class="block mb-2 font-medium text-gray-700">
                        Konfirmasi Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div class="mb-5">
    <label class="block mb-2 font-medium text-gray-700">
        Role
    </label>

    <select
        name="role"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

        <option value="">-- Pilih Role --</option>

        <option value="pegawai"
            {{ old('role') == 'pegawai' ? 'selected' : '' }}>
            Pegawai
        </option>



    </select>

    @error('role')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror
</div>

                <div class="flex justify-end gap-3">

                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow">
                        Simpan User
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection