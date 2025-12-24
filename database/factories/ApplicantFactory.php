<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = Position::pluck('id')->toArray();
        if (empty($positions)) {
             $pos = Position::create(['name' => 'Staff IT', 'is_active' => 1]);
             $positions = [$pos->id];
        }

        return [
            'position_id' => $this->faker->randomElement($positions),
            'nik' => $this->faker->unique()->numerify('################'),
            'full_name' => $this->faker->name(),
            'nickname' => $this->faker->firstName(),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->date(),
            'height' => $this->faker->numberBetween(150, 190),
            'weight' => $this->faker->numberBetween(50, 90),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'marital_status' => $this->faker->randomElement(['Belum Menikah', 'Menikah']),
            'last_education' => $this->faker->randomElement(['SMA', 'D3', 'S1']),
            'id_card_address' => $this->faker->address(),
            'domicile_address' => $this->faker->address(),
            'residence_ownership_status' => $this->faker->randomElement(['Rumah Sendiri', 'Kontrak']),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'father_name' => $this->faker->name('male'),
            'mother_name' => $this->faker->name('female'),
            'father_occupation' => $this->faker->jobTitle(),
            'mother_occupation' => $this->faker->jobTitle(),
            'parents_address' => $this->faker->address(),
            'status' => $this->faker->randomElement(['new', 'screening', 'interview', 'accepted', 'rejected']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
