<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Income;
use Carbon\Carbon;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('home', function ($view) {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfMonth();

            $incomes = Income::whereBetween('date', [$startDate, $endDate])
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            $labels = [];
            $incomeData = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
                $labels[] = $date->format('M Y');
                $incomeData[] = $incomes->where('year', $date->year)->where('month', $date->month)->first()->total ?? 0;
            }

            $view->with('monthlyIncomeSummary', [
                'labels' => $labels,
                'incomeData' => $incomeData,
            ]);
        });
    }

    public function register()
    {
        //
    }
}