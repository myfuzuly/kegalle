<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistController extends Controller
{
    public function assist(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:10|max:1000',
            'category'    => 'nullable|string|max:100',
        ]);

        $apiKey = config('services.claude.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'AI assistant is not configured.'], 503);
        }

        $category     = $request->input('category', '');
        $description  = $request->input('description');
        $categoryHint = $category ? "Category: {$category}\n" : '';

        $systemPrompt = <<<PROMPT
You are a marketplace listing assistant for kegalle.com, a Sri Lanka classified ads site.
Given a seller's rough description, generate a polished listing title and description.
Return ONLY valid JSON with keys: title (max 80 chars), description (2-4 sentences, persuasive, mentions key features).
No markdown, no extra text, just the JSON object.
PROMPT;

        $userMessage = "{$categoryHint}Seller description: {$description}";

        try {
            $response = Http::withHeaders([
                'x-api-key'         => trim($apiKey),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(25)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 300,
                'system'     => $systemPrompt,
                'messages'   => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if (!$response->successful()) {
                $body = $response->json();
                $reason = $body['error']['message'] ?? $response->body();
                Log::error('AI assist API error', [
                    'status' => $response->status(),
                    'reason' => $reason,
                ]);
                return response()->json(['error' => 'AI service error. Please try again.'], 502);
            }

            $text = $response->json('content.0.text', '');
            // Strip markdown code fences the model sometimes wraps JSON in
            $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
            $clean = preg_replace('/\s*```$/', '', $clean);
            $data = json_decode($clean, true);

            if (!$data || empty($data['title']) || empty($data['description'])) {
                Log::warning('AI assist bad JSON', ['raw' => $text]);
                return response()->json(['error' => 'Could not parse AI response.'], 502);
            }

            return response()->json([
                'title'       => substr(trim($data['title']), 0, 80),
                'description' => trim($data['description']),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AI assist connection failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not reach AI service. Check server outbound access.'], 503);
        } catch (\Throwable $e) {
            Log::error('AI assist exception: ' . $e->getMessage());
            return response()->json(['error' => 'AI assistant unavailable.'], 503);
        }
    }

    // Admin-only diagnostic: GET /dashboard/listings/ai-diag
    public function diag(Request $request)
    {
        if (! in_array(auth()->user()?->role, ['admin', 'super_admin'])) abort(403);

        $apiKey = config('services.claude.api_key');
        $keyConfigured = !empty($apiKey);

        try {
            $response = Http::withHeaders([
                'x-api-key'         => trim($apiKey),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 10,
                'messages'   => [['role' => 'user', 'content' => 'Say OK']],
            ]);

            return response()->json([
                'key_configured' => $keyConfigured,
                'http_status'    => $response->status(),
                'api_ok'         => $response->successful(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'key_configured' => $keyConfigured,
                'exception'      => $e->getMessage(),
            ]);
        }
    }
}
