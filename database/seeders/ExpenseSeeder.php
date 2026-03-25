<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\User;
use App\Models\Category;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::whereNull('created_by')->get();

        if ($users->count() > 0 && $categories->count() > 0) {
            foreach ($users as $user) {
                for ($i = 0; $i < 20; $i++) {
                    Expense::factory()->create([
                        'user_id' => $user->id,
                        'category_id' => $categories->random()->id,
                        'title' => 'Spesa di prova',
                    ]);
                }
            }
        }
    }
}
