<?php
/**
 * Category-specific listing fields + blog slug fix
 * One-time guarded setup script — auto-locks after first run
 */
$lock = __DIR__.'/.setup_catfields.lock';
if (file_exists($lock)) {
    echo '<p style="font-family:monospace">Already ran on '.file_get_contents($lock).'. Delete .setup_catfields.lock to re-run.</p>';
    exit;
}

set_time_limit(120);
error_reporting(E_ERROR);

function parseEnv($path) {
    $vars = [];
    if (!file_exists($path)) return $vars;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $eq = strpos($line, '=');
        if ($eq === false) continue;
        $k = trim(substr($line, 0, $eq));
        $v = trim(substr($line, $eq + 1));
        if (strlen($v) >= 2 && (($v[0] === '"' && $v[-1] === '"') || ($v[0] === "'" && $v[-1] === "'"))) {
            $v = substr($v, 1, -1);
        }
        $vars[$k] = $v;
    }
    return $vars;
}

$env  = parseEnv(dirname(__DIR__).'/app_core/.env');
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]
    );
} catch (Exception $e) {
    die('<p style="color:red;font-family:monospace">DB connect failed: '.htmlspecialchars($e->getMessage()).'</p>');
}

$log = [];
function logOk($m)   { global $log; $log[] = ['ok',   $m]; }
function logInfo($m)  { global $log; $log[] = ['info', $m]; }

// ── 1. Create brands table ────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_group` varchar(60) DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), KEY `brands_group` (`category_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
logOk("brands table ready");

// ── 2. Create brand_models table ─────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS `brand_models` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int unsigned NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), KEY `bm_brand` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
logOk("brand_models table ready");

