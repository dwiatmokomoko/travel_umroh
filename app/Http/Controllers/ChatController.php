<?php

namespace App\Http\Controllers;

use App\Support\ChatAssistant;
use App\Support\SiteContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request, SiteContent $site, ChatAssistant $assistant): JsonResponse
    {
        abort_unless($site->text('chatbot_enabled', '1') === '1', 404);
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1500'],
            'history' => ['sometimes', 'array', 'max:6'],
            'history.*' => ['array:role,content'],
            'history.*.role' => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:3000'],
        ]);

        return response()->json($assistant->answer($data['message'], $data['history'] ?? [], $site))
            ->header('Cache-Control', 'no-store, private');
    }
}
