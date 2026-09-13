<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Admin']);
        $staffRole = Role::create(['name' => 'Staff Gudang']);

        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@inventory.test',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('Admin');
        
        $staff = User::factory()->create([
            'name' => 'Staff Gudang 1',
            'email' => 'staff@inventory.test',
            'password' => bcrypt('password'),
        ]);
        
        $staff->assignRole('Staff Gudang');

        $this->call(DummyDataSeeder::class);
    }
}
