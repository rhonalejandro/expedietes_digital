<?php

namespace App\Http\Controllers\Api;

use App\Helpers\TelefonoHelper;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Persona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatwootWebhookController extends Controller
{
    // POST /api/v1/webhook/chatwoot

    public function handle(Request $request): JsonResponse
    {
        // Verificar firma HMAC si está configurada
        $secret = config('services.chatwoot.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Chatwoot-Signature');
            $expected  = hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($expected, (string) $signature)) {
                return response()->json(['error' => 'Firma inválida.'], 401);
            }
        }

        $event = $request->input('event');
        $data  = $request->all();

        Log::channel('single')->info("[Chatwoot Webhook] Evento: {$event}", ['data' => $data]);

        match ($event) {
            'conversation_created' => $this->onConversacionCreada($data),
            default                => null,
        };

        return response()->json(['ok' => true]);
    }

    private function onConversacionCreada(array $data): void
    {
        $contact = $data['meta']['sender'] ?? $data['contact'] ?? null;
        if (!$contact) return;

        $telefono  = $contact['phone_number']   ?? null;
        $nombre    = $contact['name']            ?? 'Contacto de Chatwoot';
        $email     = $contact['email']           ?? null;
        $cw_id     = $contact['id']              ?? null;
        $cw_conv   = $data['id']                 ?? null;

        if (!$telefono && !$email) return;

        // Buscar si ya existe como paciente
        if ($telefono) {
            [$sql, $bindings] = TelefonoHelper::whereColumna('personas.contacto', $telefono);
            $persona = Persona::whereRaw($sql, $bindings)->first();
            if ($persona?->paciente) return; // ya es paciente, no crear lead
        }

        // Buscar si ya existe como lead
        $existeLead = Lead::when($cw_id, fn($q) => $q->where('chatwoot_contact_id', $cw_id))
            ->activos()
            ->exists();

        if ($existeLead) return;

        Lead::create([
            'nombre'              => $nombre,
            'telefono'            => $telefono,
            'email'               => $email,
            'origen'              => 'chatwoot',
            'chatwoot_contact_id' => $cw_id,
            'chatwoot_conv_id'    => $cw_conv,
            'estatus'             => 'nuevo',
        ]);
    }
}
