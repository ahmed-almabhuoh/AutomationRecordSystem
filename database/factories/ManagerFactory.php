<?php

namespace Database\Factories;

use App\Models\Manager;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ManagerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'fname' => $this->faker->name(),
            'sname' => $this->faker->name(),
            'tname' => $this->faker->name(),
            'lname' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->numberBetween(1000000, 99999999),
            'identity_no' => $this->faker->numberBetween(100000000, 999999999),
            'password' => Hash::make($this->faker->password()),
            'status' => $this->faker->randomElement(Manager::STATUS),
            'gender' => $this->faker->randomElement(Manager::GENDER),
            'local_region' => $this->faker->name(),
            'description' => $this->faker->name(),
        ];
    }
}
