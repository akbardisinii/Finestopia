<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    public function getLatestAllocation()
{
    $user = Auth::user();
    $latestSalary = $user->salaries()->latest()->first();

    if (!$latestSalary) {
        return response()->json(['message' => 'No salary data found'], 404);
    }

    $allocation = $this->calculateAllocation($latestSalary->amount);
    $allocation['salary'] = $latestSalary->amount;

    return response()->json($allocation);
}
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $salary = Salary::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        $allocation = $this->calculateAllocation($request->amount);

        return response()->json([
            'success' => true,
            'salary' => $salary,
            'allocation' => $allocation,
        ]);
    }

    private function calculateAllocation($amount)
    {
        if ($amount < 5000000) {
            return [
                'primary' => $amount * 0.5,
                'secondary' => $amount * 0.2,
                'investment' => $amount * 0.3,
                'debt' => 0
            ];
        } else {
            return [
                'primary' => $amount * 0.4,
                'secondary' => $amount * 0.1,
                'investment' => $amount * 0.2,
                'debt' => $amount * 0.3
            ];
        }
    }
}