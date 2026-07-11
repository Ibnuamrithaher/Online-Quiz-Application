<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Soal: ') }} {{ $quiz->title }}
            </h2>
            <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Tambah Soal</a>
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

                    <div class="mb-6 pb-4 border-b flex justify-between items-end">
                        <div>
                            <h3 class="text-lg font-medium">Daftar Soal</h3>
                            <p class="text-sm text-gray-500">Total soal: {{ $quiz->questions->count() }}</p>
                            <p class="text-sm text-gray-500 mt-1">Batas Waktu: <strong>{{ $quiz->time_limit ? $quiz->time_limit . ' Menit' : 'Tidak Terbatas' }}</strong></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Skor Maksimal Kuis:</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ $quiz->max_score }}</p>
                        </div>
                    </div>

                    @forelse($quiz->questions as $index => $question)
                        <div class="mb-6 p-4 border rounded-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-bold">Soal {{ $index + 1 }}:</span>
                                    @if($question->image_path)
                                        <div class="mt-2 mb-2">
                                            <img src="{{ $question->image_url }}" alt="Question Image" class="max-h-48 rounded border border-gray-200">
                                        </div>
                                    @endif
                                    <p class="mt-2">{{ $question->content }}</p>
                                    <div class="mt-2 space-x-2">
                                        <span class="text-sm px-2 py-1 bg-gray-100 rounded text-gray-600">Tipe: {{ $question->type == 'multiple_choice' ? 'Pilihan Ganda' : 'Essay' }}</span>
                                        <span class="text-sm px-2 py-1 bg-indigo-100 rounded text-indigo-700 font-semibold">{{ $question->category }}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" class="px-3 py-1 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">Edit</a>
                                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            
                            @if($question->type == 'multiple_choice')
                                <div class="mt-4 pl-4 border-l-2 border-indigo-200">
                                    <h4 class="font-semibold text-sm mb-2">Pilihan:</h4>
                                    <ul class="list-disc pl-5">
                                        @foreach($question->options as $option)
                                            <li class="{{ $option->points > 0 ? 'text-green-600 font-bold' : '' }}">
                                                {{ $option->content }} <span class="text-xs text-gray-500 font-normal">({{ $option->points }} Poin)</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500">Belum ada soal untuk quiz ini.</p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
