<?php
$env = file_get_contents('/home/kegalle/app_core/.env');
function ev($k,$s){preg_match('/^'.preg_quote($k,'/').'=(.*)$/m',$s,$m);return isset($m[1])?trim($m[1]):'(not set)';}

echo "<pre>";
echo "=== Hutch SMS Config ===\n";
echo "HUTCH_SMS_URL      = ".ev('HUTCH_SMS_URL', $env)."\n";
echo "HUTCH_SMS_USERNAME = ".ev('HUTCH_SMS_USERNAME', $env)."\n";
echo "HUTCH_SMS_PASSWORD = ".(ev('HUTCH_SMS_PASSWORD',$env)!=='(not set)' ? '(set, '.strlen(ev('HUTCH_SMS_PASSWORD',$env)).' chars)' : '(not set)')."\n";
echo "HUTCH_SMS_SENDER_ID= ".ev('HUTCH_SMS_SENDER_ID', $env)."\n";

echo "\n=== phone_otps table ===\n";
$pdo = new PDO(
    "mysql:host=".ev('DB_HOST',$env).";dbname=".ev('DB_DATABASE',$env).";charset=utf8mb4",
    ev('DB_USERNAME',$env), ev('DB_PASSWORD',$env)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check table exists
try {
    $rows = $pdo->query("SELECT COUNT(*) FROM phone_otps")->fetchColumn();
    echo "phone_otps rows: $rows\n";
    $recent = $pdo->query("SELECT id,user_id,phone,code,expires_at,used,created_at FROM phone_otps ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    echo "Recent OTPs:\n";
    foreach($recent as $r) echo "  #{$r['id']} user={$r['user_id']} phone={$r['phone']} code={$r['code']} used={$r['used']} expires={$r['expires_at']} sent={$r['created_at']}\n";
} catch(\Throwable $e) {
    echo "ERROR: ".$e->getMessage()."\n";
    echo "Creating phone_otps table...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS phone_otps (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        phone VARCHAR(30) NOT NULL,
        code VARCHAR(10) NOT NULL,
        expires_at TIMESTAMP NOT NULL,
        used TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        INDEX idx_user (user_id),
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table created.\n";
}

// Live test: call Hutch API directly
echo "\n=== Live SMS API Test ===\n";
$url  = ev('HUTCH_SMS_URL', $env);
$user = ev('HUTCH_SMS_USERNAME', $env);
$pass = ev('HUTCH_SMS_PASSWORD', $env);
$sid  = ev('HUTCH_SMS_SENDER_ID', $env) ?: 'kegalle';

if($user === '(not set)' || $user === '') {
    echo "SKIP: HUTCH_SMS_USERNAME not configured in .env\n";
} else {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['username'=>$user,'password'=>$pass,'to'=>'94771234567','message'=>'kegalle test OTP: 123456','from'=>$sid]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "HTTP $http\n";
    echo "Response: ".($resp ?: "(empty)")."\n";
    if($err) echo "cURL error: $err\n";
}

@unlink(__FILE__);
echo "</pre>Done.";