// ── 3. Seed brands ────────────────────────────────────────────
$now = date('Y-m-d H:i:s');
$brandCount = (int)$pdo->query("SELECT COUNT(*) FROM brands")->fetchColumn();
if ($brandCount === 0) {
    $brandData = [
        ['mobile','Samsung'],['mobile','Apple'],['mobile','Xiaomi'],['mobile','OPPO'],
        ['mobile','Vivo'],['mobile','Realme'],['mobile','OnePlus'],['mobile','Huawei'],
        ['mobile','Nokia'],['mobile','Motorola'],['mobile','Honor'],['mobile','Tecno'],
        ['mobile','Itel'],['mobile','Infinix'],
        ['computer','HP'],['computer','Dell'],['computer','Lenovo'],['computer','Asus'],
        ['computer','Acer'],['computer','Apple'],['computer','MSI'],['computer','Toshiba'],
        ['tv','Samsung'],['tv','LG'],['tv','Sony'],['tv','TCL'],
        ['tv','Hisense'],['tv','Panasonic'],['tv','Sharp'],['tv','Philips'],
        ['camera','Canon'],['camera','Nikon'],['camera','Sony'],
        ['camera','Fujifilm'],['camera','Olympus'],['camera','Panasonic'],
        ['vehicle','Toyota'],['vehicle','Honda'],['vehicle','Nissan'],['vehicle','Suzuki'],
        ['vehicle','Mitsubishi'],['vehicle','Mazda'],['vehicle','Hyundai'],['vehicle','Kia'],
        ['vehicle','Ford'],['vehicle','Isuzu'],['vehicle','Perodua'],['vehicle','Tata'],
        ['vehicle','BMW'],['vehicle','Mercedes-Benz'],
        ['vehicle_bike','Honda'],['vehicle_bike','Yamaha'],['vehicle_bike','Suzuki'],
        ['vehicle_bike','Bajaj'],['vehicle_bike','TVS'],['vehicle_bike','Hero'],
        ['vehicle_bike','Royal Enfield'],
        ['electronics','Samsung'],['electronics','LG'],['electronics','Sony'],
        ['electronics','Philips'],['electronics','Panasonic'],
        ['home','Samsung'],['home','LG'],['home','Singer'],['home','Panasonic'],
        ['home','Philips'],['home','Abans'],['home','Sharp'],
        ['furniture','IKEA'],['furniture','Ashley'],['furniture','Nilkamal'],
        ['pet','Royal Canin'],['pet','Pedigree'],['pet','Whiskas'],['pet','Purina'],
    ];
    $ins = $pdo->prepare("INSERT INTO brands (category_group,name,slug,sort_order,is_active,created_at,updated_at) VALUES (?,?,?,0,1,?,?)");
    foreach ($brandData as [$g, $n]) {
        $sl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $n));
        $ins->execute([$g, $n, $sl, $now, $now]);
    }
    logOk("Seeded ".count($brandData)." brands");

    // Seed models for top brands
    $brandIds = [];
    foreach ($pdo->query("SELECT id,name,category_group FROM brands") as $b) {
        $brandIds[$b['category_group'].':'.$b['name']] = (int)$b['id'];
    }
    $modelData = [
        'mobile:Samsung'  => ['Galaxy S24 Ultra','Galaxy S24','Galaxy S23','Galaxy A54','Galaxy A34','Galaxy A14','Galaxy M34','Galaxy S22'],
        'mobile:Apple'    => ['iPhone 15 Pro Max','iPhone 15 Pro','iPhone 15','iPhone 14 Pro Max','iPhone 14','iPhone 13','iPhone 12','iPhone 11','iPhone SE'],
        'mobile:Xiaomi'   => ['Redmi Note 13 Pro','Redmi Note 13','Redmi Note 12','Redmi 12','POCO X6 Pro','POCO M6 Pro','13T Pro'],
        'mobile:OPPO'     => ['Reno 11 Pro','Reno 10','A98','A78','A58','Find X7'],
        'mobile:Vivo'     => ['V30 Pro','V30','V29','Y100','Y56','Y35'],
        'mobile:Realme'   => ['GT 6','12 Pro+','12 Pro','Narzo 70 Pro','C67','C55'],
        'mobile:OnePlus'  => ['12','12R','11','Nord 3','Nord CE 3 Lite'],
        'mobile:Nokia'    => ['G42','G21','C32','C22','C12'],
        'vehicle:Toyota'  => ['Aqua','Prius','Axio','Allion','Vitz','Corolla','Hilux','Land Cruiser','Rush','Raize','Camry','Premio','Belta','Fielder'],
        'vehicle:Honda'   => ['Vezel','Fit','Grace','Civic','CR-V','Accord','Jazz','Freed'],
        'vehicle:Nissan'  => ['Leaf','Note','Dayz','X-Trail','Sunny','Tiida','March','Serena'],
        'vehicle:Suzuki'  => ['Alto','Swift','Baleno','Vitara','WagonR','Celerio','Dzire','Jimny'],
        'vehicle:Mitsubishi'=> ['Outlander','ASX','Eclipse Cross','Lancer','Montero','L200'],
        'vehicle:Hyundai' => ['i10','i20','Tucson','Creta','Elantra','Santa Fe'],
        'vehicle_bike:Honda'  => ['CB 150R','CB 125R','Shine','Activa','CD 110 Dream','Hornet 2.0'],
        'vehicle_bike:Yamaha' => ['FZ-S','FZS 25','R15 V4','MT-15','Ray ZR','Fascino'],
        'vehicle_bike:Bajaj'  => ['Pulsar NS200','Pulsar 150','Avenger 220','Platina','CT 100'],
        'vehicle_bike:TVS'    => ['Apache RTR 200 4V','Apache RTR 160 4V','Jupiter','XL 100','Ntorq 125'],
        'computer:HP'     => ['Pavilion 15','Victus 16','EliteBook 840','ProBook 450','Spectre x360'],
        'computer:Dell'   => ['Inspiron 15','XPS 15','Latitude 5540','Vostro 3520','G15 Gaming'],
        'computer:Lenovo' => ['ThinkPad E15','IdeaPad Slim 5','Legion 5','Yoga 7','ThinkBook 14'],
        'tv:Samsung'      => ['Crystal 4K UHD','Frame TV','Neo QLED','QLED Q80C','TU7000'],
        'tv:LG'           => ['OLED C3','OLED B3','QNED90','UQ80','UQ75'],
        'tv:Sony'         => ['Bravia XR A80L','X95L','X90L','X85L','X80L'],
    ];
    $ins2 = $pdo->prepare("INSERT INTO brand_models (brand_id,name,slug,sort_order,is_active,created_at,updated_at) VALUES (?,?,?,0,1,?,?)");
    $mc = 0;
    foreach ($modelData as $key => $names) {
        if (!isset($brandIds[$key])) continue;
        foreach ($names as $mn) {
            $sl = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $mn));
            $ins2->execute([$brandIds[$key], $mn, $sl, $now, $now]);
            $mc++;
        }
    }
    logOk("Seeded $mc brand models");
} else {
    logInfo("Brands already seeded ($brandCount rows) — skipping");
}

