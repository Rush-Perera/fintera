@extends('layouts.app')

@section('title', 'Fund Accounts | Fintera')

@section('content')
    <section class="page-header glass-card">
        <div>
            <p class="eyebrow">Funds</p>
            <h1>Fund Accounts</h1>
            <p class="muted">Manage your bank accounts and transfer funds into payment methods.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-ghost">Back to Dashboard</a>
    </section>

    <section class="dashboard-grid">
        <article class="glass-card">
            <h3>Add Fund Account</h3>

            <form action="{{ route('fund-accounts.store') }}" method="POST" class="stack-md">
                @csrf

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="name">Account Name</label>
                        <input id="name" type="text" name="name" class="input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="bank_name">Bank Name</label>
                        <input id="bank_name" type="text" name="bank_name" class="input" value="{{ old('bank_name') }}">
                    </div>
                </div>

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="account_number">Account Number</label>
                        <input id="account_number" type="text" name="account_number" class="input" value="{{ old('account_number') }}">
                    </div>
                    <div>
                        <label class="field-label" for="current_balance">Current Balance (LKR)</label>
                        <input id="current_balance" type="number" step="0.01" min="0" name="current_balance" class="input" value="{{ old('current_balance', 0) }}" required>
                    </div>
                </div>

                <label class="check-row">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Active and available for transfer</span>
                </label>

                <button type="submit" class="btn-primary">Add Account</button>
            </form>
        </article>

        <article class="glass-card">
            <h3>Transfer To Payment Method</h3>

            <form action="{{ route('fund-transfers.store') }}" method="POST" class="stack-md">
                @csrf

                <div>
                    <label class="field-label" for="fund_account_id">From Fund Account</label>
                    <select id="fund_account_id" name="fund_account_id" class="select" required>
                        <option value="">Select account</option>
                        @foreach ($accounts->where('is_active', true) as $account)
                            <option value="{{ $account->id }}" {{ old('fund_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} (LKR {{ number_format((float) $account->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label" for="payment_method_id">To Payment Method</label>
                    <select id="payment_method_id" name="payment_method_id" class="select" required>
                        <option value="">Select method</option>
                        @foreach ($paymentMethods as $method)
                            <option value="{{ $method->id }}" {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                {{ $method->name }} (Balance: LKR {{ number_format((float) $method->balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="amount">Amount (LKR)</label>
                        <input id="amount" type="number" name="amount" min="0.01" step="0.01" class="input" value="{{ old('amount') }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="transferred_on">Transfer Date</label>
                        <input id="transferred_on" type="date" name="transferred_on" class="input" value="{{ old('transferred_on', now()->toDateString()) }}" required>
                    </div>
                </div>

                <div>
                    <label class="field-label" for="remarks">Remarks</label>
                    <input id="remarks" type="text" name="remarks" class="input" value="{{ old('remarks') }}" placeholder="Optional transfer note">
                </div>

                <button type="submit" class="btn-primary">Transfer Funds</button>
            </form>
        </article>

        <article class="glass-card">
            <h3>Transfer Between Fund Accounts</h3>

            <form action="{{ route('fund-transfers.between-accounts') }}" method="POST" class="stack-md">
                @csrf

                <div>
                    <label class="field-label" for="source_fund_account_id">From Fund Account</label>
                    <select id="source_fund_account_id" name="source_fund_account_id" class="select" required>
                        <option value="">Select source account</option>
                        @foreach ($accounts->where('is_active', true) as $account)
                            <option value="{{ $account->id }}" {{ old('source_fund_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} (LKR {{ number_format((float) $account->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label" for="destination_fund_account_id">To Fund Account</label>
                    <select id="destination_fund_account_id" name="destination_fund_account_id" class="select" required>
                        <option value="">Select destination account</option>
                        @foreach ($accounts->where('is_active', true) as $account)
                            <option value="{{ $account->id }}" {{ old('destination_fund_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} (LKR {{ number_format((float) $account->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="transfer_amount">Amount (LKR)</label>
                        <input id="transfer_amount" type="number" name="amount" min="0.01" step="0.01" class="input" value="{{ old('amount') }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="transfer_date">Transfer Date</label>
                        <input id="transfer_date" type="date" name="transferred_on" class="input" value="{{ old('transferred_on', now()->toDateString()) }}" required>
                    </div>
                </div>

                <div>
                    <label class="field-label" for="transfer_remarks">Remarks</label>
                    <input id="transfer_remarks" type="text" name="remarks" class="input" value="{{ old('remarks') }}" placeholder="Optional transfer note">
                </div>

                <button type="submit" class="btn-primary">Transfer Between Accounts</button>
            </form>
        </article>

        <article class="glass-card">
            <h3>Add Income / Expense</h3>
            <p class="muted">Adjust fund account balance directly with income or expense entries.</p>

            <form action="{{ route('transactions.store') }}" method="POST" class="stack-md">
                @csrf

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="fa_type">Type</label>
                        <select id="fa_type" name="type" class="select" required>
                            <option value="">Select type</option>
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>+ Income</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>- Expense</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label" for="fa_fund_account_id">Fund Account</label>
                        <select id="fa_fund_account_id" name="fund_account_id" class="select" required>
                            <option value="">Select fund account</option>
                            @foreach ($accounts->where('is_active', true) as $account)
                                <option value="{{ $account->id }}" {{ old('fund_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} (LKR {{ number_format((float) $account->current_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="fa_amount">Amount (LKR)</label>
                        <input id="fa_amount" type="number" name="amount" min="0.01" step="0.01" class="input" value="{{ old('amount') }}" required>
                    </div>
                    <div>
                        <label class="field-label" for="fa_transacted_on">Date</label>
                        <input id="fa_transacted_on" type="date" name="transacted_on" class="input" value="{{ old('transacted_on', now()->toDateString()) }}" required>
                    </div>
                </div>

                <div>
                    <label class="field-label" for="fa_category_id">Category (Optional)</label>
                    <select id="fa_category_id" name="category_id" class="select">
                        <option value="">No category</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon ? $category->icon . ' ' : '' }}{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label" for="fa_remarks">Remarks</label>
                    <input id="fa_remarks" type="text" name="remarks" class="input" value="{{ old('remarks') }}" placeholder="Optional note">
                </div>

                <button type="submit" class="btn-primary">Record Transaction</button>
            </form>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="glass-card">
            <h3>Fund Accounts</h3>

            @if ($accounts->isEmpty())
                <p class="empty-state">No fund accounts yet.</p>
            @else
                <div class="method-list">
                    @foreach ($accounts as $account)
                        <div class="method-item">
                            <form action="{{ route('fund-accounts.update', $account) }}" method="POST" class="stack-sm">
                                @csrf
                                @method('PUT')

                                <div class="grid-two">
                                    <input type="text" name="name" class="input" value="{{ $account->name }}" required>
                                    <input type="text" name="bank_name" class="input" value="{{ $account->bank_name }}" placeholder="Bank name">
                                </div>

                                <div class="grid-two">
                                    <input type="text" name="account_number" class="input" value="{{ $account->account_number }}" placeholder="Account number">
                                    <input type="number" name="current_balance" min="0" step="0.01" class="input" value="{{ number_format((float) $account->current_balance, 2, '.', '') }}" required>
                                </div>

                                <label class="check-row">
                                    <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }}>
                                    <span>Active</span>
                                </label>

                                <div class="inline-actions">
                                    <button type="submit" class="btn-primary">Update</button>
                                </div>
                            </form>

                            <form action="{{ route('fund-accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Delete this fund account?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="glass-card">
            <h3>Recent Transactions</h3>

            @php
                $recentTransactions = [];
                foreach ($accounts as $account) {
                    foreach ($account->transactions()->latest('transacted_on')->latest('id')->limit(20)->get() as $txn) {
                        $recentTransactions[] = $txn;
                    }
                }
                usort($recentTransactions, fn($a, $b) => $b->transacted_on <=> $a->transacted_on);
                $recentTransactions = array_slice($recentTransactions, 0, 15);
            @endphp

            @if (empty($recentTransactions))
                <p class="empty-state">No fund account transactions yet.</p>
            @else
                <ul class="transfer-list">
                    @foreach ($recentTransactions as $txn)
                        <li class="transfer-item">
                            <div class="transfer-info">
                                <strong>
                                    <span class="txn-badge {{ $txn->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                                        {{ $txn->type === 'income' ? '+ Income' : '- Expense' }}
                                    </span>
                                    LKR {{ number_format((float) $txn->amount, 2) }}
                                </strong>
                                <small>
                                    {{ $txn->fundAccount?->name ?? '-' }}
                                    {{ $txn->remarks ? "• {$txn->remarks}" : '' }}
                                    | {{ $txn->transacted_on->format('Y-m-d') }}
                                </small>
                            </div>
                            <form action="{{ route('transactions.destroy', $txn) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 0.85em;" onclick="return confirm('Delete this transaction?')">Delete</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
            @if ($recentTransfers->isEmpty())
                <p class="empty-state">No transfers yet.</p>
            @else
                <ul class="activity-list">
                    @foreach ($recentTransfers as $transfer)
                        <li>
                            <div>
                                <strong>LKR {{ number_format((float) $transfer->amount, 2) }}</strong>
                                <small>
                                    {{ $transfer->fundAccount?->name ?? '-' }} to
                                    @if ($transfer->destinationFundAccount)
                                        {{ $transfer->destinationFundAccount->name }} (Account Transfer)
                                    @else
                                        {{ $transfer->paymentMethod?->name ?? '-' }} (Payment Method)
                                    @endif
                                    | {{ $transfer->transferred_on->format('Y-m-d') }}
                                </small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>

    <style>
        .txn-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        .badge-income {
            background-color: #d4f4dd;
            color: #27ae60;
        }

        .badge-expense {
            background-color: #fadcd9;
            color: #e74c3c;
        }

        .transfer-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .transfer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .transfer-item:last-child {
            border-bottom: none;
        }

        .transfer-info {
            flex: 1;
        }

        .transfer-info strong {
            display: block;
            margin-bottom: 0.25rem;
        }

        .transfer-info small {
            color: #666;
            font-size: 0.85em;
        }
    </style>
@endsection
