<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $categories = [
            ['name' => 'Salary',       'type' => 'income',  'color' => '#10B981'],
            ['name' => 'Freelance',    'type' => 'income',  'color' => '#3B82F6'],
            ['name' => 'Food',         'type' => 'expense', 'color' => '#EF4444'],
            ['name' => 'Transport',    'type' => 'expense', 'color' => '#F59E0B'],
            ['name' => 'Utilities',    'type' => 'expense', 'color' => '#8B5CF6'],
        ];

        foreach ($categories as $cat) {
            Category::create(array_merge($cat, ['user_id' => $user->id]));
        }
    }
}
