<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Expense::with('category');
        
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }
        
        return response()->json($query->orderBy('expense_date', 'desc')->paginate(15));
    }
}
