<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use PDF;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

class AllocationController extends Controller
{
    public function downloadPdf()
{
    $user = Auth::user();
    $homeController = new HomeController();
    
    $totalIncome = Income::where('user_id', $user->id)->sum('amount') ?? 0;
    $totalExpense = Expense::where('user_id', $user->id)->sum('amount') ?? 0;
    $balance = $totalIncome - $totalExpense;
    
    $allocation = $homeController->calculateAllocation($balance);

    $pdf = PDF::loadView('reports.allocation', [
        'user' => $user,
        'allocation' => $allocation
    ]);

    return $pdf->download('financial_allocation.pdf');
}
}