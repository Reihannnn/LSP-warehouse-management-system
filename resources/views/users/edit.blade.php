@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">

    <div class="bg-white shadow-md rounded-lg overflow-hidden">

        <div class="px-6 py-4 border-b bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-800">
                Edit User
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui informasi pengguna dan reset password jika diperlukan.
            </p>
        </div>

        <div class="p-6">

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.update', $user->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
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
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role
                        </label>

                        <select
                            name="role"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                            <option value="pegawai"
                                {{ $user->role == 'pegawai' ? 'selected' : '' }}>
                                Pegawai
                            </option>

                            <option value="admin"
                                {{ $user->role == 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>

                        </select>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Kosongkan jika tidak diganti"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">

                        <p class="text-xs text-gray-500 mt-1">
                            Password lama akan tetap digunakan jika kolom ini dikosongkan.
                        </p>
                    </div>

                </div>

                <div class="flex justify-end gap-3 mt-8">

                    <a href="{{ route('users.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2 text-white bg-blue-500 rounded-lg shadow">
                        Update User
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection