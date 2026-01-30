<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Default Roles
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        Role::firstOrCreate(['name' => 'Vendor']);

        Role::firstOrCreate(['name' => 'User']);

        // 2. Create the Admin record with credentials
        $admin = Admin::updateOrCreate(
            ['email' => 'fahadjdy12@gmail.com'],
            [
                'name' => 'Fahad Admin',
                'phone' => '7203070468',
                'password' => Hash::make('Fahad@123'),
                'is_active' => true,
                'is_super_admin' => true
            ]
        );

        // 3. Create the User record with same credentials
        $user = User::updateOrCreate(
            ['email' => 'fahadjdy12@gmail.com'],
            [
                'name' => 'Fahad User',
                'phone' => '7203070468',
                'password' => Hash::make('Fahad@123'),
                'blood_group' => 'O+',
                'gender' => 'male',
                'is_active' => true,
            ]
        );

        // 4. Attach Role to User
        $userRole = Role::where('name', 'User')->first();
        if ($userRole && !$user->roles()->where('role_id', $userRole->id)->exists()) {
            $user->roles()->attach($userRole->id);
        }

        // 5. Add default skills
        $skills = ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Web Development'];
        $skillIds = [];
        foreach ($skills as $skill) {
            $tag = \App\Models\Tag::firstOrCreate(
                ['name' => $skill],
                ['slug' => \Illuminate\Support\Str::slug($skill), 'category' => 'skill', 'is_active' => true]
            );
            $skillIds[] = $tag->id;
        }
        $user->skills()->sync($skillIds);

        $this->command->info('Admin user and default skills seeded successfully!');
    }
}
