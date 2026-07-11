<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Quiz') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($quizzes as $quiz)
                        <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition flex flex-col h-full">
                            <h3 class="text-xl font-bold mb-2">{{ $quiz->title }}</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3 flex-grow">{{ $quiz->description }}</p>
                            <div class="mb-4 flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $quiz->time_limit ? $quiz->time_limit . ' Menit' : 'Waktu Bebas' }}
                            </div>
                            <a href="{{ route('quizzes.show', $quiz) }}" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 w-full text-center mt-auto">Kerjakan Quiz</a>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-8">
                            Belum ada quiz yang tersedia saat ini.
                        </div>
                    @endforelse
                </div>
                <div class="px-6 pb-6">
                    {{ $quizzes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
