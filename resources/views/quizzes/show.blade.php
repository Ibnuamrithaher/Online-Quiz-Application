<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-end">
                <div>
                    <p class="text-gray-600 text-sm mt-1">{{ $quiz->description }}</p>
                </div>
            </div>

            <!-- Alpine Component Root -->
            <div class="flex flex-col-reverse md:flex-row gap-6" x-data="quizApp()">
                
                <!-- Left Column: Active Question Form -->
                <div class="md:w-3/4">
                    <form method="POST" action="{{ route('quizzes.attempt', $quiz) }}" id="quizForm">
                        @csrf
                        
                        @forelse($quiz->questions as $index => $question)
                            <div x-show="currentQuestion === {{ $index }}" x-cloak class="bg-white rounded-lg shadow-sm border p-6 md:p-8 mb-4 transition-all duration-300">
                                
                                <!-- Question Header -->
                                <div class="flex justify-between items-center mb-6 border-b pb-3">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-xl font-bold text-gray-800">Soal No. {{ $index + 1 }}</h3>
                                        <span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded">{{ $question->category }}</span>
                                    </div>
                                    
                                    <label class="inline-flex items-center cursor-pointer bg-yellow-50 px-4 py-2 rounded-full border border-yellow-200 hover:bg-yellow-100 transition">
                                        <input type="checkbox" x-model="doubtful[{{ $index }}]" class="rounded border-gray-300 text-yellow-500 shadow-sm focus:ring-yellow-500 w-5 h-5">
                                        <span class="ml-2 font-semibold text-yellow-700 select-none">Ragu-ragu</span>
                                    </label>
                                </div>

                                <!-- Question Content -->
                                <div class="text-gray-800 text-lg mb-6 leading-relaxed">
                                    {{ $question->content }}
                                </div>
                                
                                @if($question->image_path)
                                    <div class="mb-6 flex justify-center">
                                        <img src="{{ $question->image_url }}" alt="Question Image" class="max-h-80 rounded-lg shadow-sm border border-gray-200">
                                    </div>
                                @endif
                                
                                <!-- Options / Inputs -->
                                <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                                @if($question->type == 'multiple_choice')
                                    <div class="space-y-3">
                                        @foreach($question->options as $option)
                                            <label class="flex items-center space-x-4 p-3 rounded-md hover:bg-white border border-transparent hover:border-blue-200 transition cursor-pointer">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" x-model="answers[{{ $index }}]" class="form-radio h-5 w-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                <span class="text-gray-800 font-medium text-lg">{{ $option->content }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea name="answers[{{ $question->id }}]" x-model="answers[{{ $index }}]" class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-gray-800" rows="6" placeholder="Ketikkan jawaban Anda di sini..."></textarea>
                                @endif
                                </div>

                                <!-- Navigation Buttons (Inside Question Card) -->
                                <div class="mt-8 flex justify-between items-center border-t pt-5">
                                    <button type="button" @click="prevQuestion" x-show="currentQuestion > 0" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md font-bold transition flex items-center">
                                        &larr; Sebelumnya
                                    </button>
                                    <div x-show="currentQuestion === 0"></div> <!-- Placeholder so Next stays on right -->
                                    
                                    <button type="button" @click="nextQuestion" x-show="currentQuestion < totalQuestions - 1" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-bold transition flex items-center shadow-sm">
                                        Selanjutnya &rarr;
                                    </button>

                                    <button type="button" onclick="confirmSubmit()" x-show="currentQuestion === totalQuestions - 1" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-md font-bold transition flex items-center shadow-sm">
                                        Selesai & Kumpulkan
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 p-6 bg-white rounded-lg shadow">Quiz ini belum memiliki soal.</p>
                        @endforelse
                    </form>
                </div>

                <!-- Right Column: Navigation Grid -->
                <div class="md:w-1/4">
                    <div class="bg-white p-5 rounded-lg shadow-sm border sticky top-6">
                        @if($quiz->time_limit)
                            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-center">
                                <p class="text-sm text-red-600 font-bold mb-1 uppercase tracking-wider">Sisa Waktu</p>
                                <p class="text-3xl font-black text-red-700 font-mono" x-text="formattedTime"></p>
                            </div>
                        @endif

                        <h4 class="font-bold text-gray-800 mb-4 text-center border-b pb-3">Navigasi Soal</h4>
                        
                        <!-- Category Tabs -->
                        <div class="flex flex-wrap gap-1 mb-4">
                            <button 
                                type="button" 
                                @click="activeCategory = 'Semua'"
                                :class="activeCategory === 'Semua' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-2 py-1 text-xs font-semibold rounded transition"
                            >Semua</button>
                            <template x-for="cat in Object.keys(categories)" :key="cat">
                                <button 
                                    type="button" 
                                    @click="activeCategory = cat"
                                    :class="activeCategory === cat ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="px-2 py-1 text-xs font-semibold rounded transition"
                                    x-text="cat"
                                ></button>
                            </template>
                        </div>

                        <!-- Grid -->
                        <div class="grid grid-cols-5 gap-2 mb-6">
                            @foreach($quiz->questions as $index => $q)
                                <button 
                                    type="button" 
                                    x-show="activeCategory === 'Semua' || (categories[activeCategory] && categories[activeCategory].includes({{ $index }}))"
                                    @click="jumpTo({{ $index }})" 
                                    :class="getGridColor({{ $index }})"
                                    class="w-10 h-10 flex items-center justify-center rounded border font-semibold transition-colors duration-150"
                                >
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>

                        <div class="space-y-3 text-sm text-gray-700 border-t pt-4">
                            <div class="flex items-center"><span class="w-5 h-5 rounded bg-green-500 mr-3 shadow-inner"></span> Sudah Dijawab</div>
                            <div class="flex items-center"><span class="w-5 h-5 rounded bg-yellow-400 mr-3 shadow-inner"></span> Ragu-ragu</div>
                            <div class="flex items-center"><span class="w-5 h-5 rounded bg-white border border-gray-300 mr-3 shadow-inner"></span> Belum Dijawab</div>
                        </div>
                        
                        @if($quiz->questions->count() > 0)
                        <div class="mt-8 border-t pt-4">
                            <button type="button" onclick="confirmSubmit()" class="w-full py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-bold transition shadow-md">
                                Kumpulkan Ujian
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function confirmSubmit() {
            if(confirm('Apakah Anda yakin ingin menyelesaikan ujian dan mengumpulkannya? Pastikan semua soal telah terjawab dengan baik.')) {
                document.getElementById('quizForm').submit();
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('quizApp', () => ({
                currentQuestion: 0,
                totalQuestions: {{ $quiz->questions->count() }},
                answers: {},
                doubtful: {},
                categories: {},
                activeCategory: '',
                timeLimit: {{ $quiz->time_limit ?? 'null' }},
                remainingTime: {{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }},
                formattedTime: '00:00:00',
                timerInterval: null,

                init() {
                    let cat;
                    // Initialize tracking states based on the number of questions
                    @foreach($quiz->questions as $index => $q)
                        this.answers[{{ $index }}] = '';
                        this.doubtful[{{ $index }}] = false;
                        
                        // Group by category
                        cat = '{{ addslashes($q->category ?? 'Umum') }}';
                        if (!this.categories[cat]) {
                            this.categories[cat] = [];
                        }
                        this.categories[cat].push({{ $index }});
                    @endforeach

                    // Set first active category
                    let cats = Object.keys(this.categories);
                    if (cats.length > 0) {
                        this.activeCategory = 'Semua';
                    }

                    if (this.timeLimit) {
                        this.updateFormattedTime();
                        this.startTimer();
                    }
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.remainingTime > 0) {
                            this.remainingTime--;
                            this.updateFormattedTime();
                        } else {
                            clearInterval(this.timerInterval);
                            this.autoSubmit();
                        }
                    }, 1000);
                },

                updateFormattedTime() {
                    let hours = Math.floor(this.remainingTime / 3600);
                    let minutes = Math.floor((this.remainingTime % 3600) / 60);
                    let seconds = this.remainingTime % 60;

                    this.formattedTime = 
                        String(hours).padStart(2, '0') + ':' + 
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');
                },

                autoSubmit() {
                    alert('Waktu pengerjaan telah habis! Jawaban Anda akan otomatis dikumpulkan.');
                    document.getElementById('quizForm').submit();
                },

                isAnswered(index) {
                    const ans = this.answers[index];
                    return ans !== '' && ans !== null && ans !== undefined;
                },

                getGridColor(index) {
                    // Active question outline
                    if (this.currentQuestion === index) {
                        return 'bg-blue-100 border-blue-500 text-blue-700 ring-2 ring-blue-300 shadow-sm';
                    }
                    // Doubtful has precedence over answered in terms of showing the yellow mark
                    if (this.doubtful[index]) {
                        return 'bg-yellow-400 border-yellow-500 text-yellow-900 shadow-inner';
                    }
                    // Answered
                    if (this.isAnswered(index)) {
                        return 'bg-green-500 border-green-600 text-white shadow-inner';
                    }
                    // Unanswered
                    return 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100 shadow-sm';
                },

                nextQuestion() {
                        if (this.currentQuestion < this.totalQuestions - 1) {
                            this.currentQuestion++;
                            this.syncCategory(this.currentQuestion);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },

                    prevQuestion() {
                        if (this.currentQuestion > 0) {
                            this.currentQuestion--;
                            this.syncCategory(this.currentQuestion);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },

                    jumpTo(index) {
                        this.currentQuestion = index;
                        this.syncCategory(index);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },

                    syncCategory(index) {
                        if (this.activeCategory === 'Semua') return;
                        
                        for (const cat in this.categories) {
                            if (this.categories[cat].includes(index)) {
                                this.activeCategory = cat;
                                break;
                            }
                        }
                    }
            }))
        })
    </script>
</x-app-layout>
