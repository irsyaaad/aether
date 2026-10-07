<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userMessage  = $request->message;
        $systemPrompt = "You are a helpful AI assistant. you know everything";

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type'  => 'application/json',
            ])
                ->withoutVerifying()
                ->timeout(30)
                ->post($url, [
                    'model'       => 'llama-3.1-8b-instant',
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $aiMessage = $response->json()['choices'][0]['message']['content'];
                return response()->json([
                    'status'  => 'success',
                    'message' => $aiMessage,
                    'sender'  => 'ai',
                ]);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Maaf, asisten AI sedang sibuk. Silakan coba lagi nanti.',
                'sender'  => 'ai',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi AI.',
                'sender'  => 'ai',
            ], 500);
        }
    }
}
