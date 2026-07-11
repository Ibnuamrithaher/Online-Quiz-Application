<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Quiz: ') }} {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    
                    <h3 class="text-2xl font-bold mb-4">Nilai Pilihan Ganda Anda</h3>
                    <div class="text-6xl text-indigo-600 font-black mb-8">
                        {{ $attempt->score }} <span class="text-3xl text-gray-400">/ {{ $quiz->max_score }}</span>
                    </div>
                    
                    <p class="text-gray-600 mb-8">
                        * Catatan: Nilai di atas hanya untuk soal pilihan ganda. Soal essay akan dinilai secara manual oleh administrator.
                    </p>

                    <div class="mt-8 text-left border-t pt-8">
                        <h4 class="font-bold text-lg mb-4">Review Jawaban:</h4>
                        
                        @foreach($quiz->questions as $index => $question)
                            @php
                                $answer = $attempt->answers->where('question_id', $question->id)->first();
                                $isAnswered = !is_null($answer);
                            @endphp
                            <div class="mb-6 p-4 border rounded {{ $question->type == 'multiple_choice' ? ($isAnswered && $answer->score > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200') : 'bg-gray-50' }}">
                                <div class="flex items-center space-x-3 mb-2">
                                    <p class="font-semibold">{{ $index + 1 }}. {{ $question->content }}</p>
                                    @if($question->category)
                                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded">{{ $question->category }}</span>
                                    @endif
                                </div>
                                
                                @if($question->image_path)
                                    <div class="mb-3">
                                        <img src="{{ $question->image_url }}" alt="Question Image" class="max-h-48 rounded border border-gray-200">
                                    </div>
                                @endif
                                
                                @if($question->type == 'multiple_choice')
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500 mb-2">Pilihan yang tersedia:</p>
                                        <ul class="space-y-1 mb-4">
                                            @foreach($question->options as $opt)
                                                @php
                                                    $isChosen = $isAnswered && $answer->question_option_id === $opt->id;
                                                    $isBest = $opt->points === $question->options->max('points') && $opt->points > 0;
                                                @endphp
                                                <li class="p-2 border rounded-md text-sm flex justify-between items-center {{ $isChosen ? ($answer->score > 0 ? 'bg-green-100 border-green-300' : 'bg-red-100 border-red-300') : 'bg-white' }}">
                                                    <div>
                                                        <span class="mr-2">{{ $opt->content }}</span>
                                                        @if($isChosen)
                                                            <span class="text-xs font-bold px-2 py-1 rounded bg-indigo-100 text-indigo-800">Jawaban Anda</span>
                                                        @endif
                                                        @if($isBest && !$isChosen)
                                                            <span class="text-xs font-bold px-2 py-1 rounded bg-green-100 text-green-800">Pilihan Terbaik</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-500">{{ $opt->points }} Poin</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="flex justify-end border-t pt-2 mt-2">
                                        <div class="font-bold text-sm {{ $isAnswered && $answer->score > 0 ? 'text-green-700' : 'text-red-700' }}">
                                            @if($isAnswered)
                                                Skor Anda: {{ $answer->score > 0 ? '+'.$answer->score.' Poin' : '0 Poin' }}
                                            @else
                                                Skor Anda: 0 Poin (Tidak dijawab)
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <p class="mb-1 text-sm">Jawaban Anda:</p>
                                    @if($isAnswered && $answer->answer_text)
                                        <p class="bg-white p-2 border rounded text-gray-700 italic">
                                            {{ $answer->answer_text }}
                                        </p>
                                        <p class="text-xs font-bold mt-2 {{ $answer->score !== null ? 'text-green-600' : 'text-yellow-600' }}">
                                            @if($answer->score !== null)
                                                (Dinilai: +{{ $answer->score }} Poin)
                                            @else
                                                (Menunggu penilaian manual)
                                            @endif
                                        </p>
                                    @else
                                        <p class="bg-white p-2 border rounded text-gray-400 italic">
                                            Tidak dijawab
                                        </p>
                                        <p class="text-xs font-bold mt-2 text-red-600">
                                            (Skor: 0)
                                        </p>
                                    @endif
                                @endif
                                
                                @if($question->explanation)
                                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-900">
                                        <strong>Pembahasan:</strong><br>
                                        {{ $question->explanation }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('quizzes.index') }}" class="inline-block px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition font-bold mt-4">Kembali ke Daftar Quiz</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