// ── 4. Get categories by slug ─────────────────────────────────
$catBySlug = [];
foreach ($pdo->query("SELECT id,slug FROM categories WHERE is_active=1") as $c) {
    $catBySlug[$c['slug']] = (int)$c['id'];
}
logInfo("Found ".count($catBySlug)." active categories: ".implode(', ', array_keys($catBySlug)));

// ── 5. Create / get custom fields ─────────────────────────────
function gcf($pdo, $name, $label, $type, $opts, $ph='') {
    $now = date('Y-m-d H:i:s');
    $st = $pdo->prepare("SELECT id FROM custom_fields WHERE name=? LIMIT 1");
    $st->execute([$name]);
    if ($r = $st->fetch(PDO::FETCH_ASSOC)) return (int)$r['id'];
    $pdo->prepare("INSERT INTO custom_fields (name,label,type,options,placeholder,is_required,is_searchable,sort_order,created_at,updated_at) VALUES (?,?,?,?,?,0,0,0,?,?)")
        ->execute([$name, $label, $type, json_encode($opts), $ph, $now, $now]);
    return (int)$pdo->lastInsertId();
}

function linkCF($pdo, $fid, $cid, $sort) {
    $st = $pdo->prepare("SELECT 1 FROM category_custom_field WHERE category_id=? AND custom_field_id=?");
    $st->execute([$cid, $fid]);
    if ($st->fetchColumn()) return;
    $pdo->prepare("INSERT INTO category_custom_field (category_id,custom_field_id,is_required,show_in_filter,show_in_list,sort_order) VALUES (?,?,0,0,1,?)")
        ->execute([$cid, $fid, $sort]);
}

// Shared fields
$FC = gcf($pdo,'condition','Condition','select',['Brand New','Like New','Good','Fair','For Parts'],'Select condition');
$FB = gcf($pdo,'brand_id','Brand','brand_select',[],'Select brand');
$FM = gcf($pdo,'model_id','Model','model_select',[],'Select model');
$FRAM = gcf($pdo,'ram','RAM','select',['1 GB','2 GB','3 GB','4 GB','6 GB','8 GB','12 GB','16 GB','32 GB']);
$FSTO = gcf($pdo,'storage','Storage','select',['8 GB','16 GB','32 GB','64 GB','128 GB','256 GB','512 GB','1 TB']);
$FNET = gcf($pdo,'network','Network','select',['2G','3G','4G','5G']);
$FSS  = gcf($pdo,'screen_size','Screen Size','text',[],'e.g. 6.7 inches');
$FCAM = gcf($pdo,'camera','Camera','text',[],'e.g. 50MP + 12MP');
$FBAT = gcf($pdo,'battery','Battery','text',[],'e.g. 5000 mAh');
$FFEA = gcf($pdo,'features','Key Features','checkbox_group',
    ['USB-C','Fast Charging','Wireless Charging','5G','Fingerprint','Face ID','NFC','Dual SIM','Water Resistant','AMOLED']);
