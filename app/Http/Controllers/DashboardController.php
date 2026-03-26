<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'date' => ['nullable', 'date'],
            'quick_type' => ['nullable', 'in:income,expense'],
            'activity_date_from' => ['nullable', 'date'],
            'activity_date_to' => ['nullable', 'date'],
            'activity_category' => ['nullable', 'integer'],
            'activity_type' => ['nullable', 'in:income,expense'],
        ]);

        $selectedDate = Carbon::parse($request->input('date', now()->toDateString()))->toDateString();
        $selectedMoment = Carbon::parse($selectedDate);

        $user = $request->user();

        $dailyScope = $user->transactions()->whereDate('transacted_on', $selectedDate);

        $dailyIncome = (float) (clone $dailyScope)->where('type', 'income')->sum('amount');
        $dailyExpense = (float) (clone $dailyScope)->where('type', 'expense')->sum('amount');
        $dailyNet = $dailyIncome - $dailyExpense;

        $monthStart = $selectedMoment->copy()->startOfMonth()->toDateString();
        $monthEnd = $selectedMoment->copy()->endOfMonth()->toDateString();
        $monthlyScope = $user->transactions()->whereBetween('transacted_on', [$monthStart, $monthEnd]);

        $monthIncome = (float) (clone $monthlyScope)->where('type', 'income')->sum('amount');
        $monthExpense = (float) (clone $monthlyScope)->where('type', 'expense')->sum('amount');
        $monthNet = $monthIncome - $monthExpense;

        $categorySpend = (clone $monthlyScope)
            ->where('type', 'expense')
            ->selectRaw("COALESCE(category, 'Other') as category, SUM(amount) as total")
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $transactionsForDay = $user->transactions()
            ->with('paymentMethod', 'fundAccount', 'categoryRelation')
            ->whereDate('transacted_on', $selectedDate)
            ->latest('id')
            ->get();

        // Activity filters
        $activityQuery = $user->transactions()->with('paymentMethod', 'fundAccount', 'categoryRelation');
        
        if ($request->filled('activity_date_from')) {
            $activityQuery->whereDate('transacted_on', '>=', $request->input('activity_date_from'));
        }
        
        if ($request->filled('activity_date_to')) {
            $activityQuery->whereDate('transacted_on', '<=', $request->input('activity_date_to'));
        }
        
        if ($request->filled('activity_category')) {
            $activityQuery->where('category_id', $request->input('activity_category'));
        }
        
        if ($request->filled('activity_type')) {
            $activityQuery->where('type', $request->input('activity_type'));
        }

        $incomeTransactions = (clone $activityQuery)->where('type', 'income')->latest('transacted_on')->latest('id')->get();
        $expenseTransactions = (clone $activityQuery)->where('type', 'expense')->latest('transacted_on')->latest('id')->get();

        $recentTransactions = $user->transactions()
            ->with('paymentMethod', 'fundAccount', 'categoryRelation')
            ->latest('transacted_on')
            ->latest('id')
            ->limit(8)
            ->get();

        $paymentMethods = $user->paymentMethods()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = $user->categories()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $fundAccounts = $user->fundAccounts()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $totalFundBalance = (float) $fundAccounts->sum('current_balance');
        $totalMethodBalance = (float) $paymentMethods->sum('balance');
        $quickType = $request->input('quick_type', old('type', 'expense'));

        return view('dashboard.index', [
            'selectedDate' => $selectedDate,
            'quickType' => $quickType,
            'dailyIncome' => $dailyIncome,
            'dailyExpense' => $dailyExpense,
            'dailyNet' => $dailyNet,
            'monthIncome' => $monthIncome,
            'monthExpense' => $monthExpense,
            'monthNet' => $monthNet,
            'categorySpend' => $categorySpend,
            'transactionsForDay' => $transactionsForDay,
            'recentTransactions' => $recentTransactions,
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'paymentMethods' => $paymentMethods,
            'categories' => $categories,
            'fundAccounts' => $fundAccounts,
            'totalFundBalance' => $totalFundBalance,
            'totalMethodBalance' => $totalMethodBalance,
        ]);
    }
}
