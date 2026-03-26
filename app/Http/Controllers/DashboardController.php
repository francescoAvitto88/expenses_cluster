<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $query = Expense::with('category')
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year);

        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $expensesByCategory = $expenses->groupBy(function($expense) {
            return $expense->category->name;
        })->map(function($group) {
            return $group->sum('amount');
        })->sortByDesc(function($amount) {
            return $amount;
        });

        $total = $expenses->sum('amount');

        return view('dashboard', compact('expenses', 'expensesByCategory', 'total', 'month', 'year'));
    }
}
