<?php

namespace App\Helpers;

class TelefonoHelper
{
    /**
     * Normaliza un número de teléfono a solo dígitos.
     * Chatwoot usa E.164 (+50769876543), el CRM puede tener formatos locales.
     */
    public static function soloDigitos(?string $tel): string
    {
        if (!$tel) return '';
        return preg_replace('/\D/', '', $tel);
    }

    /**
     * Devuelve los últimos N dígitos para comparación flexible.
     * +50769876543 → 69876543 (últimos 8)
     */
    public static function ultimosDigitos(?string $tel, int $n = 8): string
    {
        $digitos = self::soloDigitos($tel);
        return strlen($digitos) >= $n ? substr($digitos, -$n) : $digitos;
    }

    /**
     * Construye el fragmento SQL para buscar por teléfono de forma flexible
     * en la columna dada. Compatible con PostgreSQL.
     */
    public static function whereColumna(string $columna, string $tel): array
    {
        $ultimos = self::ultimosDigitos($tel);

        // Busca los últimos 8 dígitos dentro de la columna (strip non-digits vía regexp_replace)
        return [
            "regexp_replace({$columna}, '[^0-9]', '', 'g') LIKE ?",
            ["%{$ultimos}"],
        ];
    }
}
