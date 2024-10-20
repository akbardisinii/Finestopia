<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Allocation;
use Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class IncomeController extends Controller
{
       
    public function downloadMonthlyReport($yearMonth)
{
    \Log::info('Downloading report for: ' . $yearMonth);

    list($year, $month) = explode('-', $yearMonth);

    $user = Auth::user();
    if (!$user) {
        \Log::error('User not authenticated');
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $incomes = Income::where('user_id', $user->id)
                     ->whereYear('date', $year)
                     ->whereMonth('date', $month)
                     ->orderBy('date', 'desc')  // Order by date in descending order
                     ->orderBy('id', 'desc')    // If dates are the same, order by id in descending order
                     ->get()
                     ->map(function ($income) {
                         $income->formatted_date = Carbon::parse($income->date)->format('d-m-Y');
                         return $income;
                     });

    \Log::info('Fetched incomes: ' . $incomes->count() . ' for user: ' . $user->id);

    $totalIncome = $incomes->sum('amount');

    $pdf = PDF::loadView('reports.monthly_income', [
        'incomes' => $incomes,
        'totalIncome' => $totalIncome,
        'year' => $year,
        'month' => $month,
        'user' => $user
    ]);

    $formattedDate = Carbon::create($year, $month, 1)->locale('id')->isoFormat('D MMMM YYYY');

    $filename = "Laporan_Pemasukan_{$formattedDate}.pdf";

    \Log::info('Generating PDF: ' . $filename);

    return $pdf->download($filename);
}
    public function getMonthlySummary($yearMonth)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Parse the year and month from the input
    $date = Carbon::createFromFormat('Y-m', $yearMonth);
    $year = $date->year;
    $month = $date->month;

    // Query to get daily income totals for the specified month and user
    $dailyIncomes = Income::where('user_id', $user->id)
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(amount) as total'))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Prepare data for the chart
    $labels = [];
    $incomes = [];
    $currentDate = $date->copy()->startOfMonth();
    $endDate = $date->copy()->endOfMonth();

    while ($currentDate <= $endDate) {
        $labels[] = $currentDate->format('d M');
        $dayIncome = $dailyIncomes->firstWhere('date', $currentDate->toDateString());
        $incomes[] = $dayIncome ? $dayIncome->total : 0;
        $currentDate->addDay();
    }

    return response()->json([
        'labels' => $labels,
        'incomes' => $incomes
    ]);
}
    public function store(Request $request)
    {
        Log::info('Income store method called', $request->all());
        try {
            // Validate request data
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0',
                'title' => 'required|string|max:255',
                'date' => 'required|date',
                'category' => 'nullable|string|max:255',
            ]);

            // Create new Income entry
            $income = Income::create([
                'user_id' => Auth::id(),
                'amount' => $validated['amount'],
                'title' => $validated['title'],
                'date' => $validated['date'],
                'category' => $validated['category'] ?? null,
            ]);

            // Update allocation
            $this->updateAllocation(Auth::user(), $validated['amount']);

            return response()->json([
                'message' => 'Income saved successfully',
                'income' => $income,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in income store: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTotal()
    {
        $totalIncome = Income::where('user_id', Auth::id())->sum('amount');
        return response()->json(['totalIncome' => $totalIncome]);
    }

    public function getAllocation()
    {
        Log::info('getAllocation method called');
        try {
            $user = Auth::user();
            $totalIncome = Income::where('user_id', $user->id)->sum('amount');

            $allocation = Allocation::firstOrCreate(
                ['user_id' => $user->id],
                $this->calculateAllocation($totalIncome)
            );

            $percentages = $allocation->getPercentages();

            $result = [
                'totalIncome' => $totalIncome,
                'allocations' => [
                    'primary' => ['percentage' => $percentages['primary'], 'amount' => $allocation->primary],
                    'secondary' => ['percentage' => $percentages['secondary'], 'amount' => $allocation->secondary],
                    'investment' => ['percentage' => $percentages['investment'], 'amount' => $allocation->investment],
                    'debt' => ['percentage' => $percentages['debt'], 'amount' => $allocation->debt],
                ],
            ];
            Log::info('Returning allocation data', $result);
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in getAllocation: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function updateAllocation($user, $amount)
{
    $allocation = Allocation::firstOrNew(['user_id' => $user->id]);
    $newTotal = ($allocation->total ?? 0) + $amount;
    $newAllocation = $this->calculateAllocation($newTotal);
    
    $allocation->fill($newAllocation);
    $allocation->total = $newTotal;
    $allocation->save();
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
}