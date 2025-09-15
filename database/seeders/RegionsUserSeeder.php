<?php

namespace Database\Seeders;

use App\Models\Nominee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RegionsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
    {
        $regions = [
            'small' => ['Region II', 'CAR', 'VIII', 'Caraga', 'Region IX', 'Region IV-B'],
            'medium' => ['Region XII', 'Region X', 'Region I', 'Region VI', 'Region XI', 'Region V'],
            'large' => ['NCR', 'Region IV-A', 'Region VII', 'Region III'],
        ];

        foreach ($regions as $category => $regionList) {
            foreach ($regionList as $regionName) {
                // Create the user
                $user = User::create([
                    'user_type' => 'nominee',
                    'first_name' => $regionName, // optional, can leave null
                    'last_name' => '',
                    'designation' => '',
                    'office' => '',
                    'email' => strtolower(str_replace(' ', '_', $regionName)) . '@example.com',
                    'password' => Hash::make('password123'), // default password
                ]);

                // Create the nominee
                Nominee::create([
                    'user_id' => $user->id,
                    'nominee_type' => 'BRO',
                    'nominee_category' => $category,
                    'region' => $regionName,
                    'province' => null,
                    'nominee_name' => $regionName,
                    'status' => null,
                ]);
            }
        }

        $this->command->info('Nominee users seeded successfully!');
    }
}
