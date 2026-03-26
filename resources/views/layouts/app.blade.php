<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Fintera Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|manrope:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="screen-shell app-shell">
    <div class="ambient-bg" aria-hidden="true"></div>

    <header class="top-nav">
        <a href="{{ route('dashboard') }}" class="brand-mark">Fintera</a>

        <nav class="nav-links">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('payment-methods.index') }}" class="nav-link {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}">Payment Methods</a>
            <a href="{{ route('fund-accounts.index') }}" class="nav-link {{ request()->routeIs('fund-accounts.*') ? 'active' : '' }}">Fund Accounts</a>
        </nav>

        <div class="nav-actions">
            <span class="user-pill">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-ghost">Logout</button>
            </form>
        </div>
    </header>

    <main class="page-wrap app-wrap">
        @include('layouts.partials.flash')
        @yield('content')
    </main>

    <nav class="mobile-tabbar">
        <a href="{{ route('dashboard') }}" class="mobile-tab {{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
        <a href="{{ route('dashboard', ['quick_type' => 'expense']) }}#txn-form" class="mobile-tab">Expense</a>
        <a href="{{ route('dashboard', ['quick_type' => 'income']) }}#txn-form" class="mobile-tab">Income</a>
        <a href="{{ route('payment-methods.index') }}" class="mobile-tab {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}">Methods</a>
        <a href="{{ route('fund-accounts.index') }}" class="mobile-tab {{ request()->routeIs('fund-accounts.*') ? 'active' : '' }}">Funds</a>
    </nav>
</body>
</html>
