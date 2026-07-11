<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ type: '{{ $question->type }}' }">
                    <form method="POST" action="{{ route('admin.quizzes.questions.update', [$quiz, $question]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Tipe Soal')" />
                            <select id="type" name="type" x-model="type" class="border-gray-300 focus:border-indigo-500 bg-gray-100 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" disabled>
                                <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
                            </select>
                            <input type="hidden" name="type" value="{{ $question->type }}">
                            <p class="text-xs text-gray-500 mt-1">Tipe soal tidak dapat diubah setelah dibuat.</p>
                        </div>


                        <div class="mb-4">
                            <x-input-label for="category" :value="__('Kategori Soal')" />
                            <x-text-input id="category" class="block mt-1 w-full md:w-1/3" type="text" name="category" :value="old('category', $question->category)" required placeholder="Contoh: TIU, TWK, TKP, dsb." />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Kategori membantu peserta mengelompokkan soal saat mengerjakan ujian.</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Pertanyaan')" />
                            <textarea id="content" name="content" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" required>{{ old('content', $question->content) }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Gambar Soal (Opsional)')" />
                            
                            @if($question->image_path)
                                <div class="mt-2 mb-3">
                                    <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                                    <img src="{{ $question->image_url }}" alt="Question Image" class="max-h-48 rounded border border-gray-200">
                                    
                                    <div class="mt-2 flex items-center">
                                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                        <label for="remove_image" class="ml-2 text-sm text-red-600 font-medium">Hapus gambar ini</label>
                                    </div>
                                </div>
                            @endif

                            <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mt-1" onchange="previewImage(event)">
                            <p class="text-xs text-gray-500 mt-1">Pilih gambar baru untuk mengganti gambar lama (jika ada).</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            
                            <div id="imagePreviewContainer" class="mt-3 hidden">
                                <p class="text-xs text-gray-500 mb-1">Preview Gambar Baru:</p>
                                <img id="imagePreview" src="#" alt="Preview" class="max-h-48 rounded border border-gray-200">
                            </div>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="explanation" :value="__('Pembahasan Jawaban (Opsional)')" />
                            <textarea id="explanation" name="explanation" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-gray-700 bg-blue-50" rows="3" placeholder="Jelaskan alasan dari jawaban benar atau logika penyelesaian di sini...">{{ old('explanation', $question->explanation) }}</textarea>
                            <x-input-error :messages="$errors->get('explanation')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Penjelasan ini akan muncul kepada peserta di halaman Hasil Ujian.</p>
                        </div>

                        <div x-show="type === 'essay'" class="mb-4 border p-4 rounded-md bg-blue-50">
                            <h4 class="font-semibold mb-2">Soal Essay</h4>
                            <p class="text-sm text-gray-500 mb-4">Tentukan bobot poin maksimal yang bisa didapatkan peserta untuk soal essay ini (contoh: 100).</p>
                            
                            <div>
                                <x-input-label for="points" :value="__('Bobot Nilai Maksimal')" />
                                <x-text-input id="points" name="points" type="number" min="0" class="mt-1 block w-full md:w-1/3" :value="old('points', $question->points ?? 100)" />
                                <x-input-error :messages="$errors->get('points')" class="mt-2" />
                            </div>
                        </div>

                        <div x-show="type === 'multiple_choice'" class="mb-4 border p-4 rounded-md bg-gray-50">
                            <h4 class="font-semibold mb-2">Pilihan Ganda</h4>
                            <p class="text-sm text-gray-500 mb-4">Masukkan bobot poin untuk masing-masing opsi (misal: 10 untuk jawaban benar, 0 untuk salah, atau poin parsial lainnya).</p>
                            
                            @php $options = old('options', $question->options->toArray()); @endphp
                            
                            @for ($i = 0; $i < 4; $i++)
                                @php $opt = $options[$i] ?? null; @endphp
                                <div class="flex items-center gap-2 mb-2">
                                    @if($opt && isset($opt['id']))
                                        <input type="hidden" name="options[{{$i}}][id]" value="{{ $opt['id'] }}">
                                    @endif
                                    <div class="flex-grow">
                                        <input type="text" name="options[{{$i}}][content]" placeholder="Pilihan {{ $i+1 }}" value="{{ $opt['content'] ?? '' }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full">
                                    </div>
                                    <div class="w-24">
                                        <input type="number" name="options[{{$i}}][points]" placeholder="Poin" value="{{ $opt['points'] ?? ($i == 0 ? 10 : 0) }}" min="0" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full">
                                    </div>
                                </div>
                            @endfor
                            <x-input-error :messages="$errors->get('options')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Update Soal') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
