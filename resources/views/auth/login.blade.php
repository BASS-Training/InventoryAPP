@extends('layouts.app') {{-- Menggunakan layout utama Bootstrap kita --}}

@section('title', 'Login Aplikasi Inventaris') {{-- Judul Halaman --}}

@section('content')
<div class="container">
    <div class="row justify-content-center mt-lg-5">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">{{ __('Login ke Aplikasi Inventaris') }}</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Alamat Email') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                <button id="hold-to-reveal-password" class="btn btn-outline-secondary" type="button" aria-label="Tahan untuk melihat password" title="Tahan untuk melihat password">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted"></small>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                            <label class="form-check-label" for="remember_me">
                                {{ __('Ingat Saya') }}
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Log in') }}
                            </button>
                        </div>

                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link btn-sm" href="{{ route('password.request') }}">
                                    {{ __('Lupa password Anda?') }}
                                </a>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const revealButton = document.getElementById('hold-to-reveal-password');

    if (!passwordInput || !revealButton) return;

    const icon = revealButton.querySelector('i');
    const showPassword = () => {
        passwordInput.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
        revealButton.setAttribute('aria-label', 'Lepaskan untuk menyembunyikan password');
    };
    const hidePassword = () => {
        passwordInput.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
        revealButton.setAttribute('aria-label', 'Tahan untuk melihat password');
    };

    revealButton.addEventListener('pointerdown', (event) => {
        event.preventDefault();
        showPassword();
    });
    ['pointerup', 'pointerleave', 'pointercancel', 'lostpointercapture'].forEach((eventName) => {
        revealButton.addEventListener(eventName, hidePassword);
    });
    document.addEventListener('pointerup', hidePassword);

    revealButton.addEventListener('keydown', (event) => {
        if (event.key === ' ' || event.key === 'Enter') showPassword();
    });
    revealButton.addEventListener('keyup', hidePassword);
});
</script>
@endpush
