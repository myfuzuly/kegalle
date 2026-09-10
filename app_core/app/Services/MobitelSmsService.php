<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class MobitelSmsService
{
    private const API_URL = 'https://msmsenterpriseapi.mobitel.lk/EnterpriseSMSV3/esmsproxyURL.php';

    private string $username;
    private string $password;
    private string $alias;

    public function __construct()
    {
        $this->username = config('services.mobitel_sms.username', '');
        $this->password = config('services.mobitel_sms.password', '');
        $this->alias    = config('services.mobitel_sms.alias', 'KEGALLE.COM');
    }

    public function send(string $to, string $message): bool
    {
        $to = $this->normalizePhone($to);

        if (empty($this->username) || empty($this->password)) {
            Log::warning('MobitelSmsService: credentials not configured. Would send to ' . $to);
            return true;
        }

        if (empty($to)) {
            Log::error('MobitelSmsService: empty phone number');
            return false;
        }

        $message = mb_substr($message, 0, 160);

        try {
            $payload = json_encode([
                'username'   => $this->username,
                'password'   => $this->password,
                'from'       => $this->alias,
                'to'         => $to,
                'text'       => $message,
                'mesageType' => 0,
            ]);

            $ch = curl_init(self::API_URL);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $err      = curl_error($ch);
            curl_close($ch);

            if ($err) {
                Log::error('MobitelSmsService curl error: ' . $err);
                return false;
            }

            $data = json_decode($response, true);
            $code = $data['resultcode'] ?? null;

            if ($code == '200') {
                Log::info('MobitelSmsService: SMS sent to ' . $to);
                return true;
            }

            Log::error('MobitelSmsService: API returned code ' . $code . ' for ' . $to . ' — ' . ($data['response'] ?? ''));
            return false;

        } catch (\Exception $e) {
            Log::error('MobitelSmsService exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '94')) {
            $phone = '0' . substr($phone, 2);
        } elseif (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }
        return $phone;
    }
}
