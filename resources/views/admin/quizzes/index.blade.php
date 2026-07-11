<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Quiz') }}
            </h2>
            <a href="{{ route('admin.quizzes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Tambah Quiz</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="mb-4 flex justify-between">
                        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="flex">
                            <input type="text" name="search" placeholder="Cari Quiz..." value="{{ request('search') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm sm:text-sm">
                            <button type="submit" class="ml-2 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">Cari</button>
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($quizzes as $quiz)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $quiz->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($quiz->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.quizzes.attempts.index', $quiz) }}" class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded text-xs font-semibold shadow-sm transition">Hasil Ujian</a>
                                            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold shadow-sm transition">Kelola Soal</a>
                                            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-semibold shadow-sm transition">Edit</a>
                                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus quiz ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold shadow-sm transition">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada quiz ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $quizzes->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
