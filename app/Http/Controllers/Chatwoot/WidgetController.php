<?php

namespace App\Http\Controllers\Chatwoot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index(Request $request)
    {
        $token  = config('services.chatwoot.widget_token');
        $apiUrl = url('/api/v1');
        $crmUrl = url('/');

        return view('chatwoot.widget', compact('token', 'apiUrl', 'crmUrl'))
            ->header('X-Frame-Options', 'ALLOWALL')
            ->header('Content-Security-Policy', "frame-ancestors *");
    }
}
