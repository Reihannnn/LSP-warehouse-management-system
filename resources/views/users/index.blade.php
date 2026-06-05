@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 mt-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 ">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                User Management
            </h1>
            <p class="text-gray-500">
                Kelola akun admin dan pegawai.
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="bg-blue-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg shadow transition">
            + Tambah User
        </a>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-300 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-300 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Nama
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Email
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                            Role
                        </th>

                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($users as $user)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">
                                    {{ $user->name }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4">

                                @if($user->role === 'admin')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        Pegawai
                                    </span>
                                @endif

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-center gap-2">

                                    <a href="{{ route('users.edit', $user->id) }}"
                                       class="px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm">
                                        Edit
                                    </a>

                                    @if($user->role === 'pegawai')

                                        <form action="{{ route('users.destroy', $user->id) }}"
                                              method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Yakin ingin menghapus user ini?')"
                                                class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm">
                                                Hapus
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-500">
                                Belum ada data user.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $users->links() }}
    </div>

</div>
@endsection