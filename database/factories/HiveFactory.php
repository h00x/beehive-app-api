<?php

namespace Database\Factories;

use App\Models\Hive;
use Illuminate\Database\Eloquent\Factories\Factory;

class HiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hive::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'empty' => $this->faker->boolean,
            'archived' => $this->faker->boolean,
        ];
    }
}
