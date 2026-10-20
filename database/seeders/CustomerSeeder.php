<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['full_name' => 'Maria Santos', 'email' => 'maria.santos@example.com', 'phone' => '09171234567'],
            ['full_name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@example.com', 'phone' => '09181234567'],
            ['full_name' => 'Angela Reyes', 'email' => 'angela.reyes@example.com', 'phone' => null],
            ['full_name' => 'Paolo Garcia', 'email' => 'paolo.garcia@example.com', 'phone' => '09201234567'],
            ['full_name' => 'Camille Mendoza-Villanueva', 'email' => 'camille.mendoza.villanueva@example.com', 'phone' => '09211234567']] as $record) {
            Customer::firstOrCreate(['email' => $record['email']], $record);
        }
    }
}
