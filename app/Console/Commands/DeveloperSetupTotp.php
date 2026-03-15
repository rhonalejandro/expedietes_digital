<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PragmaRX\Google2FA\Google2FA;

/**
 * Comando: developer:setup-totp
 *
 * Genera un secret TOTP compatible con Google Authenticator,
 * lo guarda en .env como DEVELOPER_TOTP_SECRET y muestra
 * el URI de provisioning para escanear o agregar manualmente.
 *
 * Uso:
 *   php artisan developer:setup-totp
 */
class DeveloperSetupTotp extends Command
{
    protected $signature   = 'developer:setup-totp {--force : Regenerar aunque ya exista un secret}';
    protected $description = 'Configura el TOTP para el Developer Panel (Google Authenticator)';

    public function handle(): int
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            $this->error('No se encontró el archivo .env');
            return self::FAILURE;
        }

        $envContent = file_get_contents($envPath);

        // Verificar si ya existe el secret
        $exists = str_contains($envContent, 'DEVELOPER_TOTP_SECRET=');

        if ($exists && !$this->option('force')) {
            $this->warn('Ya existe un DEVELOPER_TOTP_SECRET en .env');
            $this->line('Usa --force para regenerarlo (perderás acceso con el código QR anterior).');
            return self::SUCCESS;
        }

        // Generar nuevo secret
        $google2fa = new Google2FA();
        $secret    = $google2fa->generateSecretKey(32);
        $appName   = config('app.name', 'ExpedienteDigital');
        $issuer    = str_replace(' ', '', $appName);

        // Guardar en .env
        if ($exists) {
            $envContent = preg_replace(
                '/^DEVELOPER_TOTP_SECRET=.*/m',
                "DEVELOPER_TOTP_SECRET={$secret}",
                $envContent
            );
        } else {
            $envContent .= "\n# Developer Panel — TOTP (Google Authenticator)\nDEVELOPER_TOTP_SECRET={$secret}\n";
        }

        file_put_contents($envPath, $envContent);

        // Generar URI de provisioning
        $otpauthUri = $google2fa->getQRCodeUrl($issuer, 'developer@' . $issuer, $secret);

        $this->newLine();
        $this->info('✓ Secret TOTP generado y guardado en .env');
        $this->newLine();

        $this->line('<fg=yellow>── Agregar a Google Authenticator ────────────────────────────</>');
        $this->newLine();
        $this->line('  Opción A — Clave manual en la app:');
        $this->line("  <fg=cyan>Cuenta:</> developer");
        $this->line("  <fg=cyan>Clave:</>   {$secret}");
        $this->line("  <fg=cyan>Tipo:</>    Basada en tiempo (TOTP)");
        $this->newLine();
        $this->line('  Opción B — URI (para QR externo):');
        $this->line("  <fg=green>{$otpauthUri}</>");
        $this->newLine();
        $this->line('<fg=yellow>──────────────────────────────────────────────────────────────</>');
        $this->newLine();
        $this->comment('Accede a: /developer  →  ingresa el código de 6 dígitos de la app');
        $this->newLine();

        return self::SUCCESS;
    }
}
