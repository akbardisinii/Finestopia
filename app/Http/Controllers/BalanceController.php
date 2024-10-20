<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AddBalance;
use App\Models\PaymentOption;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\Log;

class BalanceController extends Controller
{
    protected $addBalance;

    public function __construct(AddBalance $addBalance)
    {
        $this->addBalance = $addBalance;
    }

    public function index()
    {
        $user = Auth::user();
        $balances = Expense::select(['id', 'title', 'amount', 'description'])
            ->where('user_id', $user->id)
            ->paginate(5);
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        return view('balance.index', compact('balances', 'totalBalance', 'totalIncome', 'totalExpense'));
    }

    public function apiIndex()
    {
        $user = Auth::user();
        $balances = PaymentOption::select(['id', 'title', 'balance'])
            ->where('user_id', $user->id)
            ->get();
        return response()->json($balances);
    }

    public function show($id)
    {
        $balance = PaymentOption::findOrFail($id);
        return view('balance.show', compact('balance'));
    }

    public function edit($id)
    {
        $balance = PaymentOption::findOrFail($id);
        return view('balance.add-balance', compact('balance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'balance' => 'required',
            'condition' => 'required'
        ]);

        $resolve = $this->addBalance->saveBalance($request->balance, $request->condition, $id);

        if (!$resolve['status']) {
            return redirect()->back()->with('error', $resolve['error']);
        }

        return redirect('api/balances/index')->with('success', $resolve['success']);
    }

    public function getTotalBalance()
    {
        $user = Auth::user();
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        return response()->json(['totalBalance' => $totalBalance]);
    }

    public function addExpense(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'description' => 'required|string',
                'date' => 'required|date',
            ]);

            $user = Auth::user();
            $validatedData['user_id'] = $user->id;

            \DB::beginTransaction();

            $paymentOption = PaymentOption::where('user_id', $user->id)->first();
            if (!$paymentOption) {
                throw new \Exception('No payment option found');
            }

            Log::info('Current balance: ' . $paymentOption->balance);
            Log::info('Expense amount: ' . $validatedData['amount']);

            // Check if balance is sufficient
            if ($paymentOption->balance < $validatedData['amount']) {
                \DB::rollBack();
                Log::warning('Insufficient balance. Current: ' . $paymentOption->balance . ', Required: ' . $validatedData['amount']);
                return response()->json([
                    'message' => 'Saldo tidak cukup',
                    'current_balance' => $paymentOption->balance,
                    'expense_amount' => $validatedData['amount']
                ], 400);
            }

            $expense = Expense::create($validatedData);

            $newBalance = $paymentOption->balance - $expense->amount;

            $paymentOption->update([
                'balance' => $newBalance
            ]);

            \DB::commit();

            Log::info('Expense added successfully. New balance: ' . $newBalance);

            return response()->json([
                'message' => 'Expense added successfully',
                'data' => [
                    'expense' => $expense,
                    'new_balance' => $newBalance
                ]
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error adding expense: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add expense', 'error' => $e->getMessage()], 500);
        }
    }

    public function addIncome(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date',
            ]);

            $user = Auth::user();
            $validatedData['user_id'] = $user->id;

            \DB::beginTransaction();

            $income = Income::create($validatedData);

            $paymentOption = PaymentOption::where('user_id', $user->id)->first();
            if (!$paymentOption) {
                $paymentOption = PaymentOption::create([
                    'user_id' => $user->id,
                    'title' => 'Default Payment Option',
                    'balance' => 0
                ]);
            }

            $newBalance = $paymentOption->balance + $income->amount;

            $paymentOption->update([
                'balance' => $newBalance
            ]);

            \DB::commit();

            Log::info('Income added successfully. New balance: ' . $newBalance);

            return response()->json([
                'message' => 'Income added successfully',
                'data' => [
                    'income' => $income,
                    'new_balance' => $newBalance
                ]
            ], 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error adding income: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add income', 'error' => $e->getMessage()], 500);
        }
    }

    public function downloadExpenseReport()
    {
        $user = Auth::user();
        $expenses = Expense::where('user_id', $user->id)
                           ->orderBy('date', 'desc')
                           ->orderBy('id', 'desc')
                           ->get()
                           ->map(function ($expense) {
                               $expense->formatted_date = $expense->date->format('d/m/Y');
                               return $expense;
                           });

        $totalExpense = $expenses->sum('amount');

        $pdf = PDF::loadView('reports.expense', compact('expenses', 'totalExpense', 'user'));

        return $pdf->download('expense_report.pdf');
    }

    public function syncBalance()
    {
        try {
            $user = Auth::user();
            $totalIncome = Income::where('user_id', $user->id)->sum('amount');
            $totalExpense = Expense::where('user_id', $user->id)->sum('amount');
            $calculatedBalance = $totalIncome - $totalExpense;

            $paymentOption = PaymentOption::where('user_id', $user->id)->first();
            if (!$paymentOption) {
                $paymentOption = PaymentOption::create([
                    'user_id' => $user->id,
                    'title' => 'Default Payment Option',
                    'balance' => $calculatedBalance
                ]);
            } else {
                $paymentOption->update([
                    'balance' => $calculatedBalance
                ]);
            }

            Log::info('Balance synced successfully. New balance: ' . $calculatedBalance);

            return response()->json([
                'message' => 'Balance synced successfully',
                'new_balance' => $calculatedBalance
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing balance: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to sync balance', 'error' => $e->getMessage()], 500);
        }
    }
}