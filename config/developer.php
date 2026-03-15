<?php

/**
 * Configuración del Developer Panel
 *
 * Valores editables en .env:
 *   DEVELOPER_TOTP_SECRET   — Secret TOTP (generado con developer:setup-totp)
 *   DEVELOPER_SESSION_HOURS — Horas de sesión activa (default: 8)
 *   DEVELOPER_TOTP_WINDOW   — Ventana de tolerancia en pasos de 30s (default: 1)
 */
return [

    'totp_secret'    => env('DEVELOPER_TOTP_SECRET'),
    'session_hours'  => env('DEVELOPER_SESSION_HOURS', 8),
    'totp_window'    => env('DEVELOPER_TOTP_WINDOW', 1),

];
