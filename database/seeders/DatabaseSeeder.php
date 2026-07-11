<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Peserta Ujian',
            'email' => 'peserta@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // Create a realistic quiz
        $quiz1 = Quiz::create([
            'title' => 'Pemrograman Dasar PHP',
            'description' => 'Ujian ini menguji pengetahuan dasar tentang bahasa pemrograman PHP, termasuk sintaks, variabel, dan fungsi bawaan.',
            'is_active' => true,
        ]);

        // Add questions to quiz1
        $q1 = $quiz1->questions()->create([
            'type' => 'multiple_choice',
            'content' => 'Simbol apa yang digunakan untuk mendeklarasikan variabel dalam PHP?',
            'explanation' => 'Dalam PHP, semua variabel harus diawali dengan simbol dolar ($). Contoh: $nama_variabel = "Hello";',
        ]);
        $q1->options()->createMany([
            ['content' => '#', 'points' => 0],
            ['content' => '$', 'points' => 10],
            ['content' => '@', 'points' => 0],
            ['content' => '%', 'points' => 0],
        ]);

        $q2 = $quiz1->questions()->create([
            'type' => 'multiple_choice',
            'content' => 'Fungsi PHP mana yang digunakan untuk menampilkan teks ke layar?',
        ]);
        $q2->options()->createMany([
            ['content' => 'echo', 'points' => 10],
            ['content' => 'display', 'points' => 5], // partial point example
            ['content' => 'print_text', 'points' => 0],
            ['content' => 'show', 'points' => 0],
        ]);

        $quiz1->questions()->create([
            'type' => 'essay',
            'content' => 'Jelaskan secara singkat apa itu MVC (Model-View-Controller) dalam konteks Laravel.',
            'points' => 50,
        ]);

        // Second realistic quiz
        $quiz2 = Quiz::create([
            'title' => 'Pengetahuan Umum Indonesia',
            'description' => 'Tes wawasan kebangsaan dan pengetahuan umum tentang geografi serta sejarah Indonesia.',
            'is_active' => true,
        ]);

        $q3 = $quiz2->questions()->create([
            'type' => 'multiple_choice',
            'content' => 'Siapakah presiden pertama Republik Indonesia?',
            'explanation' => 'Presiden pertama Republik Indonesia adalah Ir. Soekarno yang memproklamasikan kemerdekaan bersama Moh. Hatta pada tanggal 17 Agustus 1945.',
        ]);
        $q3->options()->createMany([
            ['content' => 'Soeharto', 'points' => 0],
            ['content' => 'B.J. Habibie', 'points' => 0],
            ['content' => 'Soekarno', 'points' => 10],
            ['content' => 'Abdurrahman Wahid', 'points' => 0],
        ]);

        $quiz2->questions()->create([
            'type' => 'essay',
            'content' => 'Sebutkan 3 pulau terbesar di Indonesia beserta provinsi yang ada di dalamnya.',
            'points' => 30,
        ]);

        // Create 3 Try Out SKD CPNS
        for ($i = 1; $i <= 3; $i++) {
            $quiz = Quiz::create([
                'title' => 'Try Out SKD CPNS ' . $i,
                'description' => 'Simulasi ujian seleksi SKD CPNS lengkap seri ke-' . $i . ' terdiri dari TIU, TWK, dan TKP.',
                'time_limit' => 100,
                'is_active' => true,
            ]);

            // TWK (5 questions)
            for ($j = 1; $j <= 5; $j++) {
                $q = $quiz->questions()->create([
                    'type' => 'multiple_choice',
                    'category' => 'TWK',
                    'content' => "Soal TWK nomor $j untuk Try Out $i. Manakah pernyataan di bawah ini yang paling mencerminkan pengamalan sila ke-$j Pancasila?",
                    'explanation' => "Pembahasan: Sila ke-$j memiliki butir pengamalan spesifik yang menuntut warga negara untuk mematuhinya. Opsi B adalah jawaban yang paling tepat.",
                ]);
                $q->options()->createMany([
                    ['content' => 'Pernyataan A (Salah)', 'points' => 0],
                    ['content' => 'Pernyataan B (Benar)', 'points' => 5],
                    ['content' => 'Pernyataan C (Salah)', 'points' => 0],
                    ['content' => 'Pernyataan D (Salah)', 'points' => 0],
                    ['content' => 'Pernyataan E (Salah)', 'points' => 0],
                ]);
            }

            // TIU (5 questions)
            for ($j = 1; $j <= 5; $j++) {
                $q = $quiz->questions()->create([
                    'type' => 'multiple_choice',
                    'category' => 'TIU',
                    'content' => "Soal TIU nomor $j untuk Try Out $i. Jika X = $j dan Y = " . ($j + 5) . ", berapakah hasil dari X + Y?",
                    'explanation' => "Pembahasan: X + Y = $j + " . ($j + 5) . " = " . ((2*$j) + 5) . ".",
                ]);
                $q->options()->createMany([
                    ['content' => ( (2*$j) + 2 ), 'points' => 0],
                    ['content' => ( (2*$j) + 3 ), 'points' => 0],
                    ['content' => ( (2*$j) + 5 ), 'points' => 5],
                    ['content' => ( (2*$j) + 7 ), 'points' => 0],
                    ['content' => ( (2*$j) + 9 ), 'points' => 0],
                ]);
            }

            // TKP (5 questions)
            for ($j = 1; $j <= 5; $j++) {
                $q = $quiz->questions()->create([
                    'type' => 'multiple_choice',
                    'category' => 'TKP',
                    'content' => "Soal TKP nomor $j untuk Try Out $i. Saat Anda sedang sibuk mengerjakan tugas kantor dengan tenggat waktu hari ini, seorang rekan meminta bantuan. Apa yang akan Anda lakukan?",
                    'explanation' => "Pembahasan: Dalam soal TKP, orientasi pada hasil dan profesionalisme sangat penting, namun kolaborasi juga dinilai. Opsi A menunjukkan prioritas yang sangat baik.",
                ]);
                $q->options()->createMany([
                    ['content' => 'Menyelesaikan tugas saya terlebih dahulu, lalu membantunya jika masih ada waktu (Poin 5)', 'points' => 5],
                    ['content' => 'Meminta rekan lain untuk membantunya agar pekerjaan saya tidak terganggu (Poin 4)', 'points' => 4],
                    ['content' => 'Membantunya sebentar, lalu kembali fokus ke pekerjaan saya (Poin 3)', 'points' => 3],
                    ['content' => 'Menolak dengan tegas karena tugas saya lebih penting (Poin 2)', 'points' => 2],
                    ['content' => 'Segera membantunya dan menunda pekerjaan saya sendiri (Poin 1)', 'points' => 1],
                ]);
            }
        }
    }
}