$FIT  = gcf($pdo,'item_type','Item Type','select',[],'Describe the item type');
$FMAK = gcf($pdo,'make_id','Make','brand_select',[],'Select make');
$FVM  = gcf($pdo,'vehicle_model_id','Model','model_select',[],'Select model');
$FYR  = gcf($pdo,'year','Year','select', array_map('strval', range((int)date('Y'), 1990)));
$FMI  = gcf($pdo,'mileage','Mileage (km)','number',[],'e.g. 45000');
$FECC = gcf($pdo,'engine_cc','Engine (cc)','text',[],'e.g. 1500cc');
$FFU  = gcf($pdo,'fuel_type','Fuel Type','select',['Petrol','Diesel','Hybrid','Electric','LPG']);
$FTR  = gcf($pdo,'transmission','Transmission','select',['Automatic','Manual','CVT','Semi-Automatic']);
$FBT  = gcf($pdo,'body_type','Body Type','select',['Sedan','Hatchback','SUV','Van','Pickup','Wagon','Coupe','Bus','Lorry','Truck']);
$FCOL = gcf($pdo,'color','Color','text',[],'e.g. Silver');
$FPT  = gcf($pdo,'property_type','Property Type','select',['For Sale','For Rent','Lease']);
$FADR = gcf($pdo,'address','Full Address','text',[],'Street / Area');
$FLSZ = gcf($pdo,'land_size','Size','text',[],'e.g. 20 perches');
$FBED = gcf($pdo,'bedrooms','Bedrooms','select',['Studio','1','2','3','4','5','6+']);
$FBTH = gcf($pdo,'bathrooms','Bathrooms','select',['1','2','3','4+']);
$FOWN = gcf($pdo,'ownership','Ownership','select',['Deeds','Permit','Grant','UDA','Condominium']);

// Map: category_slug => [fieldId, ...]
$map = [
    'mobile-phones'              => [$FC,$FB,$FM,$FRAM,$FSTO,$FNET,$FSS,$FCAM,$FBAT,$FFEA],
    'smartphones'                => [$FC,$FB,$FM,$FRAM,$FSTO,$FNET,$FSS,$FCAM,$FBAT,$FFEA],
    'feature-phones'             => [$FC,$FB,$FM,$FNET,$FBAT],
    'tablets'                    => [$FC,$FB,$FM,$FRAM,$FSTO,$FSS],
    'mobile-accessories'         => [$FC,$FB,$FIT],
    'mobile-spare-parts'         => [$FC,$FB,$FIT],
    'smart-watches'              => [$FC,$FB,$FM,$FNET],
    'smart-products'             => [$FC,$FB,$FIT],
    'computers-laptops-tablets'  => [$FC,$FB,$FM,$FRAM,$FSTO,$FSS],
    'computers'                  => [$FC,$FB,$FM,$FRAM,$FSTO],
    'laptops'                    => [$FC,$FB,$FM,$FRAM,$FSTO,$FSS],
    'computer-accessories'       => [$FC,$FB,$FIT],
    'tv'                         => [$FC,$FB,$FM,$FSS],
    'tv-audio'                   => [$FC,$FB,$FM,$FSS],
    'tv-accessories'             => [$FC,$FB,$FIT],
    'audio-mp3'                  => [$FC,$FB,$FIT],
    'camera'                     => [$FC,$FB,$FM],
    'cameras'                    => [$FC,$FB,$FM],
    'electronic-home-appliances' => [$FC,$FB,$FIT],
    'video-games-other-electronics'=> [$FC,$FB,$FIT],
    'aircon-fittings'            => [$FC,$FB,$FIT],
    'electronics'                => [$FC,$FB,$FIT],
    'networking'                 => [$FC,$FB,$FIT],
    'gaming'                     => [$FC,$FB,$FIT],
    'smart-home'                 => [$FC,$FB,$FIT],
    'large-appliances'           => [$FC,$FB,$FIT],
    'kitchen-appliances'         => [$FC,$FB,$FIT],
    'small-appliances'           => [$FC,$FB,$FIT],
    'home-appliances'            => [$FC,$FB,$FIT],
    'cars'                       => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FTR,$FBT,$FECC,$FCOL],
    'suvs-jeeps'                 => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FTR,$FECC,$FCOL],
    'vans'                       => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FTR,$FCOL],
    'pickups'                    => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FTR,$FCOL],
    'buses'                      => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FCOL],
    'trucks-lorries'             => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FCOL],
    'lorries'                    => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FCOL],
    'three-wheelers'             => [$FC,$FMAK,$FVM,$FYR,$FMI,$FFU,$FCOL],
    'tractors'                   => [$FC,$FMAK,$FVM,$FYR,$FMI,$FCOL],
    'tractor'                    => [$FC,$FMAK,$FVM,$FYR,$FMI,$FCOL],
    'heavy-machinery'            => [$FC,$FMAK,$FVM,$FYR,$FMI],
    'electric-vehicles'          => [$FC,$FMAK,$FVM,$FYR,$FMI,$FTR,$FCOL],
    'motorcycles'                => [$FC,$FMAK,$FVM,$FYR,$FMI,$FECC,$FCOL],
    'bikes'                      => [$FC,$FMAK,$FVM,$FYR,$FMI,$FECC,$FCOL],
    'bicycles'                   => [$FC,$FB,$FIT,$FCOL],
    'bicycle'                    => [$FC,$FB,$FIT,$FCOL],
    'boats-watercraft'           => [$FC,$FB,$FM,$FYR],
    'boats'                      => [$FC,$FB,$FM,$FYR],
    'land'                       => [$FPT,$FADR,$FLSZ,$FOWN],
    'commercial-property'        => [$FPT,$FADR,$FLSZ,$FOWN],
    'houses'                     => [$FPT,$FADR,$FBED,$FBTH,$FLSZ,$FOWN],
    'house'                      => [$FPT,$FADR,$FBED,$FBTH,$FLSZ,$FOWN],
    'apartments'                 => [$FPT,$FADR,$FBED,$FBTH,$FOWN],
    'apartment'                  => [$FPT,$FADR,$FBED,$FBTH,$FOWN],
    'furniture'                  => [$FC,$FB,$FIT,$FCOL],
    'kitchen-items'              => [$FC,$FB,$FIT],
    'kitchen-dining'             => [$FC,$FB,$FIT],
    'bathrooms'                  => [$FC,$FB,$FIT],
    'garden'                     => [$FC,$FB,$FIT],
    'decor'                      => [$FC,$FIT,$FCOL],
    'other-items'                => [$FC,$FIT],
    'pet-food'                   => [$FB,$FIT],
    'animal-accessories'         => [$FC,$FB,$FIT],
];

