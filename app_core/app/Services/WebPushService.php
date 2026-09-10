<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebPushService
{
    public function __construct(private VapidService $vapid) {}

    public function notifyUser(int $userId, string $title, string $body, string $url = '/'): void
    {
        if (!DB::getSchemaBuilder()->hasTable('push_subscriptions')) return;

        DB::table('push_notifications')->insert([
            'user_id'    => $userId,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('push_subscriptions')->where('user_id', $userId)->get()
            ->each(fn($sub) => $this->sendToEndpoint($sub->endpoint, $sub->p256dh, $sub->auth_token, $title, $body, $url));
    }

    private function sendToEndpoint(string $endpoint, string $p256dh, string $authToken, string $title, string $body, string $url): void
    {
        $publicKey = config('services.webpush.public_key');
        if (empty($publicKey)) {
            Log::warning('WebPush: WEBPUSH_PUBLIC_KEY not set');
            return;
        }

        try {
            $jwt     = $this->vapid->buildJwt($endpoint);
            $payload = json_encode(['title' => $title, 'body' => $body, 'url' => $url]);
            $encrypted = $this->encrypt($payload, $p256dh, $authToken);

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization'    => "vapid t={$jwt},k={$publicKey}",
                    'Content-Type'     => 'application/octet-stream',
                    'Content-Encoding' => 'aes128gcm',
                    'TTL'              => '86400',
                    'Urgency'          => 'normal',
                ])
                ->withBody($encrypted, 'application/octet-stream')
                ->post($endpoint);

            if (in_array($response->status(), [410, 404])) {
                DB::table('push_subscriptions')->where('endpoint', $endpoint)->delete();
                Log::info('WebPush: removed expired subscription');
            } elseif (!in_array($response->status(), [200, 201, 202])) {
                Log::warning('WebPush: endpoint returned ' . $response->status());
            }
        } catch (\Throwable $e) {
            Log::error('WebPush error: ' . $e->getMessage());
        }
    }

    /**
     * Encrypt a plaintext payload using RFC 8291 aes128gcm content encoding.
     *
     * Steps:
     *  1. Generate ephemeral P-256 key pair (local)
     *  2. Decode subscriber's p256dh (their public key) and auth
     *  3. ECDH shared secret between local private key + subscriber public key
     *  4. Derive PRK with HKDF using auth as salt
     *  5. Derive content encryption key (CEK) and nonce via HKDF
     *  6. Encrypt with AES-128-GCM
     *  7. Build RFC 8291 header: salt(16) + rs(4) + keyid_len(1) + keyid(65) + ciphertext
     */
    private function encrypt(string $plaintext, string $p256dh, string $authToken): string
    {
        // Decode subscriber keys
        $userPublicKeyRaw = $this->b64uDecode($p256dh);
        $authKey          = $this->b64uDecode($authToken);

        // Generate ephemeral key pair
        $localKey = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $details  = openssl_pkey_get_details($localKey);
        $localPublicRaw = "\x04"
            . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
            . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);

        // Import subscriber public key
        $pem = "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode(
                "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01"
                . "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00"
                . $userPublicKeyRaw
            ), 64, "\n")
            . "-----END PUBLIC KEY-----\n";
        $userPublicKey = openssl_pkey_get_public($pem);

        // ECDH shared secret
        openssl_pkey_export($localKey, $localPrivatePem);
        $localPrivateKey = openssl_pkey_get_private($localPrivatePem);
        openssl_pkey_derive($sharedSecret, $userPublicKey, $localPrivateKey);

        // Salt
        $salt = random_bytes(16);

        // HKDF-SHA256 helper
        $hkdf = function (string $ikm, string $salt, string $info, int $length) {
            $prk = hash_hmac('sha256', $ikm, $salt, true);
            $t   = '';
            $okm = '';
            for ($i = 1; strlen($okm) < $length; $i++) {
                $t    = hash_hmac('sha256', $t . $info . chr($i), $prk, true);
                $okm .= $t;
            }
            return substr($okm, 0, $length);
        };

        // PRK (pseudo-random key) — RFC 8291 §3.4
        $authInfo = "WebPush: info\x00" . $userPublicKeyRaw . $localPublicRaw;
        $prk = $hkdf($sharedSecret, $authKey, $authInfo, 32);

        // CEK and nonce
        $cekInfo   = "Content-Encryption-Key\x00";
        $nonceInfo = "Content-Encryption-Nonce\x00";
        $cek   = $hkdf($prk, $salt, $cekInfo, 16);
        $nonce = $hkdf($prk, $salt, $nonceInfo, 12);

        // Padding: RFC 8291 §4 — one \x02 delimiter byte, rest is ciphertext
        $padded = $plaintext . "\x02";

        // AES-128-GCM encrypt
        $tag        = '';
        $ciphertext = openssl_encrypt($padded, 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);
        $ciphertext .= $tag;

        // RFC 8291 binary header: salt(16) + rs(4 bytes big-endian, 4096) + keyid_len(1) + keyid(65 bytes uncompressed public key)
        $rs = pack('N', 4096);
        return $salt . $rs . chr(65) . $localPublicRaw . $ciphertext;
    }

    private function b64uDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
