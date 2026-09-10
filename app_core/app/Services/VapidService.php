<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class VapidService
{
    private function b64u(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64uDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Generate a P-256 VAPID key pair.
     * Returns ['public_key' => base64url, 'private_pem' => PEM string]
     */
    public function generateKeys(): array
    {
        $key = openssl_pkey_new([
            'curve_name'       => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        if (!$key) {
            throw new \RuntimeException('Could not generate EC key: ' . openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);
        $pubRaw  = "\x04"
            . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
            . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);

        openssl_pkey_export($key, $privatePem);

        return [
            'public_key'  => $this->b64u($pubRaw),
            'private_pem' => $privatePem,
        ];
    }

    /**
     * Build the VAPID JWT for a given push endpoint.
     */
    public function buildJwt(string $endpoint): string
    {
        $privateKeyPem = config('services.webpush.private_key');
        $subject       = config('services.webpush.subject', 'mailto:admin@kegalle.com');

        if (empty($privateKeyPem)) {
            throw new \RuntimeException('WEBPUSH_PRIVATE_KEY not configured.');
        }

        $parts    = parse_url($endpoint);
        $audience = $parts['scheme'] . '://' . $parts['host'];

        $header  = $this->b64u(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = $this->b64u(json_encode([
            'aud' => $audience,
            'exp' => time() + 43200,
            'sub' => $subject,
        ]));

        $signing    = "$header.$payload";
        $privateKey = openssl_pkey_get_private($privateKeyPem);

        if (!$privateKey) {
            throw new \RuntimeException('Invalid VAPID private key.');
        }

        openssl_sign($signing, $derSig, $privateKey, OPENSSL_ALGO_SHA256);

        return "$signing." . $this->b64u($this->derToRaw($derSig));
    }

    /**
     * Convert DER-encoded ECDSA signature → raw R || S (64 bytes for P-256).
     */
    private function derToRaw(string $der): string
    {
        $idx = 2;
        // Handle multi-byte total length
        if (ord($der[1]) >= 0x80) {
            $idx += ord($der[1]) & 0x7f;
        }

        // Read R
        $idx++; // skip 0x02
        $rLen = ord($der[$idx++]);
        if ($rLen >= 0x80) { $rLen = ord($der[$idx++]); }
        $r = substr($der, $idx, $rLen);
        $idx += $rLen;

        // Read S
        $idx++; // skip 0x02
        $sLen = ord($der[$idx++]);
        if ($sLen >= 0x80) { $sLen = ord($der[$idx++]); }
        $s = substr($der, $idx, $sLen);

        return str_pad(ltrim($r, "\x00"), 32, "\x00", STR_PAD_LEFT)
             . str_pad(ltrim($s, "\x00"), 32, "\x00", STR_PAD_LEFT);
    }
}
