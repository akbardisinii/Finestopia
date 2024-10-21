<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function getBalance()
    {
        $user = Auth::user();
        $totalIncome = Income::where('user_id', $user->id)->sum('amount') ?? 0;
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount') ?? 0;
        $balance = $totalIncome - $totalExpense;

        return response()->json([
            'balance' => $balance,
            'formattedBalance' => 'Rp. ' . number_format($balance, 0, ',', '.')
        ]);
    }
    public function index()
    {
        $user = Auth::user();
        $totalIncome = Income::where('user_id', $user->id)->sum('amount') ?? 0;
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount') ?? 0;
        
        // Log untuk debugging
        Log::info('Total Income: ' . $totalIncome);
        Log::info('Total Expense: ' . $totalExpense);

        // Hitung saldo (balance)
        $balance = $totalIncome - $totalExpense;

        // Ambil data alokasi
        $allocation = $this->calculateAllocation($balance);

        // Ambil expense terakhir untuk debugging
        $lastExpense = Expense::where('user_id', $user->id)->latest()->first();
        Log::info('Last Expense: ', $lastExpense ? $lastExpense->toArray() : ['No expense found']);

        return view('home', compact('totalIncome', 'totalExpense', 'balance', 'allocation', 'lastExpense'));
    }

    public function calculateAllocation($amount)
    {
        if ($amount == 0) {
            return [
                'type' => 'No Income',
                'allocations' => [
                    'primary' => ['amount' => 0, 'percentage' => 0],
                    'secondary' => ['amount' => 0, 'percentage' => 0],
                    'investment' => ['amount' => 0, 'percentage' => 0],
                    'debt' => ['amount' => 0, 'percentage' => 0]
                ],
                'total' => 0
            ];
        }

        if ($amount < 5000000) {
            $primary = $amount * 0.5;
            $secondary = $amount * 0.2;
            $investment = $amount * 0.3;
            $debt = 0;
        } else {
            $primary = $amount * 0.4;
            $secondary = $amount * 0.1;
            $investment = $amount * 0.2;
            $debt = $amount * 0.3;
        }

        return [
            'type' => $amount < 5000000 ? 'Pendapatan Rendah' : 'Pendapatan Tinggi',
            'notif' => $amount < 5000000 ? ' Pendapatan Anda di bawah Rp5.000.000. Sebaiknya hindari memiliki cicilan agar kondisi keuangan tetap sehat' : '',
            'allocations' => [
                'primary' => [
                    'amount' => $primary,
                    'percentage' => $this->calculatePercentage($primary, $amount)
                ],
                'secondary' => [
                    'amount' => $secondary,
                    'percentage' => $this->calculatePercentage($secondary, $amount)
                ],
                'investment' => [
                    'amount' => $investment,
                    'percentage' => $this->calculatePercentage($investment, $amount)
                ],
                'debt' => [
                    'amount' => $debt,
                    'percentage' => $this->calculatePercentage($debt, $amount)
                ]
            ],
            'total' => $amount
        ];
    }

    private function calculatePercentage($part, $total)
    {
        return $total > 0 ? ($part / $total) * 100 : 0;
    }
}