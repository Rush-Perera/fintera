@extends('layouts.guest')

@section('title', 'Login | Fintera')

@section('content')
    <section class="hero-center">
        <div class="glass-card auth-card">
            <p class="eyebrow">Welcome Back</p>
            <h1>Login to your finance space</h1>
            <p class="muted">Track your daily income and expenses in one clean dashboard.</p>

            <form action="{{ route('login.store') }}" method="POST" class="stack-md">
                @csrf

                <label class="field-label" for="email">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus>

                <label class="field-label" for="password">Password</label>
                <input id="password" name="password" type="password" class="input" required>

                <label class="check-row">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <span>Keep me signed in</span>
                </label>

                <button type="submit" class="btn-primary full">Login</button>
            </form>

            <p class="inline-note">
                New here?
                <a href="{{ route('register') }}">Create an account</a>
            </p>
        </div>
    </section>
@endsection
