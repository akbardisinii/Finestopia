<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use App\Models\Allocation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\HomeController;

class AllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $allocation = new Allocation();
            $allocation->user_id = auth()->id();
            $allocation->date = $request->date;
            $allocation->primary_percentage = $request->primary_percentage;
            $allocation->secondary_percentage = $request->secondary_percentage;
            $allocation->investment_percentage = $request->investment_percentage;
            $allocation->debt_percentage = $request->debt_percentage;
            $allocation->save();

            return response()->json([
                'success' => true,
                'message' => 'Allocation stored successfully',
                'data' => $allocation
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store allocation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to store allocation: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf()
    {
        try {
            $user = Auth::user();
            $homeController = new HomeController();
            
            $totalIncome = Income::where('user_id', $user->id)->sum('amount') ?? 0;
            $totalExpense = Expense::where('user_id', $user->id)->sum('amount') ?? 0;
            $balance = $totalIncome - $totalExpense;
            
            $allocation = $homeController->calculateAllocation($balance);

            // Pastikan view exists
            if (!view()->exists('reports.allocation')) {
                throw new \Exception('PDF template not found');
            }

            $pdf = PDF::loadView('reports.allocation', [
                'user' => $user,
                'allocation' => $allocation
            ]);

            $pdf->setPaper('A4', 'portrait');

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="financial_allocation.pdf"',
            ]);

        } catch (\Exception $e) {
            Log::error('PDF Generation Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Gagal menghasilkan PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadHistoryPdf(Request $request)
{
    try {
        \Log::info('Starting PDF generation with data:', $request->all());

        // Validasi input ye bukan validasi hati seseorang:)
        $validated = $request->validate([
            'date' => 'required|date',
            'total' => 'required|numeric',
            'primary_percentage' => 'required|numeric',
            'secondary_percentage' => 'required|numeric',
            'investment_percentage' => 'required|numeric',
            'debt_percentage' => 'required|numeric',
        ]);

        // Hitung nominal
        $data = [
            'date' => $validated['date'],
            'total' => $validated['total'],
            'primary_percentage' => $validated['primary_percentage'],
            'secondary_percentage' => $validated['secondary_percentage'],
            'investment_percentage' => $validated['investment_percentage'],
            'debt_percentage' => $validated['debt_percentage'],
            'primary_amount' => floor($validated['total'] * $validated['primary_percentage'] / 100),
            'secondary_amount' => floor($validated['total'] * $validated['secondary_percentage'] / 100),
            'investment_amount' => floor($validated['total'] * $validated['investment_percentage'] / 100),
            'debt_amount' => floor($validated['total'] * $validated['debt_percentage'] / 100),
        ];

        // Cek file
        $viewPath = resource_path('views/reports/allocation_history.blade.php');
        if (!file_exists($viewPath)) {
            throw new \Exception("Template PDF tidak ditemukan di: {$viewPath}");
        }

        // Generate PDF
        $pdf = PDF::loadView('reports.allocation_history', $data);
        $pdf->setPaper('A4', 'portrait');

        // Set filename
        $filename = 'alokasi_keuangan_' . date('Y-m-d', strtotime($validated['date'])) . '.pdf';

        return $pdf->download($filename);

    } catch (\Exception $e) {
        \Log::error('PDF Generation Error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => true,
            'message' => 'Gagal menghasilkan PDF: ' . $e->getMessage()
        ], 500);
    }
}
}