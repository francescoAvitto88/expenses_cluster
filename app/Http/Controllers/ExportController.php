<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportCsv(Request $request)
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

        $filename = "spese_{$year}_{$month}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Data', 'Categoria', 'Titolo', 'Importo', 'Note'];

        $callback = function() use($expenses, $columns) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for proper Excel rendering
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';'); // EU format delimiter

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    $expense->expense_date->format('Y-m-d'),
                    $expense->category->name,
                    $expense->title,
                    number_format($expense->amount, 2, ',', ''),
                    $expense->notes
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
