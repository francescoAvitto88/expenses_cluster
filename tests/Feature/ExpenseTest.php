<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Expense;
use Spatie\Permission\Models\Role;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_user_can_view_own_expenses()
    {
        $user = User::factory()->create()->assignRole('user');
        $otherUser = User::factory()->create()->assignRole('user');

        $category = Category::create(['name' => 'Food']);
        
        $ownExpense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'My lunch',
            'amount' => 15.50,
            'expense_date' => now(),
        ]);

        $otherExpense = Expense::create([
            'user_id' => $otherUser->id,
            'category_id' => $category->id,
            'title' => 'Their lunch',
            'amount' => 20.00,
            'expense_date' => now(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('My lunch');
        $response->assertDontSee('Their lunch');
    }

    public function test_admin_can_view_all_expenses()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $otherUser = User::factory()->create()->assignRole('user');

        $category = Category::create(['name' => 'Food']);
        
        Expense::create([
            'user_id' => $otherUser->id,
            'category_id' => $category->id,
            'title' => 'Their lunch',
            'amount' => 20.00,
            'expense_date' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Their lunch');
    }
}
