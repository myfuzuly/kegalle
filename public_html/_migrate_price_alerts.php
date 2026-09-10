<?php
$env = file_get_contents('/home/kegalle/app_core/.env');
function ev($k,$s){preg_match('/^'.preg_quote($k,'/').'=(.*)$/m',$s,$m);return trim($m[1]??'');}
$pdo = new PDO(
    "mysql:host=".ev('DB_HOST',$env).";dbname=".ev('DB_DATABASE',$env).";charset=utf8mb4",
    ev('DB_USERNAME',$env), ev('DB_PASSWORD',$env)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS price_alerts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  listing_id BIGINT UNSIGNED NOT NULL,
  price_when_set DECIMAL(12,2) NOT NULL DEFAULT 0,
  notified_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY unique_alert (user_id, listing_id),
  KEY idx_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo "price_alerts table ready.\n";
@unlink(__FILE__);
echo "Done.";
