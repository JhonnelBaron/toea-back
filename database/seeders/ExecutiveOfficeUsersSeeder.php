<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExecutiveOfficeUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            'Administrative Service',
            'Legal Division',
            'Certification Office',
            'Financial and Management Service',
            'National Institute for Technical Education and Skills Development',
            'Public Information and Assistance Division',
            'Planning Office',
            'Partnership and Linkages Office',
            'Regional Operations Management Office',
            'Information and Communication Office',
            'World Skills'
        ];

        foreach ($offices as $office) {
            // Split office name into first and last name
            $parts = explode(' ', $office);
            $firstName = $parts[0];
            $lastName = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : $parts[0];

            // Create email by replacing spaces with dot and making it lowercase
            $email = strtolower(str_replace(' ', '.', $office)) . '@tesda.gov.ph';

            DB::table('users')->insert([
                'user_type' => 'executive office focal',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'designation' => 'Central Office',
                'office' => $office,
                'email' => $email,
                'password' => Hash::make('toea2025'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
