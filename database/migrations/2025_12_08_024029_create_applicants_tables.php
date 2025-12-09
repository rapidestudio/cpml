<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Personal Data
            $table->string('nik')->unique(); // No KTP
            $table->string('full_name'); // Nama Lengkap
            $table->string('nickname')->nullable(); // Nama Panggilan
            $table->enum('gender', ['Laki-laki', 'Perempuan']); // Jenis Kelamin
            $table->string('place_of_birth'); // Tempat Lahir
            $table->date('date_of_birth'); // Tanggal Lahir
            $table->string('religion'); // Agama
            $table->enum('marital_status', ['Belum Menikah', 'Menikah', 'Cerai']); // Status Pernikahan
            $table->string('last_education'); // Pendidikan Terakhir
            $table->text('id_card_address'); // Alamat KTP
            $table->text('domicile_address'); // Alamat Domisili
            $table->string('residence_ownership_status'); // Status Kepemilikan: Rumah Sendiri, dll
            $table->string('phone_number'); // No Handphone
            $table->string('email')->unique(); // Email

            // Family Data - Parents
            $table->string('father_name'); // Nama Ayah
            $table->string('mother_name'); // Nama Ibu
            $table->string('father_occupation')->nullable(); // Pekerjaan Ayah
            $table->string('mother_occupation')->nullable(); // Pekerjaan Ibu
            $table->text('parents_address')->nullable(); // Alamat Orang Tua

            // Family Data - Spouse (Nullable)
            $table->string('spouse_name')->nullable(); // Nama Suami/Istri
            $table->text('spouse_address')->nullable(); // Alamat Suami/Istri
            $table->integer('spouse_age')->nullable(); // Usia Suami/Istri
            $table->string('spouse_occupation')->nullable(); // Pekerjaan Suami/Istri
            $table->string('spouse_phone')->nullable(); // No Handphone Suami/Istri

            $table->timestamps();
        });

        Schema::create('applicant_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('company_address')->nullable();
            $table->string('position');
            $table->string('start_year'); // Bisa tahun saja atau tanggal
            $table->string('end_year')->nullable(); // Nullable jika masih bekerja
            $table->timestamps();
        });

        Schema::create('applicant_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('education')->nullable();
            $table->timestamps();
        });

        Schema::create('applicant_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship');
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_emergency_contacts');
        Schema::dropIfExists('applicant_children');
        Schema::dropIfExists('applicant_work_experiences');
        Schema::dropIfExists('applicants');
    }
};
