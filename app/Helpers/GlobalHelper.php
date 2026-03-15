<?php

namespace App\Helpers;

/**
 * GlobalHelper
 * 
 * Funciones globales reutilizables en toda la aplicación.
 * Principio DRY: Centraliza funciones comunes para evitar código repetido.
 */
class GlobalHelper
{
    /**
     * Formatear teléfono
     * 
     * @param string $phone
     * @return string
     */
    public static function formatPhone(string $phone): string
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

    /**
     * Validar si un string es nulo o vacío
     * 
     * @param mixed $value
     * @return bool
     */
    public static function isEmpty($value): bool
    {
        return $value === null || trim($value) === '';
    }

    /**
     * Sanitizar string
     * 
     * @param string $value
     * @return string
     */
    public static function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generar slug
     * 
     * @param string $text
     * @return string
     */
    public static function generateSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        return preg_replace('/-+/', '-', $text);
    }

    /**
     * Formatear moneda
     * 
     * @param float $amount
     * @param string $currency
     * @return string
     */
    public static function formatCurrency(float $amount, string $currency = 'USD'): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'CRC' => '₡',
        ];
        
        $symbol = $symbols[$currency] ?? $currency;
        return $symbol . number_format($amount, 2);
    }

    /**
     * Obtener iniciales de un nombre
     * 
     * @param string $name
     * @return string
     */
    public static function getInitials(string $name): string
    {
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        
        return substr($initials, 0, 3);
    }

    /**
     * Verificar si una fecha es hoy
     * 
     * @param string $date
     * @return bool
     */
    public static function isToday(string $date): bool
    {
        return date('Y-m-d', strtotime($date)) === date('Y-m-d');
    }

    /**
     * Formatear fecha
     * 
     * @param string $date
     * @param string $format
     * @return string
     */
    public static function formatDate(string $date, string $format = 'd/m/Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Calcular diferencia de días entre fechas
     * 
     * @param string $date1
     * @param string $date2
     * @return int
     */
    public static function daysDifference(string $date1, string $date2): int
    {
        $datetime1 = new \DateTime($date1);
        $datetime2 = new \DateTime($date2);
        
        $interval = $datetime1->diff($datetime2);
        return $interval->days;
    }

    /**
     * Truncar texto
     * 
     * @param string $text
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate(string $text, int $length = 50, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . $suffix;
    }

    /**
     * Generar color aleatorio pastel
     * 
     * @return string
     */
    public static function randomPastelColor(): string
    {
        $colors = [
            '#FFB3BA', '#BAFFC9', '#BAE1FF', '#FFFFBA',
            '#FFDFBA', '#E2F0CB', '#B5EAD7', '#C7CEEA'
        ];
        
        return $colors[array_rand($colors)];
    }

    /**
     * Verificar si es un email válido
     * 
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Obtener dominio de un email
     * 
     * @param string $email
     * @return string
     */
    public static function getEmailDomain(string $email): string
    {
        $parts = explode('@', $email);
        return $parts[1] ?? '';
    }

    /**
     * Enmascarar email
     * 
     * @param string $email
     * @return string
     */
    public static function maskEmail(string $email): string
    {
        if (!strpos($email, '@')) {
            return $email;
        }
        
        list($user, $domain) = explode('@', $email);
        $userLength = strlen($user);
        
        if ($userLength <= 2) {
            $maskedUser = $user[0] . '*';
        } else {
            $maskedUser = $user[0] . str_repeat('*', $userLength - 2) . $user[$userLength - 1];
        }
        
        return $maskedUser . '@' . $domain;
    }

    /**
     * Convertir bytes a formato legible
     * 
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Generar token aleatorio
     * 
     * @param int $length
     * @return string
     */
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Limpiar string para nombre de archivo
     * 
     * @param string $filename
     * @return string
     */
    public static function cleanFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return strtolower($filename);
    }

    /**
     * Verificar si es un número de teléfono válido (Costa Rica)
     * 
     * @param string $phone
     * @return bool
     */
    public static function isValidCRCPhone(string $phone): bool
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^[0-9]{8}$/', $clean) || preg_match('/^[0-9]{4}$/', $clean);
    }
}
