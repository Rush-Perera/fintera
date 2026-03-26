@extends('layouts.guest')

@section('title', 'Register | Fintera')

@section('content')
    <section class="hero-center">
        <div class="glass-card auth-card">
            <p class="eyebrow">Get Started</p>
            <h1>Create your account</h1>
            <p class="muted">Set up your personal finance dashboard in under a minute.</p>

            <form action="{{ route('register.store') }}" method="POST" class="stack-md">
                @csrf

                <label class="field-label" for="name">Name</label>
                <input id="name" name="name" type="text" class="input" value="{{ old('name') }}" required>

                <label class="field-label" for="email">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required>

                <label class="field-label" for="password">Password</label>
                <input id="password" name="password" type="password" class="input" required>

                <label class="field-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input" required>

                <button type="submit" class="btn-primary full">Create Account</button>
            </form>

            <p class="inline-note">
                Already have an account?
                <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </section>
@endsection
