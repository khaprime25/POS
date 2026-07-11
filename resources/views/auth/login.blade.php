@extends('layouts.guest')

@section('content')

<div class="login-wrapper">

    <div class="login-container">

        {{-- LEFT SIDE --}}
        <div class="login-left">

            <div class="login-logo">

                <i class="fa-solid fa-mug-hot"></i>

            </div>

            <h1 style="display: inline-block;">
                Cafe POS
            </h1>

            <p class="login-tagline">
                Simple. Fast. Reliable.
            </p>

            <p class="login-description">

                Manage orders, kitchen workflow, inventory,
                staff and sales from one clean dashboard.

            </p>

            <div class="login-features">

                <div class="login-feature">

                    <i class="fa-solid fa-check"></i>

                    Fast Checkout

                </div>

                <div class="login-feature">

                    <i class="fa-solid fa-check"></i>

                    Kitchen Workflow

                </div>

                <div class="login-feature">

                    <i class="fa-solid fa-check"></i>

                    Inventory Tracking

                </div>

                <div class="login-feature">

                    <i class="fa-solid fa-check"></i>

                    Daily Sales Reports

                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="login-card">

            <div class="mb-3">

                <h2 style="margin-bottom: 1rem;">
                    Sign In
                </h2>

                <p class="section-subtitle">
                    Welcome back! Please login to continue.
                </p>

            </div>

            <form method="POST"
                action="{{ route('login') }}">

                @csrf

                {{-- Email --}}
                <div class="form-group mb-3">

                    <label class="form-label">

                        Email Address

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email') }}"
                        placeholder="owner@example.com"
                        required
                        autofocus>

                    @error('email')

                    <small class="form-error">

                        {{ $message }}

                    </small>

                    @enderror

                </div>

                {{-- Password --}}
                <div class="form-group mb-3">

                    <label class="form-label">

                        Password

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="••••••••"
                        required>

                    @error('password')

                    <small class="form-error">

                        {{ $message }}

                    </small>

                    @enderror

                </div>

                <div class="login-options">

                    <label class="remember-check">

                        <input
                            type="checkbox"
                            name="remember">

                        Remember Me

                    </label>

                    @if(Route::has('password.request'))

                    <a href="{{ route('password.request') }}">

                        Forgot Password?

                    </a>

                    @endif

                </div>

                <button
                    type="submit"
                    class="btn btn-primary login-btn">

                    <i class="fa-solid fa-right-to-bracket me-2"></i>

                    Login

                </button>

            </form>

        </div>

    </div>

</div>

@endsection