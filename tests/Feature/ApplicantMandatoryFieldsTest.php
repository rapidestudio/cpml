<?php

namespace Tests\Feature;

use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ApplicantMandatoryFieldsTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_personal_data_fields_are_mandatory()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
            // Missing all personal data
        ]);

        $response->assertSessionHasErrors(['nik', 'full_name', 'gender', 'place_of_birth', 'date_of_birth', 'height', 'weight', 'religion', 'marital_status', 'last_education', 'id_card_address', 'domicile_address', 'residence_ownership_status', 'phone_number', 'email']);
    }

    public function test_family_data_parents_are_mandatory()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
            // Provide valid personal data to isolate family data errors
            'nik' => '1234567890123456',
            'full_name' => 'John Doe',
            'gender' => 'Laki-laki',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'height' => 170,
            'weight' => 60,
            'religion' => 'Islam',
            'marital_status' => 'Belum Menikah',
            'last_education' => 'S1',
            'id_card_address' => 'Jl. Test',
            'domicile_address' => 'Jl. Test',
            'residence_ownership_status' => 'Rumah Sendiri',
            'phone_number' => '08123456789',
            'email' => 'john@example.com',
            // Missing Parents Data
        ]);

        $response->assertSessionHasErrors(['father_name', 'mother_name', 'father_occupation', 'mother_occupation', 'parents_address']);
    }

    public function test_spouse_data_mandatory_if_married()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
            'nik' => '1234567890123456',
            'full_name' => 'John Doe',
            'gender' => 'Laki-laki',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'height' => 170,
            'weight' => 60,
            'religion' => 'Islam',
            'marital_status' => 'Menikah', // MARRIED
            'last_education' => 'S1',
            'id_card_address' => 'Jl. Test',
            'domicile_address' => 'Jl. Test',
            'residence_ownership_status' => 'Rumah Sendiri',
            'phone_number' => '08123456789',
            'email' => 'john@example.com',
            // Family Data Parents
            'father_name' => 'Dad',
            'mother_name' => 'Mom',
            'father_occupation' => 'Retired',
            'mother_occupation' => 'Housewife',
            'parents_address' => 'Jl. Parents',
             // Missing Spouse Data
        ]);

        $response->assertSessionHasErrors(['spouse_name', 'spouse_address', 'spouse_age', 'spouse_occupation', 'spouse_phone']);
    }

    public function test_work_experience_is_mandatory()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
            'nik' => '1234567890123456',
            'full_name' => 'John Doe',
            'gender' => 'Laki-laki',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'height' => 170,
            'weight' => 60,
            'religion' => 'Islam',
            'marital_status' => 'Belum Menikah',
            'last_education' => 'S1',
            'id_card_address' => 'Jl. Test',
            'domicile_address' => 'Jl. Test',
            'residence_ownership_status' => 'Rumah Sendiri',
            'phone_number' => '08123456789',
            'email' => 'john@example.com',
            'father_name' => 'Dad',
            'mother_name' => 'Mom',
            'father_occupation' => 'Retired',
            'mother_occupation' => 'Housewife',
            'parents_address' => 'Jl. Parents',
            // Work Experience Missing
             'work_experiences' => [],
        ]);

        $response->assertSessionHasErrors(['work_experiences']);
    }

    public function test_emergency_contact_is_mandatory()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
            // ... (other data assumed present if we focused testing, but here we just check if this specific error key exists)
             'emergency_contacts' => [],
        ]);

        $response->assertSessionHasErrors(['emergency_contacts']);
    }
    
    public function test_checklist_is_mandatory()
    {
        $position = Position::create(['name' => 'Backend Developer', 'is_active' => true]);

        $response = $this->post('/apply', [
            'position_id' => $position->id,
             'checklist' => [],
        ]);

        $response->assertSessionHasErrors(['checklist']);
    }
}
