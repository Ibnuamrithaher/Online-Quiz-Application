<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.quizzes.attempts.index', $quiz) }}" class="text-gray-500 hover:text-gray-700">
                &larr; Kembali
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Penilaian Ujian: ') }} {{ $attempt->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 pb-6 border-b flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">{{ $quiz->title }}</h3>
                            <p class="text-sm text-gray-600">Peserta: {{ $attempt->user->name }} ({{ $attempt->user->email }})</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total Skor:</p>
                            <p class="text-3xl font-black text-indigo-600">{{ $attempt->score }} <span class="text-lg text-gray-400 font-normal">/ {{ $quiz->max_score }}</span></p>
                        </div>
                    </div>

                    <form action="{{ route('admin.quizzes.attempts.grade', [$quiz, $attempt]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @foreach($attempt->answers as $index => $answer)
                            <div class="mb-6 p-4 border rounded-lg {{ $answer->question->type == 'multiple_choice' ? 'bg-gray-50' : 'bg-white shadow-sm' }}">
                                <p class="font-semibold mb-2">{{ $index + 1 }}. {{ $answer->question->content }}</p>
                                
                                @if($answer->question->image_path)
                                    <div class="mb-3">
                                        <img src="{{ $answer->question->image_url }}" alt="Question Image" class="max-h-48 rounded border border-gray-200">
                                    </div>
                                @endif
                                
                                @if($answer->question->type == 'multiple_choice')
                                    <div class="flex justify-between items-center mt-2 p-3 rounded {{ $answer->score > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <div>
                                            <p class="text-sm">Jawaban: <strong>{{ $answer->option ? $answer->option->content : '-' }}</strong></p>
                                        </div>
                                        <div class="font-bold text-sm">
                                            {{ $answer->score > 0 ? '+' . $answer->score . ' Poin' : '0 Poin' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 mb-1">Jawaban Peserta:</p>
                                        <div class="p-3 border rounded bg-gray-50 mb-4 whitespace-pre-wrap">{{ $answer->answer_text }}</div>
                                        
                                        <div class="p-3 border border-indigo-100 bg-indigo-50 rounded-lg">
                                            <p class="text-sm font-semibold mb-2">Berikan Nilai (0 - {{ $answer->question->points ?? 100 }}):</p>
                                            <div class="flex gap-4">
                                                <input type="number" name="grades[{{ $answer->id }}]" value="{{ $answer->score ?? 0 }}" min="0" max="{{ $answer->question->points ?? 100 }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-32" required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="mt-8 flex justify-end">
                            <x-primary-button>
                                {{ __('Simpan Penilaian') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
