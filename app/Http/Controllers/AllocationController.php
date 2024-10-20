<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Allocation;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AllocationController extends Controller
{
    public function getAllocation()
    {
        Log::info('getAllocation method called');
        try {
            $user = Auth::user();
            $totalIncome = Income::where('user_id', $user->id)->sum('amount');
            $allocation = Allocation::where('user_id', $user->id)->first();
            
            if (!$allocation) {
                Log::info('No allocation found, creating new one');
                $allocationData = $this->calculateAllocation($totalIncome);
                $allocation = new Allocation($allocationData);
                $allocation->user_id = $user->id;
                $allocation->save();
            } else {
                // Update existing allocation
                $allocationData = $this->calculateAllocation($totalIncome);
                $allocation->update($allocationData);
            }
 
            $percentages = $allocation->getPercentages();
            $result = [
                'totalIncome' => $totalIncome,
                'allocations' => [
                    'primary' => ['percentage' => $percentages['primary'], 'amount' => $allocation->primary],
                    'secondary' => ['percentage' => $percentages['secondary'], 'amount' => $allocation->secondary],
                    'investment' => ['percentage' => $percentages['investment'], 'amount' => $allocation->investment],
                    'debt' => ['percentage' => $percentages['debt'], 'amount' => $allocation->debt],
                ],
                'total' => $allocation->total,
            ];
            Log::info('Returning allocation data', $result);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in getAllocation: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    private function calculateAllocation($amount)
    {
        if ($amount < 5000000) {
            return [
                'primary' => $amount * 0.5,
                'secondary' => $amount * 0.2,
                'investment' => $amount * 0.3,
                'debt' => 0,
                'total' => $amount,
            ];
        } else {
            return [
                'primary' => $amount * 0.4,
                'secondary' => $amount * 0.1,
                'investment' => $amount * 0.2,
                'debt' => $amount * 0.3,
                'total' => $amount,
            ];
        }
    }

    public function updateAllocation(Request $request)
    {
        Log::info('updateAllocation method called', $request->all());
        try {
            $user = Auth::user();
            $allocation = Allocation::where('user_id', $user->id)->first();

            if (!$allocation) {
                Log::info('No allocation found, creating new one');
                $allocation = new Allocation();
                $allocation->user_id = $user->id;
            }

            $allocation->primary = $request->input('primary');
            $allocation->secondary = $request->input('secondary');
            $allocation->investment = $request->input('investment');
            $allocation->debt = $request->input('debt');
            $allocation->total = $allocation->primary + $allocation->secondary + $allocation->investment + $allocation->debt;

            $allocation->save();

            Log::info('Allocation updated successfully', $allocation->toArray());
            return response()->json(['message' => 'Allocation updated successfully', 'allocation' => $allocation]);
        } catch (\Exception $e) {
            Log::error('Error in updateAllocation: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating allocation'], 500);
        }
    }
}