<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function getTotal()
    {
        $totalExpense = Expense::forUser(Auth::id())->sum('amount');
        return response()->json(['totalExpense' => $totalExpense]);
    }

    public function index()
    {
        $expenses = Expense::forUser(Auth::id())->orderBy('date', 'desc')->get();
        return response()->json($expenses);
    }

    public function show($id)
    {
        $expense = Expense::forUser(Auth::id())->findOrFail($id);
        return response()->json($expense);
    }

    public function store(Request $request)
    {
        Log::info('Received expense data:', $request->all());

        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
                'description' => 'nullable|string|max:1000',
                'category_id' => 'nullable|exists:categories,id',
            ]);

            $user = Auth::user();
            $currentBalance = $this->calculateBalance($user->id);

            Log::info("Current balance: {$currentBalance}, Expense amount: {$validatedData['amount']}");

            if ($currentBalance < $validatedData['amount']) {
                DB::rollBack();
                Log::warning("Insufficient balance. Current: {$currentBalance}, Required: {$validatedData['amount']}");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Saldo tidak cukup',
                    'balance' => $currentBalance
                ], 400);
            }

            $expense = Expense::create([
                'user_id' => $user->id,
                'title' => $validatedData['title'],
                'amount' => $validatedData['amount'],
                'date' => $validatedData['date'],
                'description' => $validatedData['description'] ?? null,
                'category_id' => $validatedData['category_id'] ?? null,
            ]);

            $newBalance = $currentBalance - $validatedData['amount'];

            DB::commit();

            Log::info("Expense created successfully. New balance: {$newBalance}");

            return response()->json([
                'status' => 'success',
                'message' => 'Expense has been added successfully.',
                'expense' => $expense,
                'balance' => $newBalance,
                'totalExpense' => Expense::forUser($user->id)->sum('amount')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating expense: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while adding the expense.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::forUser(Auth::id())->findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'amount' => 'sometimes|required|numeric|min:0',
                'date' => 'sometimes|required|date',
                'description' => 'nullable|string|max:1000',
                'category_id' => 'nullable|exists:categories,id',
            ]);

            $user = Auth::user();
            $currentBalance = $this->calculateBalance($user->id) + $expense->amount;

            if ($currentBalance < $validatedData['amount']) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Saldo tidak cukup untuk pembaruan ini',
                    'balance' => $currentBalance
                ], 400);
            }

            $expense->update($validatedData);

            $newBalance = $currentBalance - $validatedData['amount'];

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Expense has been updated successfully.',
                'expense' => $expense,
                'balance' => $newBalance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating expense: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the expense.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::forUser(Auth::id())->findOrFail($id);
            
            $user = Auth::user();
            $newBalance = $this->calculateBalance($user->id) + $expense->amount;

            $expense->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Expense has been deleted successfully.',
                'balance' => $newBalance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting expense: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the expense.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateBalance($userId)
    {
        $totalIncome = Income::where('user_id', $userId)->sum('amount');
        $totalExpense = Expense::forUser($userId)->sum('amount');
        return $totalIncome - $totalExpense;
    }
}