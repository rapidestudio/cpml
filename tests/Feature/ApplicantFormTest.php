<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicantFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_form_screen_can_be_rendered(): void
    {
        $response = $this->get('/apply');

        $response->assertStatus(200);
    }

    public function test_new_applicants_can_submit_data(): void
    {
        $response = $this->post('/apply', [
            'nik' => '1234567890123456',
            'full_name' => 'John Doe',
            'nickname' => 'John',
            'gender' => 'Laki-laki',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'religion' => 'Islam',
            'marital_status' => 'Belum Menikah',
            'last_education' => 'S1',
            'id_card_address' => 'Jl. Test No. 1',
            'domicile_address' => 'Jl. Test No. 1',
            'residence_ownership_status' => 'Rumah Sendiri',
            'phone_number' => '08123456789',
            'email' => 'john@example.com',
            'father_name' => 'Father',
            'mother_name' => 'Mother',
            
            // Nested
            'work_experiences' => [
                [
                    'company_name' => 'PT Test',
                    'position' => 'Staff',
                    'start_year' => '2020',
                    'end_year' => '2022',
                    'company_address' => 'Jl. Company',
                ]
            ],
            'children' => [],
            'emergency_contacts' => []
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('applicants', [
            'email' => 'john@example.com',
            'nik' => '1234567890123456',
        ]);
        $this->assertDatabaseHas('applicant_work_experiences', [
            'company_name' => 'PT Test',
        ]);
    }

    public function test_admin_can_view_applicants_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/applicants');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_applicant_details(): void
    {
        $user = User::factory()->create();
        $applicant = Applicant::create([
             'nik' => '1234567890123456',
            'full_name' => 'John Doe',
            'nickname' => 'John',
            'gender' => 'Laki-laki',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '1990-01-01',
            'religion' => 'Islam',
            'marital_status' => 'Belum Menikah',
            'last_education' => 'S1',
            'id_card_address' => 'Jl. Test No. 1',
            'domicile_address' => 'Jl. Test No. 1',
            'residence_ownership_status' => 'Rumah Sendiri',
            'phone_number' => '08123456789',
            'email' => 'john@example.com',
            'father_name' => 'Father',
            'mother_name' => 'Mother',
        ]);

        $response = $this->actingAs($user)->get("/admin/applicants/{$applicant->id}");

        $response->assertStatus(200);
    }
}
