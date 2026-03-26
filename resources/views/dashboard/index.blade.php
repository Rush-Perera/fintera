@extends('layouts.app')

@section('title', 'Dashboard | Fintera')

@section('content')
    <section class="glass-card mobile-primary-actions">
        <p class="eyebrow">Quick Add</p>
        <h1>What do you want to add?</h1>
        <div class="quick-grid">
            <a href="{{ route('dashboard', ['date' => $selectedDate, 'quick_type' => 'expense']) }}#txn-form" class="btn-danger quick-btn">+ Add Expense</a>
            <a href="{{ route('dashboard', ['date' => $selectedDate, 'quick_type' => 'income']) }}#txn-form" class="btn-primary quick-btn">+ Add Income</a>
        </div>
        <div class="quick-links">
            <a href="{{ route('payment-methods.index') }}" class="chip-link">Payment Methods</a>
            <a href="{{ route('categories.index') }}" class="chip-link">Categories</a>
            <a href="{{ route('fund-accounts.index') }}" class="chip-link">Fund Accounts</a>
        </div>
    </section>

    <article class="glass-card activity-card-main">
        <div class="activity-header">
            <h3>Income & Expense Activity</h3>
            <a href="{{ route('categories.index') }}" class="chip-link">Manage Categories</a>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="activity-filters-modern">
            <input type="hidden" name="date" value="{{ $selectedDate }}">

            <div class="filter-group">
                <div class="filter-input">
                    <label for="activity_date_from">From</label>
                    <input id="activity_date_from" type="date" name="activity_date_from" class="input input-sm" value="{{ request('activity_date_from') }}">
                </div>

                <div class="filter-input">
                    <label for="activity_date_to">To</label>
                    <input id="activity_date_to" type="date" name="activity_date_to" class="input input-sm" value="{{ request('activity_date_to') }}">
                </div>

                <div class="filter-input">
                    <label for="activity_category">Category</label>
                    <select id="activity_category" name="activity_category" class="select input-sm">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('activity_category') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon ? $category->icon . ' ' : '' }}{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-input">
                    <label for="activity_type">Type</label>
                    <select id="activity_type" name="activity_type" class="select input-sm">
                        <option value="">Both</option>
                        <option value="income" {{ request('activity_type') === 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ request('activity_type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-primary btn-sm">Apply</button>
                    <a href="{{ route('dashboard', ['date' => $selectedDate]) }}" class="btn-ghost btn-sm">Reset</a>
                </div>
            </div>
        </form>

        <div class="activity-summary">
            <div class="summary-item income">
                <div>
                    <p>Income Total</p>
                    <h4>LKR {{ number_format((float) $incomeTransactions->sum('amount'), 2) }}</h4>
                </div>
                <span class="count">{{ $incomeTransactions->count() }} txns</span>
            </div>
            <div class="summary-item expense">
                <div>
                    <p>Expense Total</p>
                    <h4>LKR {{ number_format((float) $expenseTransactions->sum('amount'), 2) }}</h4>
                </div>
                <span class="count">{{ $expenseTransactions->count() }} txns</span>
            </div>
        </div>

        <div class="activity-tabs-modern">
            <div class="tab income-tab">
                <div class="tab-header">
                    <h4>Income Transactions</h4>
                    <span class="tab-count">{{ $incomeTransactions->count() }}</span>
                </div>
                
                @if ($incomeTransactions->isEmpty())
                    <div class="empty-activity">
                        <p>No income transactions</p>
                    </div>
                @else
                    <div class="activity-items">
                        @foreach ($incomeTransactions as $item)
                            <div class="activity-item-new income-item-new">
                                <div class="item-left">
                                    <div class="item-icon-new">{{ $item->categoryRelation?->icon ?? '💵' }}</div>
                                    <div class="item-info">
                                        <h5>{{ $item->remarks ?: ($item->categoryRelation?->name ?: 'No description') }}</h5>
                                        <span class="item-date">
                                            {{ $item->transacted_on->format('M d, Y') }}
                                            @if($item->paymentMethod)
                                                • {{ $item->paymentMethod->name }}
                                            @elseif($item->fundAccount)
                                                • {{ $item->fundAccount->name }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="item-right income-right">
                                    <span class="amount-value">LKR {{ number_format((float) $item->amount, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tab expense-tab">
                <div class="tab-header">
                    <h4>Expense Transactions</h4>
                    <span class="tab-count">{{ $expenseTransactions->count() }}</span>
                </div>
                
                @if ($expenseTransactions->isEmpty())
                    <div class="empty-activity">
                        <p>No expense transactions</p>
                    </div>
                @else
                    <div class="activity-items">
                        @foreach ($expenseTransactions as $item)
                            <div class="activity-item-new expense-item-new">
                                <div class="item-left">
                                    <div class="item-icon-new">{{ $item->categoryRelation?->icon ?? '💳' }}</div>
                                    <div class="item-info">
                                        <h5>{{ $item->remarks ?: ($item->categoryRelation?->name ?: 'No description') }}</h5>
                                        <span class="item-date">
                                            {{ $item->transacted_on->format('M d, Y') }}
                                            @if($item->paymentMethod)
                                                • {{ $item->paymentMethod->name }}
                                            @elseif($item->fundAccount)
                                                • {{ $item->fundAccount->name }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="item-right expense-right">
                                    <span class="amount-value">LKR {{ number_format((float) $item->amount, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <style>
            .activity-card-main {
                padding: 2rem !important;
                background: #ffffff !important;
            }

            .activity-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            }

            .activity-header h3 {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 700;
                color: #2c3e50;
            }

            .activity-filters-modern {
                background: #f9fafb;
                padding: 1.25rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
                border: 1px solid #e5e7eb;
            }

            .filter-group {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 1rem;
                align-items: flex-end;
            }

            .filter-input {
                display: flex;
                flex-direction: column;
                gap: 0.35rem;
            }

            .filter-input label {
                font-size: 0.8rem;
                font-weight: 600;
                color: #5a6c7d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .filter-actions {
                display: flex;
                gap: 0.5rem;
            }

            .activity-summary {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                margin-bottom: 2rem;
            }

            .summary-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0.5rem;
                padding: 1rem;
                border-radius: 10px;
                background: #ffffff;
                border: 1px solid #e6e7eb;
                transition: all 0.3s ease;
            }

            .summary-item.income,
            .summary-item.expense {
                border-color: #e6e7eb;
            }

            .summary-item p {
                margin: 0;
                font-size: 0.8rem;
                color: #7f8c8d;
                font-weight: 500;
            }

            .summary-item h4 {
                margin: 0.25rem 0 0 0;
                font-size: 1.3rem;
                font-weight: 700;
            }

            .summary-item.income h4 {
                color: #1f7a4d;
            }

            .summary-item.expense h4 {
                color: #c64747;
            }

            .summary-item .count {
                font-size: 0.75rem;
                color: #95a5a6;
                white-space: nowrap;
            }

            .activity-tabs-modern {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .tab {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .tab-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-bottom: 0.75rem;
                border-bottom: 2px solid rgba(0, 0, 0, 0.08);
            }

            .tab-header h4 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 600;
            }

            .tab-count {
                background: rgba(0, 0, 0, 0.05);
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                color: #2c3e50;
            }

            .empty-activity {
                text-align: center;
                padding: 2rem 1rem;
                color: #95a5a6;
                font-size: 0.95rem;
            }

            .activity-items {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                max-height: 650px;
                overflow-y: auto;
                padding-right: 0.5rem;
            }

            .activity-items::-webkit-scrollbar {
                width: 6px;
            }

            .activity-items::-webkit-scrollbar-track {
                background: transparent;
            }

            .activity-items::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .activity-items::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.2);
            }

            .activity-item-new {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                padding: 1rem;
                border-radius: 10px;
                background: #fff;
                border: 1px solid #eceff2;
                transition: all 0.25s ease;
            }

            .activity-item-new:hover {
                background: #f9f9f9;
                border-color: #e0e0e0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            }

            .income-item-new {
                border-top: 3px solid #e6e7eb;
            }

            .expense-item-new {
                border-top: 3px solid #e6e7eb;
            }

            .item-left {
                display: flex;
                align-items: center;
                gap: 0.9rem;
                flex: 1;
                min-width: 0;
            }

            .item-icon-new {
                font-size: 1.6rem;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                background: #f4f5f7;
                border-radius: 8px;
                flex-shrink: 0;
            }

            .item-info {
                flex: 1;
                min-width: 0;
            }

            .item-info h5 {
                margin: 0 0 0.2rem 0;
                font-size: 0.95rem;
                font-weight: 600;
                color: #2c3e50;
                line-height: 1.3;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .item-date {
                font-size: 0.8rem;
                color: #95a5a6;
                display: block;
            }

            .item-right {
                text-align: right;
                flex-shrink: 0;
                white-space: nowrap;
            }

            .amount-value {
                font-size: 1rem;
                font-weight: 700;
                letter-spacing: -0.25px;
                display: block;
            }

            .income-right .amount-value {
                color: #27ae60;
            }

            .expense-right .amount-value {
                color: #e74c3c;
            }

            @media (max-width: 768px) {
                .activity-tabs-modern {
                    grid-template-columns: 1fr;
                }

                .activity-summary {
                    grid-template-columns: 1fr;
                }

                .filter-group {
                    grid-template-columns: 1fr;
                }

                .activity-card-main {
                    padding: 1.5rem !important;
                }
            }
        </style>
    </article>

    <section class="page-header glass-card">
        <div>
            <p class="eyebrow">Dashboard</p>
            <h1>Daily Finance</h1>
            <p class="muted">Pick a date and capture your transactions quickly.</p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}" class="date-filter">
            <label class="field-label" for="date">Selected date</label>
            <input id="date" type="date" name="date" class="input" value="{{ $selectedDate }}">
            <button class="btn-primary" type="submit">Apply</button>
        </form>
    </section>

    <section id="txn-form" class="glass-card">
        <div class="card-head">
            <h3>Add Income or Expense</h3>
            <div class="quick-links">
                <a href="{{ route('categories.index') }}" class="chip-link">Manage categories</a>
                <a href="{{ route('payment-methods.index') }}" class="chip-link">Manage payment methods</a>
                <a href="{{ route('fund-accounts.index') }}" class="chip-link">Manage funds</a>
            </div>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="stack-md">
            @csrf

            <div class="grid-two">
                <div>
                    <label class="field-label" for="type">Type</label>
                    <select id="type" name="type" class="select" required>
                        <option value="income" {{ old('type', $quickType) === 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ old('type', $quickType) === 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>

                <div>
                    <label class="field-label" for="transacted_on">Date</label>
                    <input id="transacted_on" name="transacted_on" type="date" class="input" value="{{ old('transacted_on', $selectedDate) }}" required>
                </div>
            </div>

            <div class="grid-two">
                <div>
                    <label class="field-label" for="amount">Amount</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" class="input" value="{{ old('amount') }}" required>
                </div>

                <div>
                    <label class="field-label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="select">
                        <option value="">Select a category (optional)</option>
                        @php
                            $typeValue = old('type', $quickType);
                        @endphp
                        @foreach ($categories->where('type', $typeValue === 'income' ? 'income' : 'expense') as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon ? $category->icon . ' ' : '' }}{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="field-label" for="payment_method_id">Payment Method</label>
                <select id="payment_method_id" name="payment_method_id" class="select">
                    <option value="">Select a payment method</option>
                    @foreach ($paymentMethods as $method)
                        <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                            {{ $method->name }} | Balance: LKR {{ number_format((float) $method->balance, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label" for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks" class="textarea" rows="3" placeholder="Optional note about this transaction">{{ old('remarks') }}</textarea>
            </div>

            <div>
                <label class="field-label" for="payslip">Payslip / Receipt</label>
                <input id="payslip" name="payslip" type="file" class="input" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit" class="btn-primary">Save Transaction</button>
        </form>
    </section>

    <section class="stats-grid">
        <article class="glass-card stat-card">
            <p class="muted">Income (Day)</p>
            <h2 class="text-income">LKR {{ number_format($dailyIncome, 2) }}</h2>
        </article>
        <article class="glass-card stat-card">
            <p class="muted">Expense (Day)</p>
            <h2 class="text-expense">LKR {{ number_format($dailyExpense, 2) }}</h2>
        </article>
        <article class="glass-card stat-card">
            <p class="muted">Net (Day)</p>
            <h2 class="{{ $dailyNet >= 0 ? 'text-income' : 'text-expense' }}">LKR {{ number_format($dailyNet, 2) }}</h2>
        </article>
        <article class="glass-card stat-card">
            <p class="muted">Net (Month)</p>
            <h2 class="{{ $monthNet >= 0 ? 'text-income' : 'text-expense' }}">LKR {{ number_format($monthNet, 2) }}</h2>
            <small class="muted">Income: {{ number_format($monthIncome, 2) }} | Expense: {{ number_format($monthExpense, 2) }}</small>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="glass-card">
            <h3>Fund Positions</h3>
            <ul class="metric-list">
                <li>
                    <span>Fund Accounts Total</span>
                    <strong>LKR {{ number_format($totalFundBalance, 2) }}</strong>
                </li>
                <li>
                    <span>Payment Methods Total</span>
                    <strong>LKR {{ number_format($totalMethodBalance, 2) }}</strong>
                </li>
                <li>
                    <span>Active Fund Accounts</span>
                    <strong>{{ $fundAccounts->count() }}</strong>
                </li>
            </ul>

            <h3 class="section-gap">Top Monthly Expense Categories</h3>
            @if ($categorySpend->isEmpty())
                <p class="empty-state">No expense data yet for this month.</p>
            @else
                <ul class="metric-list">
                    @foreach ($categorySpend as $category)
                        <li>
                            <span>{{ $category->category }}</span>
                            <strong>LKR {{ number_format((float) $category->total, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>

    <section class="glass-card">
        <h3>Transactions for {{ $selectedDate }}</h3>

        @if ($transactionsForDay->isEmpty())
            <p class="empty-state">No transactions on this date.</p>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Payment</th>
                            <th>Remarks</th>
                            <th>Amount</th>
                            <th>Payslip</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactionsForDay as $transaction)
                            <tr>
                                <td>{{ ucfirst($transaction->type) }}</td>
                                <td>{{ $transaction->categoryRelation?->name ?? $transaction->category ?? 'Other' }}</td>
                                <td>{{ $transaction->paymentMethod?->name ?? 'N/A' }}</td>
                                <td><strong>{{ $transaction->remarks ?: 'No description' }}</strong></td>
                                <td>{{ $transaction->type === 'income' ? '+' : '-' }} LKR {{ number_format((float) $transaction->amount, 2) }}</td>
                                <td>
                                    @if ($transaction->payslip_path)
                                        <a href="{{ asset('storage/' . $transaction->payslip_path) }}" target="_blank">View</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
