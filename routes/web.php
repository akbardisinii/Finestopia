<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AjaxDashboardController,
    TestimonialController,
    BalanceController,
    BudgetController,
    CategoryController,
    CurrentBudgetController,
    HomeController,
    PaymentOptionController,
    TransactionController,
    IncomeController,
    FinancialController,
    SalaryController,
    ExpenseController,
    AllocationController,
    AllocationCalculatorController
};
use App\Http\Controllers\Auth\RegisterController;
use App\Models\Debt;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Testimonial;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $testimonials = \App\Models\Testimonial::latest()->take(6)->get();
    return view('welcome', compact('testimonials'));
})->name('welcome');

Route::get('/book', function(){
    return view('book');
});

Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');


Route::get('/proxy-ai', function (Request $request) {
    $prompt = strtolower($request->query('prompt'));
    $response = Http::get("https://api.riskimivan.my.id/api/ai?prompt=" . urlencode($prompt));
    return $response->body();
});

Route::view('template', '/template');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Auth::routes(['register' => true]);

Route::middleware('auth')->group(function () {
    Route::prefix('/api')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/kalkulator', [AllocationCalculatorController::class, 'index'])->name('allocation.calculator');
        Route::get('/balances/index', [BalanceController::class, 'index']);
        Route::get('/allocation/pdf', [AllocationController::class, 'downloadPdf'])->name('allocation.pdf');
        Route::get('/download-expense-report', [BalanceController::class, 'downloadExpenseReport'])->name('download.expense.report');
        Route::get('/income/monthly-report/{yearMonth}', [IncomeController::class, 'downloadMonthlyReport']);
        Route::get('/expense/total', [ExpenseController::class, 'getTotal']);

        Route::middleware('ajax')->group(function () {
        Route::get('/balance/index', [BalanceController::class, 'index'])->name('balance.index');
        Route::middleware('auth:sanctum')->get('/allocation', [IncomeController::class, 'getAllocation']);

        // Expense routes
        Route::get('/expenses', [ExpenseController::class, 'index']);
        // Route::get('/expense/total', 'ExpenseController@getTotal');
        Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
        Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
        Route::middleware('auth:sanctum')->get('/income/monthly-summary/{yearMonth}', [IncomeController::class, 'getMonthlySummary']);
        Route::get('/balance', [App\Http\Controllers\HomeController::class, 'getBalance']);
        Route::get('/api/balances/paginate', [BalanceController::class, 'paginate'])->name('balances.paginate');

        Route::get('/financial-summary', [FinancialController::class, 'getFinancialSummary']);
        Route::get('/balances', [BalanceController::class, 'index'])->name('balances.index');
        Route::get('/balances-test', function() {
            return 'Balances Test Route';
        })->name('balances.test');
        // Income routes
        Route::post('/income', [IncomeController::class, 'store'])->name('income.store');
        Route::get('/income/total', [IncomeController::class, 'getTotal'])->name('income.total');
        Route::get('/financial-allocation', [FinancialController::class, 'getFinancialAllocation'])->name('financial.allocation');
        Route::post('/salary', [SalaryController::class, 'store'])->middleware('auth:sanctum');
        Route::get('/latest-allocation', [FinancialController::class, 'getLatestAllocation']);
        Route::get('/allocation', [IncomeController::class, 'getAllocation']);
        Route::post('/allocation/update', [AllocationController::class, 'updateAllocation']);
        Route::post('/allocation/update', [AllocationController::class, 'updateAllocation']);
        Route::get('/balances', [BalanceController::class, 'apiIndex']);
        Route::get('/total-balance', [BalanceController::class, 'getTotalBalance']);
        Route::get('/total-income', [IncomeController::class, 'getTotal']);
        Route::get('/total-expense', [ExpenseController::class, 'getTotal']);
        //Ajax and Dashboard Controller
        Route::get('balances/current', [AjaxDashboardController::class, 'currentBalances']);
        Route::get('budgets/current/name', [AjaxDashboardController::class, 'getCurrentBudget']);
        Route::get('budgets/current/amount', [AjaxDashboardController::class, 'currentBudgetAmount']);
        Route::get('budgets/previous', [AjaxDashboardController::class, 'previousBudgets']);
        Route::get('dashboard/general-info', [AjaxDashboardController::class, 'generalInfo']);
        Route::get('transactions/ten-income', [AjaxDashboardController::class, 'tenIncomes']);
        Route::get('transactions/ten-expense', [AjaxDashboardController::class, 'tenExpense']);

        //Balance Controller
        Route::get('balances/show/{id}', [BalanceController::class, 'show']);
        Route::get('balances/add-balance/{id}', [BalanceController::class, 'edit']);
        Route::put('balances/add-balance/{id}', [BalanceController::class, 'update']);

        //Budget and CurrentBudgetController Controller
        Route::get('budgets/index', [BudgetController::class, 'index']);
        Route::get('budgets/current', [CurrentBudgetController::class, 'index']);
        Route::get('budgets/show/{id}', [BudgetController::class, 'show']);
        Route::get('budgets/create', [BudgetController::class, 'create']);
        Route::post('budgets/store', [BudgetController::class, 'store']);

        //Category Controller
        Route::get('categories/index', [CategoryController::class, 'index']);
        Route::get('categories/show/{id}', [CategoryController::class, 'show']);
        Route::get('categories/create', [CategoryController::class, 'create']);
        Route::post('categories/store', [CategoryController::class, 'store']);
        Route::get('categories/edit/{id}', [CategoryController::class, 'edit']);
        Route::put('categories/update/{id}', [CategoryController::class, 'update']);
        Route::delete('categories/delete/{id}', [CategoryController::class, 'destroy']);

        //PaymentOption Controller
        Route::get('payment-options/index', [PaymentOptionController::class, 'index']);
        Route::get('payment-options/amount/{id}', [PaymentOptionController::class, 'amount']);
        Route::get('payment-options/show/{id}', [PaymentOptionController::class, 'show']);
        Route::get('payment-options/create', [PaymentOptionController::class, 'create']);
        Route::post('payment-options/store', [PaymentOptionController::class, 'store']);
        Route::get('payment-options/edit/{id}', [PaymentOptionController::class, 'edit']);
        Route::put('payment-options/update/{id}', [PaymentOptionController::class, 'update']);
        Route::delete('payment-options/delete/{id}', [PaymentOptionController::class, 'destroy']);

        //Transaction Controller
        Route::get('transactions/index', [TransactionController::class, 'index']);
        Route::get('transactions/show/{id}', [TransactionController::class, 'show']);
        Route::get('transactions/create', [TransactionController::class, 'create']);
        Route::post('transactions/store', [TransactionController::class, 'store']);
        });
    });

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});
Auth::routes(['logout' => false]);
require __DIR__.'/prezet.php';