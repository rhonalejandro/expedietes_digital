           
@extends('layouts.onboarding')

@section('content')
<div class="onboarding-panel">
    <div class="onboarding-header">
        <h3 class="onboarding-title">{{ __('onboarding.Configuración inicial') }}</h3>
    </div>
    <div class="panel-body onboarding-form" style="padding: 32px 24px;">
        <form method="POST" action="{{ route('onboarding.store') }}">
            @csrf
            <h4 class="text-center" style="margin-bottom:28px; color:#007b8a; font-weight:600; letter-spacing:1px;">
                <span class="glyphicon glyphicon-briefcase" style="margin-right:8px;"></span>
                {{ __('onboarding.Datos de la empresa') !== 'onboarding.Datos de la empresa' ? __('onboarding.Datos de la empresa') : 'Datos de la empresa' }}
            </h4>
            <div class="form-group has-feedback">
                <label for="empresa_nombre">{{ __('onboarding.Nombre de la empresa') !== 'onboarding.Nombre de la empresa' ? __('onboarding.Nombre de la empresa') : 'Nombre de la empresa' }}</label>
                <input type="text" class="form-control" id="empresa_nombre" name="empresa_nombre" value="{{ old('empresa_nombre') }}" required>
                <span class="glyphicon glyphicon-home form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_tipo_identificacion">Tipo de identificación</label>
                <select class="form-control" id="empresa_tipo_identificacion" name="empresa_tipo_identificacion" required>
                    <option value="">Seleccione...</option>
                    <option value="RUC" {{ old('empresa_tipo_identificacion') == 'RUC' ? 'selected' : '' }}>RUC</option>
                    <option value="RIF" {{ old('empresa_tipo_identificacion') == 'RIF' ? 'selected' : '' }}>RIF</option>
                    <option value="Cédula Jurídica" {{ old('empresa_tipo_identificacion') == 'Cédula Jurídica' ? 'selected' : '' }}>Cédula Jurídica</option>
                    <option value="Otro" {{ old('empresa_tipo_identificacion') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
                <span class="glyphicon glyphicon-credit-card form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_identificacion">Número de identificación</label>
                <input type="text" class="form-control" id="empresa_identificacion" name="empresa_identificacion" value="{{ old('empresa_identificacion') }}" required>
                <span class="glyphicon glyphicon-barcode form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_direccion">Dirección</label>
                <input type="text" class="form-control" id="empresa_direccion" name="empresa_direccion" value="{{ old('empresa_direccion') }}" required>
                <span class="glyphicon glyphicon-map-marker form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_telefono">Teléfono</label>
                <input type="text" class="form-control" id="empresa_telefono" name="empresa_telefono" value="{{ old('empresa_telefono') }}" required>
                <span class="glyphicon glyphicon-earphone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_email">Correo electrónico</label>
                <input type="email" class="form-control" id="empresa_email" name="empresa_email" value="{{ old('empresa_email') }}" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_pagina_web">Página web</label>
                <input type="url" class="form-control" id="empresa_pagina_web" name="empresa_pagina_web" value="{{ old('empresa_pagina_web') }}">
                <span class="glyphicon glyphicon-globe form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_facebook">Facebook</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-thumbs-up" style="color:#3b5998;"></i></span>
                    <input type="url" class="form-control" id="empresa_facebook" name="empresa_facebook" value="{{ old('empresa_facebook') }}" placeholder="https://facebook.com/empresa">
                </div>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_instagram">Instagram</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-camera" style="color:#e4405f;"></i></span>
                    <input type="url" class="form-control" id="empresa_instagram" name="empresa_instagram" value="{{ old('empresa_instagram') }}" placeholder="https://instagram.com/empresa">
                </div>
            </div>
            <div class="form-group has-feedback">
                <label for="empresa_whatsapp">WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-phone" style="color:#25d366;"></i></span>
                    <input type="text" class="form-control" id="empresa_whatsapp" name="empresa_whatsapp" value="{{ old('empresa_whatsapp') }}" placeholder="+584123456789">
                </div>
            </div>
            </div>
            <hr style="margin:32px 0 24px 0;">
            <h4 class="text-center" style="margin-bottom:28px; color:#007b8a; font-weight:600; letter-spacing:1px;">
                <span class="glyphicon glyphicon-user" style="margin-right:8px;"></span>
                {{ __('onboarding.Datos del superusuario') !== 'onboarding.Datos del superusuario' ? __('onboarding.Datos del superusuario') : 'Datos del superusuario' }}
            </h4>
            <div class="form-group has-feedback" style="margin-bottom:22px;">
                <label for="usuario_nombre">{{ __('onboarding.Nombre completo') !== 'onboarding.Nombre completo' ? __('onboarding.Nombre completo') : 'Nombre completo' }}</label>
                <input type="text" class="form-control" id="usuario_nombre" name="usuario_nombre" value="{{ old('usuario_nombre') }}" required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback" style="margin-bottom:22px;">
                <label for="usuario_email">{{ __('onboarding.Correo electrónico') !== 'onboarding.Correo electrónico' ? __('onboarding.Correo electrónico') : 'Correo electrónico' }}</label>
                <input type="email" class="form-control" id="usuario_email" name="usuario_email" value="{{ old('usuario_email') }}" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback" style="margin-bottom:22px;">
                <label for="usuario_password">{{ __('onboarding.Contraseña') !== 'onboarding.Contraseña' ? __('onboarding.Contraseña') : 'Contraseña' }}</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="usuario_password" name="usuario_password" required>
                    <span class="input-group-btn">
                        <button class="btn btn-default toggle-password" type="button" tabindex="-1" data-target="#usuario_password">
                            <span class="glyphicon glyphicon-eye-open"></span>
                        </button>
                    </span>
                </div>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback" style="margin-bottom:22px;">
                <label for="usuario_password_confirmation">{{ __('onboarding.Confirmar contraseña') !== 'onboarding.Confirmar contraseña' ? __('onboarding.Confirmar contraseña') : 'Confirmar contraseña' }}</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="usuario_password_confirmation" name="usuario_password_confirmation" required>
                    <span class="input-group-btn">
                        <button class="btn btn-default toggle-password" type="button" tabindex="-1" data-target="#usuario_password_confirmation">
                            <span class="glyphicon glyphicon-eye-open"></span>
                        </button>
                    </span>
                </div>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:24px; font-size:1.2em;">
                <span class="glyphicon glyphicon-ok-circle" style="margin-right:8px;"></span>
                {{ __('onboarding.Completar configuración inicial') !== 'onboarding.Completar configuración inicial' ? __('onboarding.Completar configuración inicial') : 'Completar configuración inicial' }}
            </button>
        </form>
    </div>
</div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggles = document.querySelectorAll('.toggle-password');
        toggles.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var input = document.querySelector(btn.getAttribute('data-target'));
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        btn.querySelector('span').classList.remove('glyphicon-eye-open');
                        btn.querySelector('span').classList.add('glyphicon-eye-close');
                    } else {
                        input.type = 'password';
                        btn.querySelector('span').classList.remove('glyphicon-eye-close');
                        btn.querySelector('span').classList.add('glyphicon-eye-open');
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection
