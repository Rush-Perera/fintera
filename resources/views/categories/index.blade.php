@extends('layouts.app')

@section('title', 'Categories | Fintera')

@section('content')
    <section class="page-header glass-card">
        <div>
            <p class="eyebrow">Settings</p>
            <h1>Categories</h1>
            <p class="muted">Create and manage income and expense categories for better tracking.</p>
        </div>
        <div class="quick-links">
            <a href="{{ route('dashboard') }}" class="btn-ghost">Dashboard</a>
        </div>
    </section>

    <section class="dashboard-grid">
        <article class="glass-card">
            <h3>Add New Category</h3>

            <form action="{{ route('categories.store') }}" method="POST" class="stack-md">
                @csrf

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="type">Type</label>
                        <select id="type" name="type" class="select" required>
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                        </select>
                    </div>

                    <div>
                        <label class="field-label" for="name">Name</label>
                        <input id="name" type="text" name="name" class="input" value="{{ old('name') }}" placeholder="e.g., Food, Salary, Transport" required>
                    </div>
                </div>

                <div class="grid-two">
                    <div>
                        <label class="field-label" for="icon">Icon (1-3 chars)</label>
                        <input id="icon" type="text" name="icon" class="input" value="{{ old('icon') }}" placeholder="e.g., 🍔, 💼, 🚕" maxlength="3">
                    </div>

                    <div>
                        <label class="field-label" for="color">Color</label>
                        <input id="color" type="color" name="color" class="input" value="{{ old('color', '#3498db') }}">
                    </div>
                </div>

                <button type="submit" class="btn-primary">Add Category</button>
            </form>
        </article>

        <article class="glass-card">
            <h3>Income Categories</h3>

            @if ($incomeCategories->isEmpty())
                <p class="empty-state">No income categories yet.</p>
            @else
                <div class="category-list">
                    @foreach ($incomeCategories as $category)
                        <div class="category-card" style="border-left: 4px solid {{ $category->color ?? '#2ecc71' }}">
                            <div class="category-header">
                                <div class="category-display">
                                    <span class="category-icon" style="color: {{ $category->color ?? '#2ecc71' }}">{{ $category->icon ?? '📊' }}</span>
                                    <div>
                                        <h4>{{ $category->name }}</h4>
                                        <p class="category-type">Income</p>
                                    </div>
                                </div>
                                <span class="category-badge income">Income</span>
                            </div>

                            <form action="{{ route('categories.update', $category) }}" method="POST" class="category-edit-form">
                                @csrf
                                @method('PUT')

                                <div class="grid-two">
                                    <input type="text" name="name" class="input input-sm" value="{{ $category->name }}" required>
                                    <input type="text" name="icon" class="input input-sm" value="{{ $category->icon }}" placeholder="Icon" maxlength="3">
                                </div>

                                <input type="color" name="color" class="input input-sm" value="{{ $category->color ?? '#2ecc71' }}">

                                <div class="category-actions">
                                    <button type="submit" class="btn-icon btn-primary" title="Save">✓</button>
                                    <button type="button" class="btn-icon btn-secondary" onclick="this.closest('.category-edit-form').classList.remove('visible')" title="Cancel">✕</button>
                                </div>
                            </form>

                            <div class="category-footer">
                                <button type="button" class="btn-icon-text" onclick="this.closest('.category-card').querySelector('.category-edit-form').classList.add('visible')" title="Edit">✎</button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-text btn-danger" title="Delete">🗑</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>

        <article class="glass-card">
            <h3>Expense Categories</h3>

            @if ($expenseCategories->isEmpty())
                <p class="empty-state">No expense categories yet.</p>
            @else
                <div class="category-list">
                    @foreach ($expenseCategories as $category)
                        <div class="category-card" style="border-left: 4px solid {{ $category->color ?? '#e74c3c' }}">
                            <div class="category-header">
                                <div class="category-display">
                                    <span class="category-icon" style="color: {{ $category->color ?? '#e74c3c' }}">{{ $category->icon ?? '💳' }}</span>
                                    <div>
                                        <h4>{{ $category->name }}</h4>
                                        <p class="category-type">Expense</p>
                                    </div>
                                </div>
                                <span class="category-badge expense">Expense</span>
                            </div>

                            <form action="{{ route('categories.update', $category) }}" method="POST" class="category-edit-form">
                                @csrf
                                @method('PUT')

                                <div class="grid-two">
                                    <input type="text" name="name" class="input input-sm" value="{{ $category->name }}" required>
                                    <input type="text" name="icon" class="input input-sm" value="{{ $category->icon }}" placeholder="Icon" maxlength="3">
                                </div>

                                <input type="color" name="color" class="input input-sm" value="{{ $category->color ?? '#e74c3c' }}">

                                <div class="category-actions">
                                    <button type="submit" class="btn-icon btn-primary" title="Save">✓</button>
                                    <button type="button" class="btn-icon btn-secondary" onclick="this.closest('.category-edit-form').classList.remove('visible')" title="Cancel">✕</button>
                                </div>
                            </form>

                            <div class="category-footer">
                                <button type="button" class="btn-icon-text" onclick="this.closest('.category-card').querySelector('.category-edit-form').classList.add('visible')" title="Edit">✎</button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-text btn-danger" title="Delete">🗑</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </article>
    </section>

    <style>
        .category-list {
            display: grid;
            gap: 1rem;
        }

        .category-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateX(4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .category-display {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .category-icon {
            font-size: 1.5rem;
        }

        .category-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .category-type {
            margin: 0;
            font-size: 0.75rem;
            color: #7f8c8d;
        }

        .category-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
        }

        .category-badge.income {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
        }

        .category-badge.expense {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }

        .category-edit-form {
            display: none;
            background: rgba(255, 255, 255, 0.7);
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            gap: 0.5rem;
            flex-direction: column;
        }

        .category-edit-form.visible {
            display: flex;
        }

        .category-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .category-footer {
            display: flex;
            gap: 0.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
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
            width: 32px;
            height: 32px;
            border: none;
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.2s ease;
            padding: 0;
            margin-right: 0.25rem;
        }

        .btn-icon-text:hover {
            background: rgba(52, 152, 219, 0.3);
        }

        .btn-icon-text.btn-danger {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .btn-icon-text.btn-danger:hover {
            background: rgba(231, 76, 60, 0.3);
        }
    </style>
@endsection
