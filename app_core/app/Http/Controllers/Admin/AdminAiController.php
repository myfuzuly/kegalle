<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminAiController extends Controller
{
    public function suggestFromImages(Request $request)
    {
        $content = [];
        $added   = 0;

        // Mode A: existing listing (edit page)
        if ($request->filled('listing_id')) {
            $request->validate(['listing_id' => 'integer|exists:listings,id']);
            $listing = Listing::with('images')->findOrFail($request->integer('listing_id'));
            foreach ($listing->images->take(4) as $image) {
                $relativePath = ltrim($image->path, '/\\');
                $fullPath = storage_path('app/public/' . $relativePath);
                if (!file_exists($fullPath)) continue;
                $mime = mime_content_type($fullPath);
                if (!in_array($mime, ['image/jpeg','image/png','image/gif','image/webp'])) continue;
                $content[] = ['type'=>'image','source'=>['type'=>'base64','media_type'=>$mime,'data'=>base64_encode(file_get_contents($fullPath))]];
                $added++;
            }
        }
        // Mode B: base64 images from JS (create page)
        elseif ($request->filled('images')) {
            $request->validate(['images'=>'array|min:1|max:4','images.*.data'=>'required|string','images.*.type'=>'required|string']);
            $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            foreach (array_slice($request->input('images'), 0, 4) as $img) {
                $mime = $img['type'] ?? '';
                if (!in_array($mime, $allowed)) continue;
                $content[] = ['type'=>'image','source'=>['type'=>'base64','media_type'=>$mime,'data'=>$img['data']]];
                $added++;
            }
        }
        else {
            return response()->json(['error' => 'Provide listing_id or images array.'], 422);
        }

        if ($added === 0) {
            return response()->json(['error' => 'No readable images found.'], 422);
        }

        $content[] = ['type'=>'text','text'=>'You are an SEO copywriter for Kegalle Marketplace, a local classifieds site in Kegalle district, Sri Lanka. Based on the product image(s), generate: (1) An SEO-optimised listing TITLE — max 50 characters, front-load the primary keyword (product type + brand/model if visible), be specific and meaningful, do NOT include any location or place names, no filler words like "for sale", "best price", or "available"; (2) An SEO-optimised DESCRIPTION as an HTML bullet list of 4-6 points — each point must naturally include a search keyword buyers would use (e.g. brand, model, material, size, condition, use case), write in clear English, avoid keyword stuffing, mention "Kegalle" once if relevant. Reply ONLY with valid JSON, no extra text: {"title":"...","description":"<ul><li>Point one</li><li>Point two</li></ul>"}'];

        $apiKey = config('services.claude.api_key');
        if (empty($apiKey)) return response()->json(['error' => 'CLAUDE_API_KEY not configured.'], 500);

        try {
            $response = Http::withHeaders(['x-api-key'=>$apiKey,'anthropic-version'=>'2023-06-01','content-type'=>'application/json'])
                ->timeout(30)->post('https://api.anthropic.com/v1/messages', ['model'=>'claude-haiku-4-5-20251001','max_tokens'=>400,'messages'=>[['role'=>'user','content'=>$content]]]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection failed: '.$e->getMessage()], 500);
        }

        if (!$response->successful()) return response()->json(['error' => 'AI request failed ('.$response->status().'): '.substr($response->body(),0,300)], 500);

        $text = $response->json('content.0.text', '');
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $parsed = json_decode($m[0], true);
            if ($parsed && isset($parsed['title'], $parsed['description'])) return response()->json($parsed);
        }
        return response()->json(['error' => 'Could not parse AI response: '.substr($text,0,200)], 500);
    }

    public function translateText(Request $request)
    {
        $request->validate(['text' => 'required|string|max:8000', 'language' => 'required|in:sinhala,tamil']);

        $apiKey   = config('services.google_translate.api_key');
        $language = $request->input('language');
        $target   = $language === 'sinhala' ? 'si' : 'ta';
        $text     = strip_tags($request->input('text'));

        if (trim($text) === '') return response()->json(['error' => 'No text to translate.'], 422);

        // Use Google Cloud Translation API if key is configured
        if (!empty($apiKey)) {
            try {
                $response = Http::timeout(15)->post(
                    'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey,
                    ['q' => $text, 'target' => $target, 'source' => 'en', 'format' => 'text']
                );
            } catch (\Exception $e) {
                return response()->json(['error' => 'Translation request failed: ' . $e->getMessage()], 500);
            }

            if (!$response->successful()) {
                return response()->json(['error' => 'Translation failed (' . $response->status() . ').'], 500);
            }

            $translated = trim($response->json('data.translations.0.translatedText', ''));
            if ($translated === '') return response()->json(['error' => 'Empty translation returned.'], 500);

            $lines = array_values(array_filter(array_map('trim', explode("\n", $translated))));
            $html  = '<ul>' . implode('', array_map(fn($l) => '<li>' . htmlspecialchars($l, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>', $lines)) . '</ul>';

            return response()->json(['translated' => $html]);
        }

        // Fallback: use Claude if Google key not set
        $claudeKey = config('services.claude.api_key');
        if (empty($claudeKey)) return response()->json(['error' => 'Translation API key not configured.'], 500);

        $langName = $language === 'sinhala' ? 'Sinhala (සිංහල)' : 'Tamil (தமிழ்)';
        $prompt   = "Translate the following English product listing description into {$langName} for a Sri Lankan classifieds marketplace. Keep bullet points if present. Return ONLY the translated text, no explanation.\n\n{$text}";

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $claudeKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'    => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1000,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Translation request failed: ' . $e->getMessage()], 500);
        }

        if (!$response->successful()) {
            return response()->json(['error' => 'Translation failed (' . $response->status() . ').'], 500);
        }

        $translated = trim($response->json('content.0.text', ''));
        if ($translated === '') return response()->json(['error' => 'Empty translation returned.'], 500);

        $lines = array_values(array_filter(array_map('trim', explode("\n", $translated))));
        $html  = '<ul>' . implode('', array_map(fn($l) => '<li>' . htmlspecialchars($l, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</li>', $lines)) . '</ul>';

        return response()->json(['translated' => $html]);
    }
}
