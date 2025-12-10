<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Question::insert([
            ['text' => 'Apakah Anda bersedia bekerja di hari libur?', 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'Apakah Anda bersedia ditempatkan di seluruh cabang perusahaan?', 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'Apakah Anda memiliki riwayat penyakit serius?', 'created_at' => now(), 'updated_at' => now()],
            ['text' => 'Apakah Anda pernah terlibat kasus hukum?', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
