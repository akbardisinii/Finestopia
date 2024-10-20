<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function getFinancialSummary()
    {
        $summary = DB::table('incomes')
            ->select(
                DB::raw('COALESCE(SUM(CASE WHEN incomes.user_id = ' . Auth::id() . ' THEN incomes.amount ELSE 0 END), 0) as totalIncome'),
                DB::raw('COALESCE(SUM(CASE WHEN expenses.user_id = ' . Auth::id() . ' THEN expenses.amount ELSE 0 END), 0) as totalExpense')
            )
            ->leftJoin('expenses', 'incomes.user_id', '=', 'expenses.user_id')
            ->first();

        return response()->json([
            'totalIncome' => $summary->totalIncome,
            'totalExpense' => $summary->totalExpense
        ]);
    }

    // Jika Anda memiliki metode lain di sini, biarkan mereka tetap ada
}