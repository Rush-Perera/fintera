@extends('layouts.app')

@section('title', 'Payment Methods | Fintera')

@section('content')
    <section class="page-header glass-card">
        <div>
            <p class="eyebrow">Settings</p>
            <h1>Payment Methods</h1>
            <p class="muted">Create and manage methods used for transactions and transfers.</p>
        </div>
        <div class="quick-links">
            <a href="{{ route('fund-accounts.index') }}" class="btn-ghost">Fund Accounts</a>
            <a href="{{ route('dashboard') }}" class="btn-ghost">Back to Dashboard</a>
        </div>
    </section>

    <section class="dashboard-grid">
        <article class="glass-card">
            <h3>Add New Payment Method</h3>

            <form action="{{ route('payment-methods.store') }}" method="POST" class="stack-md">
                @csrf

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="name">Name</label>
                        <input id="name" type="text" name="name" class="input" value="{{ old('name') }}" required>
                    </div>

                    <div>
                        <label class="field-label" for="type">Type</label>
                        <select id="type" name="type" class="select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_card">Bank Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="ewallet">E-wallet</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="field-label" for="details">Details</label>
                    <input id="details" type="text" name="details" class="input" placeholder="Optional note (e.g., HNB Debit Card)" value="{{ old('details') }}">
                </div>

                <label class="check-row">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Active and selectable in transaction form</span>
                </label>

                <button type="submit" class="btn-primary">Add Method</button>
            </form>
        </article>

        <article class="glass-card">
            <h3>Existing Methods</h3>

            @if ($methods->isEmpty())
                <p class="empty-state">No payment methods yet.</p>
            @else
                <div class="method-list">
                    @foreach ($methods as $method)
                        <div class="method-card">
                            <div class="method-header">
                                <div class="method-info">
                                    <h4 class="method-name">{{ $method->name }}</h4>
                                    <p class="method-type">{{ ['cash' => 'Cash', 'bank_card' => 'Bank Card', 'bank_transfer' => 'Bank Transfer', 'ewallet' => 'E-wallet', 'other' => 'Other'][$method->type] ?? $method->type }}</p>
                                </div>
                                <div class="method-balance">
                                    <div class="balance-label">Balance</div>
                                    <div class="balance-amount">LKR {{ number_format((float) $method->balance, 2) }}</div>
                                </div>
                            </div>

                            @if ($method->details)
                                <p class="method-details">{{ $method->details }}</p>
                            @endif

                            <div class="method-status">
                                <span class="status-badge {{ $method->is_active ? 'active' : 'inactive' }}">
                                    {{ $method->is_active ? '● Active' : '● Inactive' }}
                                </span>
                            </div>

                            <form action="{{ route('payment-methods.update', $method) }}" method="POST" class="method-edit-form">
                                @csrf
                                @method('PUT')

                                <div class="grid-two">
                                    <input type="text" name="name" class="input input-sm" value="{{ $method->name }}" required>
                                    <select name="type" class="select select-sm" required>
                                        @foreach (['cash' => 'Cash', 'bank_card' => 'Bank Card', 'bank_transfer' => 'Bank Transfer', 'ewallet' => 'E-wallet', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}" {{ $method->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="text" name="details" class="input input-sm" value="{{ $method->details }}" placeholder="Details">

                                <div>
                                    <label class="field-label" for="balance-{{ $method->id }}">Balance (LKR)</label>
                                    <input id="balance-{{ $method->id }}" type="number" name="balance" class="input input-sm" value="{{ $method->balance }}" step="0.01" min="0" placeholder="0.00">
                                </div>

                                <label class="check-row">
                                    <input type="checkbox" name="is_active" value="1" {{ $method->is_active ? 'checked' : '' }}>
                                    <span>Active</span>
                                </label>

                                <div class="method-actions">
                                    <button type="submit" class="btn-icon btn-primary" title="Save Changes">
                                        <span class="icon">✓</span>
                                    </button>
                                    <button type="button" class="btn-icon btn-secondary" onclick="this.closest('.method-edit-form').classList.remove('visible')" title="Cancel">
                                        <span class="icon">✕</span>
                                    </button>
                                </div>
                            </form>

                            <div class="method-footer">
                                <button type="button" class="btn-icon-text" onclick="this.closest('.method-card').querySelector('.method-edit-form').classList.toggle('visible')" title="Edit">
                                    <span class="icon">✎</span>
                                </button>
                                <form action="{{ route('payment-methods.destroy', $method) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this payment method?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-text btn-danger" title="Delete">
                                        <span class="icon">🗑</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>

    <style>
        .method-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .method-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .method-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .method-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .method-type {
            margin: 0;
            font-size: 0.875rem;
            color: #7f8c8d;
        }

        .method-balance {
            text-align: right;
        }

        .balance-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #27ae60;
            margin-top: 0.25rem;
        }

        .method-details {
            font-size: 0.875rem;
            color: #555;
            margin: 0.75rem 0;
            font-style: italic;
        }

        .method-status {
            margin-bottom: 1rem;
        }

        .status-badge {
            display: inline-block;
            font-size: 0.8rem;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
        }

        .status-badge.inactive {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }

        .method-edit-form {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            gap: 0.75rem;
        }

        .method-edit-form.visible {
            display: flex;
            flex-direction: column;
        }

        .input-sm,
        .select-sm {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .method-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
            padding: 0;
        }

        .btn-icon.btn-primary {
            background-color: #2ecc71;
            color: white;
        }

        .btn-icon.btn-primary:hover {
            background-color: #27ae60;
        }

        .btn-icon.btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn-icon.btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-icon-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            padding: 0;
            margin-right: 0.5rem;
        }

        .btn-icon-text:hover {
            background: rgba(46, 204, 113, 0.3);
            transform: scale(1.1);
        }

        .btn-icon-text.btn-danger {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .btn-icon-text.btn-danger:hover {
            background: rgba(231, 76, 60, 0.3);
        }

        .method-footer {
            display: flex;
            gap: 0.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .icon {
            display: inline-block;
            font-weight: bold;
        }
    </style>
@endsection