$linked = 0; $skipped = 0;
foreach ($map as $slug => $fields) {
    if (!isset($catBySlug[$slug])) { $skipped++; continue; }
    $cid = $catBySlug[$slug];
    foreach ($fields as $i => $fid) { linkCF($pdo, $fid, $cid, $i); $linked++; }
}
logOk("Linked $linked field-category pairs ($skipped category slugs not found in DB — OK)");

// ── 6. Fix blog slug ──────────────────────────────────────────
try {
    $n = $pdo->exec("UPDATE posts SET slug=REPLACE(slug,'2025','2026'), updated_at=NOW() WHERE slug LIKE '%2025%'");
    if ($n > 0) logOk("Fixed $n blog post slug(s): 2025 → 2026");
    else logInfo("Blog: no 2025 slugs found (already correct or table not available)");
} catch (Exception $e) {
    logInfo("Blog slug: ".$e->getMessage());
}

// ── Done ──────────────────────────────────────────────────────
file_put_contents($lock, date('c'));
?><!DOCTYPE html>
<html><head><title>Setup Complete</title>
<style>body{font-family:monospace;max-width:700px;margin:40px auto;padding:20px;background:#f7f8fa}
h2{color:#1B5E20}.ok{color:#2e7d32;margin:4px 0}.info{color:#666;margin:4px 0}.err{color:#c62828;margin:4px 0}</style>
</head><body>
<h2>✅ Setup Complete</h2>
<?php foreach ($log as [$t, $m]): ?>
<p class="<?= $t ?>">
  <?= $t==='ok' ? '✓' : ($t==='err' ? '✗' : 'ℹ') ?> <?= htmlspecialchars($m) ?>
</p>
<?php endforeach; ?>
<p style="margin-top:24px;color:#888;font-size:12px">Script locked. Delete <code>.setup_catfields.lock</code> from public_html to re-run.</p>
</body></html>
