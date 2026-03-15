<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Panel — Acceso</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}">
    <style>
        body { background: #0d1117; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { background: #161b22; border: 1px solid #30363d; border-radius: 12px; width: 100%; max-width: 360px; padding: 2rem; }
        .login-card input { background: #0d1117; border: 1px solid #30363d; color: #c9d1d9; text-align: center; letter-spacing: .3em; font-size: 1.5rem; font-weight: 600; }
        .login-card input:focus { background: #0d1117; border-color: #667eea; color: #c9d1d9; box-shadow: 0 0 0 3px rgba(102,126,234,.2); }
        .login-card input::placeholder { letter-spacing: normal; font-size: 14px; color: #8b949e; font-weight: 400; }
        .btn-primary { background: #667eea; border: none; }
        .btn-primary:hover { background: #5a6fd6; }
        .alert-danger { background: rgba(248,81,73,.1); border-color: rgba(248,81,73,.3); color: #f85149; font-size: 13px; }
        .alert-success { background: rgba(63,185,80,.1); border-color: rgba(63,185,80,.3); color: #3fb950; font-size: 13px; }
    </style>
</head>
<body>

    <div class="login-card">
        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="mb-3" style="font-size: 2.5rem; color: #667eea;">
                <i class="ti ti-shield-lock"></i>
            </div>
            <h5 class="fw-bold mb-1" style="color: #c9d1d9;">Developer Panel</h5>
            <p class="mb-0" style="color: #8b949e; font-size: 13px;">
                Ingresa el código de Google Authenticator
            </p>
        </div>

        {{-- Alerts --}}
        @if (session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        {{-- Formulario TOTP --}}
        <form method="POST" action="{{ route('developer.login') }}">
            @csrf
            <div class="mb-3">
                <input
                    type="text"
                    name="code"
                    class="form-control @error('code') is-invalid @enderror"
                    placeholder="000000"
                    maxlength="6"
                    autocomplete="one-time-code"
                    inputmode="numeric"
                    autofocus
                >
                @error('code')
                    <div class="invalid-feedback text-center">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-login me-1"></i>Verificar
            </button>
        </form>

        <p class="text-center mt-3 mb-0" style="color: #8b949e; font-size: 12px;">
            Código de 6 dígitos · se renueva cada 30 segundos
        </p>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Auto-submit cuando se ingresan 6 dígitos
        document.querySelector('input[name="code"]').addEventListener('input', function () {
            if (this.value.replace(/\D/g, '').length === 6) {
                this.value = this.value.replace(/\D/g, '');
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>
