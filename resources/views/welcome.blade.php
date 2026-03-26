@extends('layouts.guest')

@section('title', 'Fintera | Personal Finance Tracker')

@section('content')
    <section class="hero-center">
        <div class="glass-card hero-card">
            <p class="eyebrow">Personal Finance, Simplified</p>
            <h1>Build better money habits with clear daily tracking.</h1>
            <p class="muted">
                Add income and expenses by date, upload payslips, track methods of payment,
                and understand how your month is going in one focused dashboard.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Open Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn-ghost">Create Account</a>
                @endauth
            </div>
        </div>
    </section>
@endsection
