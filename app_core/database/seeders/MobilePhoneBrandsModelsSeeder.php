<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\BrandModel;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobilePhoneBrandsModelsSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'mobile-phones-tablets-2015')->first();
        if (!$category) {
            $this->command->error('Category mobile-phones-tablets-2015 not found.');
            return;
        }

        $data = $this->getBrandData();
        $brandCount = 0;
        $modelCount = 0;

        foreach ($data as $idx => $entry) {
            $brand = Brand::updateOrCreate(
                ['name' => $entry['brand']],
                [
                    'slug'           => Str::slug($entry['brand']),
                    'sort_order'     => $idx,
                    'is_active'      => true,
                    'category_group' => '',
                ]
            );
            $brandCount++;

            // Link to mobile phones category
            if (!DB::table('brand_category')
                    ->where('brand_id', $brand->id)
                    ->where('category_id', $category->id)
                    ->exists()) {
                DB::table('brand_category')->insert([
                    'brand_id'    => $brand->id,
                    'category_id' => $category->id,
                ]);
            }

            // Upsert models
            foreach ($entry['models'] as $mIdx => $modelName) {
                BrandModel::updateOrCreate(
                    ['brand_id' => $brand->id, 'name' => $modelName],
                    [
                        'slug'       => Str::slug($modelName),
                        'sort_order' => $mIdx,
                        'is_active'  => true,
                    ]
                );
                $modelCount++;
            }

            $this->command->line("  {$entry['brand']}: " . count($entry['models']) . ' models');
        }

        $this->command->info("Done — {$brandCount} brands, {$modelCount} models seeded for '{$category->name}'.");
    }

    private function getBrandData(): array
    {
        return [
            ['brand' => 'Samsung', 'models' => [
                'A Quantum','B110','B110E','B310','B310E','B315E','Buddy',
                'Galaxy A01','Galaxy A02','Galaxy A02 Core','Galaxy A02s',
                'Galaxy A03','Galaxy A03 Core','Galaxy A03s','Galaxy A04','Galaxy A04e','Galaxy A04s',
                'Galaxy A05','Galaxy A05s','Galaxy A06','Galaxy A07',
                'Galaxy A10','Galaxy A10e','Galaxy A10s','Galaxy A11','Galaxy A12','Galaxy A13','Galaxy A14',
                'Galaxy A15','Galaxy A15L','Galaxy A16','Galaxy A17','Galaxy A2 Core',
                'Galaxy A20','Galaxy A20e','Galaxy A20s','Galaxy A21s','Galaxy A22','Galaxy A23','Galaxy A24',
                'Galaxy A25','Galaxy A26','Galaxy A30','Galaxy A30s','Galaxy A31','Galaxy A32','Galaxy A33',
                'Galaxy A34','Galaxy A35','Galaxy A36','Galaxy A40','Galaxy A41','Galaxy A42',
                'Galaxy A50','Galaxy A50s','Galaxy A51','Galaxy A52','Galaxy A52s','Galaxy A53','Galaxy A54',
                'Galaxy A55','Galaxy A56','Galaxy A60','Galaxy A70','Galaxy A70s','Galaxy A71','Galaxy A72',
                'Galaxy A73','Galaxy A80','Galaxy A81','Galaxy A82','Galaxy A8+','Galaxy A8 Star',
                'Galaxy A9','Galaxy A9 Pro',
                'Galaxy Ace','Galaxy Active Neo','Galaxy Alpha',
                'Galaxy C7','Galaxy C9 Pro','Galaxy Core','Galaxy Core Prime',
                'Galaxy F04','Galaxy F05','Galaxy F06','Galaxy F07','Galaxy F13','Galaxy F15','Galaxy F16',
                'Galaxy F17','Galaxy F23','Galaxy F36','Galaxy F42','Galaxy F54','Galaxy F55','Galaxy Fold',
                'Galaxy Grand','Galaxy Grand Prime',
                'Galaxy J1','Galaxy J2','Galaxy J2 Core','Galaxy J3','Galaxy J4','Galaxy J4+','Galaxy J5',
                'Galaxy J5 Prime','Galaxy J6','Galaxy J6+','Galaxy J7','Galaxy J7 Prime','Galaxy J7 Pro',
                'Galaxy J8','Galaxy Jump',
                'Galaxy M01','Galaxy M01 Core','Galaxy M01s','Galaxy M02','Galaxy M02s','Galaxy M03','Galaxy M04',
                'Galaxy M05','Galaxy M06','Galaxy M07','Galaxy M10','Galaxy M11','Galaxy M12','Galaxy M13',
                'Galaxy M14','Galaxy M15','Galaxy M16','Galaxy M17','Galaxy M20','Galaxy M21','Galaxy M21s',
                'Galaxy M22','Galaxy M23','Galaxy M30s','Galaxy M31','Galaxy M31 Prime','Galaxy M32','Galaxy M33',
                'Galaxy M34','Galaxy M36','Galaxy M40','Galaxy M42','Galaxy M44','Galaxy M51','Galaxy M52',
                'Galaxy M53','Galaxy M54','Galaxy M55','Galaxy M55s','Galaxy M56',
                'Galaxy Note','Galaxy Note II','Galaxy Note 3','Galaxy Note 4','Galaxy Note 5','Galaxy Note 7',
                'Galaxy Note 8','Galaxy Note 9','Galaxy Note10','Galaxy Note10+','Galaxy Note20',
                'Galaxy Note20 Ultra','Galaxy Note Edge','Galaxy Note FE',
                'Galaxy On5','Galaxy On7','Galaxy On Max',
                'Galaxy Quantum','Galaxy Quantum 2','Galaxy Quantum 3','Galaxy Quantum 4','Galaxy Quantum 5',
                'Galaxy S3','Galaxy S3 Mini','Galaxy S4','Galaxy S4 Mini','Galaxy S5','Galaxy S5 Mini',
                'Galaxy S6','Galaxy S6 Edge','Galaxy S6 Edge+','Galaxy S7','Galaxy S7 Edge',
                'Galaxy S8','Galaxy S8+','Galaxy S9','Galaxy S9+',
                'Galaxy S10','Galaxy S10+','Galaxy S10 Lite','Galaxy S10e',
                'Galaxy S20','Galaxy S20 FE','Galaxy S20 FE 5G','Galaxy S20+','Galaxy S20 Ultra',
                'Galaxy S21','Galaxy S21 FE','Galaxy S21 FE 5G','Galaxy S21+','Galaxy S21 Ultra',
                'Galaxy S22','Galaxy S22 Plus','Galaxy S22 Ultra',
                'Galaxy S23','Galaxy S23 FE','Galaxy S23 Plus','Galaxy S23 Ultra',
                'Galaxy S24','Galaxy S24 FE','Galaxy S24 Plus','Galaxy S24 Ultra',
                'Galaxy S25','Galaxy S25 Edge','Galaxy S25 FE','Galaxy S25 Plus','Galaxy S25 Ultra',
                'Galaxy S26','Galaxy S26 Plus','Galaxy S26 Ultra',
                'Galaxy Wide 7','Galaxy XCover 4','Galaxy XCover 5','Galaxy XCover Pro',
                'Galaxy Z Flip','Galaxy Z Flip 5G','Galaxy Z Flip3','Galaxy Z Flip4','Galaxy Z Flip5',
                'Galaxy Z Flip6','Galaxy Z Flip7','Galaxy Z Flip7 FE',
                'Galaxy Z Fold','Galaxy Z Fold2','Galaxy Z Fold3','Galaxy Z Fold4',
                'Galaxy Z Fold5','Galaxy Z Fold6','Galaxy Z Fold7',
                'Other Model',
            ]],
            ['brand' => 'Xiaomi', 'models' => [
                '11 Lite NE','11T','11T Pro','12 Pro','12C','13 Lite','13 Pro','15T Pro',
                'Black Shark 3S','Civi',
                'Mi 5','Mi 9','Mi 9T','Mi 9T Pro','Mi 10i','Mi 11','Mi 11 Lite','Mi 11 Ultra',
                'Mi A2 Lite','Mi A3','Mi Max','Mi Note','Mi Note 3','Mi Note 10',
                'Poco C55','Poco C71','Poco F3','Poco F5','Poco F6','Poco F7 Pro',
                'Poco M2 Pro','Poco M3','Poco M3 Pro','Poco M6','Poco M6 Pro','Poco M7','Poco M7 Pro','Poco M8',
                'Poco X3','Poco X3 NFC','Poco X3 Pro','Poco X4 Pro','Poco X5','Poco X5 Pro',
                'Poco X6','Poco X6 Neo','Poco X6 Pro','Poco X7','Poco X7 Pro','Pocophone F1',
                'Redmi 2','Redmi 5','Redmi 5A','Redmi 6','Redmi 6A','Redmi 8','Redmi 8A',
                'Redmi 9','Redmi 9 Activ','Redmi 9 Power','Redmi 9A','Redmi 9C','Redmi 9T',
                'Redmi 10','Redmi 10 Prime','Redmi 10A','Redmi 10C',
                'Redmi 11','Redmi 11 Prime','Redmi 12','Redmi 12C','Redmi 12R',
                'Redmi 13','Redmi 13C','Redmi 13R','Redmi 14','Redmi 14C',
                'Redmi 15','Redmi 15C',
                'Redmi A1','Redmi A1+','Redmi A2','Redmi A2 Plus','Redmi A3','Redmi A4','Redmi A5','Redmi Go',
                'Redmi Note','Redmi Note 2','Redmi Note 5','Redmi Note 6 Pro',
                'Redmi Note 7','Redmi Note 7 Pro','Redmi Note 8','Redmi Note 8 Pro',
                'Redmi Note 9','Redmi Note 9 Pro','Redmi Note 9S',
                'Redmi Note 10','Redmi Note 10 Pro','Redmi Note 10 Pro Max','Redmi Note 10S','Redmi Note 10T',
                'Redmi Note 11','Redmi Note 11 Pro','Redmi Note 11 Pro+','Redmi Note 11E',
                'Redmi Note 11R','Redmi Note 11S','Redmi Note 11T',
                'Redmi Note 12','Redmi Note 12 Pro','Redmi Note 12 Pro+','Redmi Note 12R',
                'Redmi Note 13','Redmi Note 13 Pro','Redmi Note 13 Pro+',
                'Redmi Note 14','Redmi Note 14 Pro','Redmi Note 14 Pro+','Redmi Note 14S',
                'Redmi Note 15','Redmi Note 15 Pro','Redmi Note 15 Pro+',
                'Xiaomi 12','Xiaomi 12 Lite','Xiaomi 12 Pro',
                'Xiaomi 13','Xiaomi 13 Lite','Xiaomi 13 Pro','Xiaomi 13 Ultra',
                'Xiaomi 14','Xiaomi 14 Pro','Xiaomi 14 Ultra',
                'Xiaomi 15','Xiaomi 15 Pro','Xiaomi 15 Ultra',
                'Xiaomi 17','Xiaomi 17 Pro','Xiaomi 17 Ultra',
                'Other Model',
            ]],
            ['brand' => 'Huawei', 'models' => [
                'Ascend G6','Ascend G7','Ascend Mate 7','Ascend P7',
                'Enjoy 10','Enjoy 20 Pro','Enjoy 50','Enjoy 60','Enjoy 70',
                'GR3','GR5',
                'Honor 10','Honor 20','Honor 50','Honor 5C','Honor 5X','Honor 6C','Honor 70','Honor 7C','Honor 8X','Honor 90',
                'Mate 9','Mate 10','Mate 10 Pro','Mate 20','Mate 20 Pro','Mate 20 X',
                'Mate 30','Mate 30 Pro','Mate 30E Pro','Mate 40','Mate 40 Pro',
                'Mate 50 Pro','Mate 60 Pro','Mate X2','Mate X3','Mate Xs',
                'Nova','Nova 2i','Nova 3i','Nova 4','Nova 5T','Nova 7 SE','Nova 7i',
                'Nova 8','Nova 9','Nova 9 SE','Nova 10','Nova 11','Nova 12',
                'Nova Y61','Nova Y70','Nova Y90',
                'P8','P9','P9 Plus','P10','P10 Plus','P20','P20 Pro',
                'P30 Lite','P30 Pro','P40','P40 Pro','P40 Pro+',
                'P50 Pocket','P50 Pro','P60 Pro','P70 Pro',
                'P Smart','P Smart 2021','P Smart Pro','P Smart Z',
                'Y3','Y5','Y5 Lite','Y5 Prime','Y50','Y5p',
                'Y6','Y60','Y6p','Y6s','Y7','Y70','Y7 Pro','Y7a','Y7p',
                'Y80','Y8p','Y9','Y90','Y9 Prime','Y9s',
                'Other Model',
            ]],
            ['brand' => 'Vivo', 'models' => [
                'S5','S6','S7','S9','S10','S12','S15','S16','S17','S18','S19',
                'T1x','T2x','T2 Pro','T3','T3 Pro','T4','T4 Ultra',
                'V9','V9 Pro','V11','V11 Pro','V15','V15 Pro','V17','V19','V19 Neo',
                'V25 Pro','V25e','V29','V29 Pro','V30','V30 Pro','V40 Lite','V50 Lite','V50e',
                'X50','X50 Pro','X60 Pro','X70','X70 Pro','X80','X80 Pro',
                'X100','X200','X200 Pro Mini','X200 Ultra',
                'X Flip','X Fold','X Fold+','X Fold2','X Fold3','X Fold3 Pro',
                'Y01','Y01A','Y02A','Y03t','Y04e','Y11 (2023)','Y12','Y12A',
                'Y15','Y15A','Y18','Y18e','Y18i','Y20A','Y20G','Y20s',
                'Y21A','Y21e','Y22s','Y27','Y27s','Y35',
                'Other Model',
            ]],
            ['brand' => 'Honor', 'models' => [
                '8','9','9 Lite','10','10 Lite','20','20 Pro',
                '50','50 Pro','70','70 Pro','80','80 Pro','90','90 Lite','90 Pro',
                '100','100 Pro','200','200 Pro','400','400 Pro','500','500 Pro',
                '5C','5X','6C','7C','7X','8X',
                'Magic3 Lite','Magic3 Pro','Magic4 Lite','Magic4 Pro',
                'Magic5 Lite','Magic5 Pro','Magic6 Lite','Magic6 Pro',
                'Magic7 Lite','Magic7 Pro',
                'Note 8','Note 10',
                'Play 4','Play 5T','Play 6T','Play 7T','Play 8T',
                'View 10','View 10 Lite','View 20',
                'X5','X5 Plus','X5b','X6','X6b','X6c','X7a','X7b','X7c',
                'X8','X8b','X9','X9a','X9b','X9c',
                'Other Model',
            ]],
            ['brand' => 'Infinix', 'models' => [
                'GT 10 Pro','GT 20 Pro','GT 30 Pro',
                'Hot 6','Hot 7','Hot 8','Hot 9 Play','Hot 10 Play','Hot 10i','Hot 10s',
                'Hot 11 Play','Hot 11s','Hot 12','Hot 12 Play',
                'Hot 20','Hot 20i','Hot 30 Pro','Hot 30i','Hot 40 Pro','Hot 40i',
                'Hot 50 Pro','Hot 50 Pro+','Hot 50i',
                'Hot 60 Pro','Hot 60 Pro+','Hot 60i',
                'Note 8','Note 10','Note 10 Pro','Note 11','Note 11 Pro','Note 12','Note 12 Pro',
                'Note 30','Note 30 Pro','Note 40','Note 40 Pro','Note 40 Pro+',
                'Note 50','Note 50 Pro','Note 50 Pro+',
                'S3','S4','S5','S5 Pro',
                'Smart 4','Smart 5','Smart 6','Smart 7','Smart 8','Smart 9','Smart 9 HD','Smart 10','Smart 10 Plus','Smart HD',
                'Zero 8','Zero 30','Zero 30 5G','Zero 40','Zero Ultra','Zero X','Zero X Pro',
                'Other Model',
            ]],
            ['brand' => 'Sony', 'models' => [
                'Xperia 1','Xperia 1 II','Xperia 1 III','Xperia 1 IV','Xperia 1 V',
                'Xperia 5','Xperia 5 II','Xperia 5 III','Xperia 5 IV','Xperia 5 V',
                'Xperia 8','Xperia 8 Lite',
                'Xperia 10','Xperia 10 II','Xperia 10 III','Xperia 10 IV','Xperia 10 V',
                'Xperia C','Xperia C3','Xperia C4','Xperia C5 Ultra',
                'Xperia E3','Xperia E4','Xperia E5',
                'Xperia L1','Xperia L2','Xperia L3','Xperia L4',
                'Xperia M2','Xperia M4 Aqua','Xperia M5',
                'Xperia Pro','Xperia Pro-I',
                'Xperia T','Xperia T2 Ultra','Xperia T3',
                'Xperia X','Xperia X Compact','Xperia X Performance',
                'Xperia XA1','Xperia XA1 Plus','Xperia XA1 Ultra',
                'Xperia XA2','Xperia XA2 Ultra',
                'Xperia XZ','Xperia XZ Premium',
                'Xperia XZ1','Xperia XZ1 Compact',
                'Xperia XZ2','Xperia XZ2 Compact','Xperia XZ2 Premium',
                'Xperia XZ3','Xperia XZs',
                'Xperia Z1','Xperia Z1 Compact',
                'Xperia Z2','Xperia Z3','Xperia Z3 Compact','Xperia Z3+',
                'Xperia Z5','Xperia Z5 Compact','Xperia Z5 Premium',
                'Other Model',
            ]],
            ['brand' => 'Realme', 'models' => [
                '5','5 Pro','7','7 Pro','8','8 Pro',
                '9','9 Pro','9 Pro+','9R',
                '10','10 Pro','10 Pro+','10R',
                '11','11 Pro','11 Pro+','11R',
                '12','12 Pro','12 Pro+',
                'C11','C12','C15','C20','C21','C21Y','C25','C30s','C31','C32','C33','C35',
                'C51','C53','C55','C61','C65','C67',
                'GT','GT 2 Pro','GT 3','GT 5','GT 6',
                'GT Master Edition','GT Neo','GT Neo 2','GT Neo 3','GT Neo 5','GT Neo 6',
                'Narzo 20','Narzo 30','Narzo 50 Pro','Narzo 50i','Narzo 60','Narzo 70',
                'X2 Pro','X7','X7 Max','X7 Pro',
                'Other Model',
            ]],
            ['brand' => 'Nokia', 'models' => [
                '1','1 Plus','1.4','1110','1280',
                '105','150','215','216',
                '2','2.1','2.2','2.3','2.4',
                '3','3.1','3.2','3.4','3310 (2017)',
                '4.2','5.1','5.3','5.4',
                '6.1','6.1 Plus','6.2',
                '7 Plus','7.2','8','8 Sirocco','8.1',
                '9 PureView',
                'Asha',
                'C01 Plus','C2','C3','C10','C20','C21','C21 Plus','C30','C31','C32',
                'Eseries',
                'G10','G11','G11 Plus','G20','G50','G60','G100','G400',
                'X10','X20','X30',
                'Other Model',
            ]],
            ['brand' => 'OnePlus', 'models' => [
                'One','X',
                '3','3T','5','5T','6','6T',
                '7','7 Pro','7T','7T Pro',
                '8','8 Pro','9','9 Pro',
                '10 Pro','10R','10R Endurance Edition','10T',
                '11','11R','12','12R','13','13 Ultra','13R',
                'Ace 2','Ace 2 Pro','Ace 3','Ace 3 Pro','Ace 5','Ace 5 Pro',
                'Nord','Nord 2','Nord 2T','Nord 3','Nord 4','Nord 5',
                'Nord CE','Nord CE 2','Nord CE 2 Lite','Nord CE 3','Nord CE 3 Lite',
                'Nord CE 4','Nord CE 4 Lite',
                'Nord N100','Nord N200','Nord N20','Nord N20 SE',
                'Nord N30','Nord N30 SE','Nord N300',
                'Other Model',
            ]],
            ['brand' => 'Oppo', 'models' => [
                'A1','A1K','A3','A3s','A5','A5s','A5x',
                'A7','A9','A12','A15','A15s','A16','A16K','A17','A17K',
                'A31','A53','A53s','A54','A57','A58','A5s',
                'A71','A73','A74','A76','A77s','A78','A83','A94','A96',
                'F1s','F3','F5','F11','F15','F17','F17 Pro','F19','F19 Pro','F21 Pro',
                'Find X','Find X Series',
                'Reno','Reno 4','Reno 7','Reno 8','Reno A','Reno5 Pro',
                'Reno 12','Reno 12 Pro',
                'Other Model',
            ]],
            ['brand' => 'Apple', 'models' => [
                'iPhone (Original/2G)','iPhone 3G','iPhone 3GS',
                'iPhone 4','iPhone 4S',
                'iPhone 5','iPhone 5C','iPhone 5S',
                'iPhone 6','iPhone 6 Plus','iPhone 6S','iPhone 6S Plus',
                'iPhone 7','iPhone 7 Plus',
                'iPhone 8','iPhone 8 Plus',
                'iPhone X','iPhone XR','iPhone XS','iPhone XS Max',
                'iPhone SE (1st generation)','iPhone SE (2nd generation)','iPhone SE (3rd generation)',
                'iPhone 11','iPhone 11 Pro','iPhone 11 Pro Max',
                'iPhone 12','iPhone 12 mini','iPhone 12 Pro','iPhone 12 Pro Max',
                'iPhone 13','iPhone 13 mini','iPhone 13 Pro','iPhone 13 Pro Max',
                'iPhone 14','iPhone 14 Plus','iPhone 14 Pro','iPhone 14 Pro Max',
                'iPhone 15','iPhone 15 Plus','iPhone 15 Pro','iPhone 15 Pro Max',
                'iPhone 16','iPhone 16 Plus','iPhone 16 Pro','iPhone 16 Pro Max','iPhone 16e',
                'iPhone 17','iPhone 17 Plus','iPhone 17 Pro','iPhone 17 Pro Max',
                'Other Model',
            ]],
            ['brand' => 'Google', 'models' => [
                'Pixel','Pixel XL',
                'Pixel 2','Pixel 2 XL',
                'Pixel 3','Pixel 3 XL','Pixel 3a','Pixel 3a XL',
                'Pixel 4','Pixel 4 XL','Pixel 4a','Pixel 4a 5G',
                'Pixel 5','Pixel 5a',
                'Pixel 6','Pixel 6 Pro','Pixel 6a',
                'Pixel 7','Pixel 7 Pro','Pixel 7a',
                'Pixel 8','Pixel 8 Pro','Pixel 8a',
                'Pixel 9','Pixel 9 Pro','Pixel 9 Pro Fold','Pixel 9 Pro XL','Pixel 9a',
                'Pixel 10','Pixel 10 Pro','Pixel 10 Pro Fold','Pixel 10 Pro XL','Pixel 10A',
                'Pixel C','Pixel Fold','Pixel Slate','Pixel Tablet',
                'Pixelbook','Pixelbook Go',
                'Other Model',
            ]],
            ['brand' => 'LG', 'models' => [
                'G2','G3','G4','G6','G8','G8S ThinQ','G8X ThinQ',
                'K4','K7','K9','K10','K10 (2017)','K10 (2018)','K11','K20','K22','K30','K31',
                'K40','K41S','K42','K50','K50S','K51','K51S','K61','K62','K71','K92 5G','K Series',
                'Nexus','Nexus 4','Nexus 5','Nexus 5X',
                'Q6','Q7','Q8','Q8 (2018)','Q60','Q61','Q70','Q71','Q92 5G','Q Series',
                'Stylo 3','Stylo 4','Stylo 5','Stylo 6','Stylo 7','Stylo Series',
                'V10','V20','V30','V30+','V35 ThinQ','V40 ThinQ','V50 ThinQ','V50S ThinQ','V60 ThinQ','V Series',
                'Velvet','Velvet 5G','Velvet Series',
                'Wing','Wing 5G',
                'X Cam','X Charge','X Mach','X Max','X Power','X Power 2','X Screen','X Series','X Style','X Venture',
                'X2','X4','X4+','X5','X6',
                'Other Model',
            ]],
            ['brand' => 'ZTE', 'models' => [
                'Blade A5','Blade A31','Blade A35','Blade A51','Blade A55','Blade A71','Blade V30',
                'Libra','nubia','Nubia V60',
                'Other Model',
            ]],
            ['brand' => 'Tecno', 'models' => [
                'Pop 5','Pop 7',
                'Spark','Spark 6 Go','Spark 7','Spark 7 Pro','Spark 8c','Spark 10c',
                'Other Model',
            ]],
            ['brand' => 'Nothing', 'models' => [
                'Phone 1','Phone 2','Phone 2a','Phone 2a Plus','Phone 3a','Phone 3a Pro',
                'CMF Phone 1','CMF Phone 2 Pro',
                'Other Model',
            ]],
            ['brand' => 'Motorola', 'models' => [
                'E4','G04s','G30','G4','G5','RAZR','Z2 Play',
                'Other Model',
            ]],
            ['brand' => 'itel', 'models' => ['A23','A48','A60','Wave 8','Wave 8C','Other Model']],
            ['brand' => 'Blackview', 'models' => ['Shark 8','Shark 9','Other Model']],
            ['brand' => 'iQOO', 'models' => ['Z6 lite','Other Model']],
            ['brand' => 'TCL', 'models' => ['20E','20L','20L+','20SE','20Y','Other Model']],
            ['brand' => 'HMD', 'models' => ['Crest Max','Fusion','Other Model']],
            ['brand' => 'Softlogic', 'models' => ['MAX 111','MAX 222','Other Model']],
            ['brand' => 'Oscal', 'models' => ['Pilot 1','Other Model']],
            ['brand' => 'UMIDIGI', 'models' => ['G1','Other Model']],
            ['brand' => 'Fujitsu', 'models' => ['Arrows MX F-F01K','Other Model']],
            ['brand' => 'Meizu', 'models' => ['Lucky 08','Other Model']],
            ['brand' => 'Sharp', 'models' => ['Aquos Crystal','Other Model']],
            ['brand' => 'Hotwav', 'models' => ['Note 13','Other Model']],
            ['brand' => 'Greentel', 'models' => ['O20','Other Model']],
            ['brand' => 'E-tel', 'models' => ['T15','Other Model']],
            ['brand' => 'Alcatel', 'models' => ['Other Model']],
            ['brand' => 'Dialog', 'models' => ['Other Model']],
            ['brand' => 'Lenovo', 'models' => ['K6 Note','K8 Plus','Other Model']],
            ['brand' => 'WiKO', 'models' => ['T20','Other Model']],
            ['brand' => 'China Mobile', 'models' => ['Other Model']],
            ['brand' => 'iPro', 'models' => ['Other Model']],
            ['brand' => 'Kyocera', 'models' => ['Basio','Basio 4','Torque KYV46','Other Model']],
            ['brand' => 'Digno', 'models' => ['704kcc','Other Model']],
            ['brand' => 'Sony Ericsson', 'models' => ['Other Model']],
            ['brand' => 'BlackBerry', 'models' => ['DTEK50','Q10','Other Model']],
            ['brand' => 'Acer', 'models' => ['Liquid Jade','Other Model']],
            ['brand' => 'Sky', 'models' => ['Other Model']],
            ['brand' => 'Ag-tel', 'models' => ['Other Model']],
            ['brand' => 'Asus', 'models' => ['Other Model']],
            ['brand' => 'HP', 'models' => ['Other Model']],
            ['brand' => 'HTC', 'models' => ['U11','Other Model']],
            ['brand' => 'LeEco', 'models' => ['Le2','Other Model']],
            ['brand' => 'Micromax', 'models' => ['Other Model']],
            ['brand' => 'Microsoft', 'models' => ['Lumia 950','Other Model']],
            ['brand' => 'Q Mobile', 'models' => ['Other Model']],
        ];
    }
}
