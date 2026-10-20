<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['username' => 'admin01', 'full_name' => 'Andrea Ramos'],
            ['username' => 'cashier01', 'full_name' => 'Miguel Torres'],
            ['username' => 'cashier02', 'full_name' => 'Sofia Lopez'],
            ['username' => 'staff01', 'full_name' => 'Daniel Aquino'],
            ['username' => 'manager01', 'full_name' => 'Isabella Flores']] as $record) {
            User::firstOrCreate(['username' => $record['username']], $record);
        }
    }
}
