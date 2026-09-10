<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleBrandsModelsSeeder extends Seeder
{
    public function run(): void
    {
        // Category IDs
        $CAT_CARS        = 2611; // Cars & Light Vehicles
        $CAT_THREE_WHEEL = 2620; // Three Wheelers
        $CAT_BICYCLE     = 2602; // Bicycles
        $CAT_BUS         = 2625; // Buses
        $CAT_MOTO        = 2633; // Motorcycles & Scooters
        $CAT_SUV         = 2614; // SUVs & Jeeps
        $CAT_VAN         = 2615; // Vans & MPVs
        $CAT_PICKUP      = 2616; // Pickup Trucks
        $CAT_AG          = 2624; // Agricultural Vehicles
        $CAT_HEAVY       = 2628; // Heavy Machinery Vehicles
        $CAT_LORRY       = 2629; // Lorries & Trucks

        // ── CAR BRANDS WITH MODELS ──────────────────────────────────────────
        $carData = [
            'Toyota' => ['Other Model','Allex','Allion','Alphard','Aqua','Avanza','Avensis','Axio','Belta','Caldina','Cami','Camry','Carina','Ceres','CHR','Corolla','Corona','Corsa','Crown','Cynos','Duet','Fortuner','GT86','Harrier','Hilux','Hyryder','Hyryder V','IST','Land Cruiser','Land Cruiser 79','Land Cruiser Prado','Land Cruiser Sahara','Mark','Mirai','Noah','Passo','Pixis','Premio','Prius','Ractis','Raize','RAV4','Roomy','Rush','Sienta','Soluna','Sprinter','Starlet','Taisor','Tank','Tercel','TUNDRA','Urban Cruiser','UrbanCruiser Hyryder','Vanguard','Vellfire','Veloz','Verossa','Vios','Vista','Vitz','Voxy','Wigo','Yaris','Yaris Ativ','Yaris Cross','110','Corolla 121','Corolla 141','Corolla 2','Corolla AE110','KE20','Ke72','Aqua X Urban','Marino','AT 170','AA60','Mark 2','EE101','DX','Elephant Back','Glanza','CE108','Platz','SAI','Echo','Cresta','Dx Wagon'],
            'Suzuki' => ['Other Model','Alto','A-Star','Baleno','Celerio','Cultus','Dzire','Ertiga','Escudo','Esteem','Estilo','Every','Fronx','Grand Vitara','Hustler','Ignis','Jimny','Liana','Maruti','S-Cross','Solio','Spacia','Spresso','Swift','SX4','Vitara','Wagon R','Wagon R FX','Wagon R FZ','Wagon R Stingray','Wagon R ZX','XBee','Zen'],
            'Honda' => ['Other Model','Vezel','CRV','Fit','N-Box','Civic','Grace','N-WGN','City','Freed','Fit Aria','Fit Shuttle','WR-V','ZRV Z','Insight','Accord','CRZ','HR-V','Integra','Airwave','N-Van','S660','Step Wagon','BR-V','Logo','N-One'],
            'Nissan' => ['Other Model','Dayz','Sunny','Magnite','X-Trail','Roox','March','Almera','ROOX HIGHWAY STAR X','Serena','AD Wagon','Patrol','Bluebird','Cefiro','Clipper','Leaf','Pulsar','Sakura','Tiida','Wingroad','GT-R','Navara','Presea','Sylphy','Juke','Qashqai','Aura','Cedric P430','Double cab','FB13','Note','Teana','370Z','Dualis','Kicks','MOCO','Murano','Primera','Skyline','Tekna'],
            'Daihatsu' => ['Other Model','Mira','Taft','Rocky','Thor','Move','Tanto','Charade','Hijet','Terios','Canbus','Boon','Copen','Charmant','Wake','Cast Activa','F50'],
            'Mercedes Benz' => ['Other Model','C200','E200','GLB','C180','CLA 180','E350','CLA 200','E300','C220','GLE 400','S400','G Wagon','C350','CLA 250','GLE 300D','G450d','S580e','C160','EQB','EQS 450','S300','S320','A180','C300','E240','Vito','A200','C230','E220','E250','GLA 180','GLS 600','ML320','S500','SLC 180','B200','B250e','CLA45','E180','EQE 300','G400d','G800','GLA 200','GLC 250','GLC 300','GLS350','S350','S560','SLC 200'],
            'Mitsubishi' => ['Other Model','Lancer','Montero','eK Wagon','Outlander','Eclipse Cross','Pajero','L200','Xpander','4DR','EK Custom','Galant','Delica','Triton GSR','ASX','Attrage','eK Space','Cedia','Celeste'],
            'Kia' => ['Other Model','Sonet','Sorento','Syros','Sportage','Picanto','Seltos','Rio','Stonic','Carens','Cerato','Spectra','Sephia','Carnival','Clarus','EV5','Niro'],
            'Land Rover' => ['Other Model','Defender','Range Rover','Range Rover Sport','Discovery','Range Rover Evoque','Freelander','Discovery Sport','Range Rover PHEV','Range Rover Velar'],
            'BMW' => ['Other Model','X1','520d','318i','740Le','X5','523i','218i','525i','320d','530e','520i','740e','i8','Mini Cooper','X3','220i','528i','X2','316i','740Li','320i','i7','X7','530i','740i','i3','iX3','X5 eDrive','X5 M','225XE','330e','420d','430i','530d','535i','725D','730d','730Ld','750iL','750Ld','ActiveHybrid 7','E90','i4','i5','X6 M','Z4'],
            'Audi' => ['Other Model','A3','Q7','Q2','Q3','A4','A5','A1','A6','Q5','e-tron','A8','Q4 E-Tron S Line','Q4','Q8 E Tron 50','TTS'],
            'Hyundai' => ['Other Model','Venue','Tucson','Santa Fe','Accent','Sonata','Elantra','Eon','Grand i10','Matrix','Santro','Stellar','Trajet','Getz','Alcazar','Atos','Coupe','Creta','Excel','i20'],
            'Mahindra' => ['Other Model','Scorpio Pikup','Scorpio','KUV 100','Bolero','Thar','e2o'],
            'Mazda' => ['Other Model','Familia','3','6','CX-5','Demio','Flair','Axela','Carol','Tribute','2','BT-50','CX-3','RX'],
            'Ford' => ['Other Model','Raptor Ranger','Ranger','Laser','Fiesta','Focus','Mustang','Super Duty','Festiva','Ecosport','Everest','Kuga'],
            'Peugeot' => ['Other Model','5008','3008','2008','408','407','508','305','405','104','308','404','406','Voleex'],
            'Micro' => ['Other Model','Panda','Panda Cross','Kyron','Rexton','Geely','Tivoli','Korondo','Lifan','MX 7','Trend','Actyon','Almaz','Junior'],
            'Tata' => ['Other Model','Nano','Indica','Indigo','Xenon','Curvv','Nexon','Safari','Sumo'],
            'DFSK' => ['Other Model','Glory'],
            'Volkswagen' => ['Other Model','Taigun','Polo','Beetle','Tiguan','T-Cross','Passat','Golf','Jetta','ID'],
            'Perodua' => ['Other Model','Axia','Viva Elite','Bezza','Kelisa'],
            'Maruti Suzuki' => ['Other Model','800','Alto','Zen','Gypsy'],
            'MG' => ['Other Model','ZS','Hector Plus','MG4 X','HS Hybrid+','MG4 Electric','MG4 V Long Range','6'],
            'Lexus' => ['Other Model','LX600','RX350','LBX','LS500h','LX570','NX','NX300H','RX400','RX500h','CT-200H','GX550','HS250H','IS 300h','Land Cruiser','LS600h','LX500d','RX450h'],
            'Ssang Yong' => ['Other Model','Kyron','Rexton','Korando','Tivoli','Actyon','Musso'],
            'Bajaj' => ['Other Model','Qute'],
            'Jeep' => ['Other Model','Compass','Wrangler','Gladiator Rubicon','Grand Cherokee','Renegade','Cherokee'],
            'Renault' => ['Other Model','KWID'],
            'Isuzu' => ['Other Model','Gemini','Bighorn','MU-X','Rodeo'],
            'BAIC' => ['Other Model','X55 II'],
            'BYD' => ['Other Model','Atto 3','Dolphin','Seal','Sealion 7','Seal U','Song Plus','Tang','E6','M6','Shark','Qin Plus'],
            'Mini' => ['Other Model','Cooper','Countryman'],
            'Chery' => ['Other Model','QQ','Tiggo 4 Pro'],
            'Porsche' => ['Other Model','Cayenne','911 Carrera','Macan','Panamera','Taycan','718 Boxter','718 Cayman'],
            'Tesla' => ['Other Model','Model 3','Model Y','Model S'],
            'Maruti' => ['Other Model'],
            'Jaguar' => ['Other Model','F-Pace','XF','E-Pace','X-Type','XE','XJ'],
            'Datsun' => ['Other Model','Redi Go'],
            'Subaru' => ['Other Model','Forester','Pleo','XV','Legacy'],
            'Volvo' => ['Other Model','S90','XC90','850','S40','S60','S80','XC40'],
            'Bentley' => ['Other Model','Flying Spur','Bentayga'],
            'Morris' => ['Other Model','Minor','Mini'],
            'Proton' => ['Other Model','Wira','Saga','Gen-2'],
            'Zotye' => ['Other Model','Z100','Nomad'],
            'Austin' => ['Other Model','7','Mini Cooper'],
            'Chevrolet' => ['Other Model','Cruze','Camaro'],
            'Chrysler' => ['Other Model','300'],
            'Daewoo' => ['Other Model','Lanos','Leganza'],
            'Fiat' => ['Other Model','Bravo','500','Linea','Palio'],
            'Opel' => ['Other Model','Astra','Omega'],
            'BAW' => ['Other Model','E7'],
            'Lamborghini' => ['Other Model','Urus'],
            'Rolls-Royce' => ['Other Model','Cullinan'],
            'JAC' => ['Other Model','T9'],
            'Jetour' => ['Other Model','Dashing'],
            'SAIC Maxus' => ['Other Model','T90 Luxury'],
            'Skoda' => ['Other Model','Karoq','Kylaq'],
            'Ferrari' => ['Other Model'],
            'GWM' => ['Other Model'],
            'Hino' => ['Other Model'],
            'Hummer' => ['Other Model'],
            'Lotus' => ['Other Model'],
            'Maxus' => ['Other Model'],
            'Wuling' => ['Other Model'],
        ];

        // ── MOTORCYCLE/BIKE BRANDS WITH MODELS ──────────────────────────────
        $bikeData = [
            'Bajaj' => ['Other Model','Aspire','Avenger','Avenger Cruise','Avenger Street','Avenger Street 150','Boxer','Byk','Caliber','Chetak (2006)','Chetak Electric','CT100','CT110','Discover','Discover 100','Discover 110','Discover 125','Discover 135','Discover 150','Dominar','Dominar 400','Kristal','Platina','Platina 100','Platina 100 ES','Platina 110','Platina 110 ES','Platina 125','Platina Comfort','Pulsar 125','Pulsar 135','Pulsar 135 LS','Pulsar 150','Pulsar 180','Pulsar 200','Pulsar 220F','Pulsar AS150','Pulsar N125','Pulsar N150','Pulsar N160','Pulsar NS125','Pulsar NS160','Pulsar NS200','Pulsar NS400Z','Pulsar RS200','V12','V15','XCD','XCD 125'],
            'TVS' => ['Other Model','Apache','Apache RR310','Apache RTR','Apache RTR150','Apache RTR160','Apache RTR160 4V','Apache RTR180','Apache RTR200','Apache RTR200 4V','Apache RTX300','Centra','Fiero 125','Flame','HLX150','HLX150F','iQube','iQube S','iQube ST','Jupiter','Jupiter 125','Metro','Metro 100','Metro 110','Ntorq','Ntorq 125','Pep','Phoenix','Radeon','Raider 125','Ronin','Scooty Pep','Scooty Pep+','Scooty Zest','Sport','Star City Plus','Star Sport','Star Sport 125','Streak','Stryker 125','Victor','Wego','Wego 110','XL100','XL100 Comfort','XL100 Heavy Duty','XL100 WIN Edition','XL Super','Zest'],
            'Honda' => ['Other Model','Activa','Activa 125','ADV150','ADV160','Aviator','AX1','Benly','CB125','CB125F','CB223S','CB350RS','CB Hornet','CB Hornet 160R','CB Shine','CB Trigger','CB Twister 110','CB Unicorn','CBR125R','CBR250R','CBR250RR','CBR1000RR','CD70','CD90','CD110','CD125','CD200','CG125','Chaly','CM Custom','CRF','CRF250','Degree','Dio','Dream','Dream 125','Dream110','Dream Yuga','FTR','Grazia','Grazia 125','Hness CB350','Hornet','Little Cub','Livo','Magna','Navi','PCX','PCX125','PCX150','PCX160','Rebel','Rebel 250','Roadmaster','Scoopy 110','SH300i','Shine SP','Stunner','Super Cub','Today','Unicorn 160','X-Blade','X-Blade 160','XL185','XLR','XR','Zoomer'],
            'Hero' => ['Other Model','Achiever','CBZ','HF Deluxe','HF Dawn','Hunk','Hunk 150R','Hunk 160','Hunk 160R','Hunk 160R 4V','Hunk Double Disc','Hunk Single Disc','Karizma','Karizma ZMR','Maestro','Maestro Edge','Maestro Edge 110','Passion Plus','Passion Pro','Splendor','Splendor i Smart','Splendor Plus','Super Splendor','Xoom 110','Xoom 110 CE','Xoom 110 FI','Xoom 110 i3s','Xoom 125R','Xpulse','Xpulse 200','Xtreme 125R','Xtreme 160R','Xtreme Sports'],
            'Yamaha' => ['Other Model','Aerox','Aerox 155','Alpha','Crux','DT','Fascino','Fazer','Fazer 25','FZ','FZ1','FZ16','FZ25','FZ-S','FZ-S FI','FZ V1','FZ V2','FZ V3','FZ V4','Gladiator','Libero','Mate','MT-15','MT-25','NMax','R1','R3','R6','R15','R15 V2','R15 V3','R15 V4','R15M','Ray','Ray Z','Ray ZR','Ray ZR Street Rally','RX100','Saluto','Saluto 125','Serow XT225','SR125','SZR','SZ-RR','TMax','TTR','TW','TW200','TZR','Virago','Vox','WR','WRF','WRX250','XSR125','XSR155','XTZ125','YBR110','YS125','YZ','ZR'],
            'KTM' => ['Other Model','250 Adventure','390 Adventure','390 Adventure R','390 Adventure X','390 Enduro R','Duke','Duke 125','Duke 150','Duke 200','Duke 250','Duke 390','EXC 250','EXC 300','EXC 350 F','EXC 450 F','Freeride E-XC','RC','RC 125','RC 200','RC 390','SX 50','SX 65','SX 85','SX 125','SX 250 F','SX 450 F'],
            'Suzuki' => ['Other Model','Access','AX100','Bandit','Burgman','Burgman 125','Burgman Street','Choinori','Djebel','DR','DRZ','EN125-2A','GN','GN125','GN125 H','GS125','GS150R','GSX125','Gixxer','Gixxer 250','Gixxer FI Disc','Gixxer SF','Gixxer SF 250','Grass Tracker','Hayate','Intruder','Intruder ABS','Intruder FI','Katana','Lets','Samurai 150','SB Tracker','SX','TMR','V-Strom 250','VanVan RV200','Volty'],
            'Demak' => ['Other Model','ATM','Civic','D7','Dart DXT225','DTM','DTM 150','DTM 200','DZM','DZM 200','DZR','DZR 120','Explorer','Rino','Savage Supra','Sky Born','Skyline','Skyline GT225','Transler','Transtar','Tropica','Warrior','Warrior 150'],
            'Ranomoto' => ['Other Model','CG125','Dream','GN125','Keeway','Moped Super','Pattaya','Pattaya Double Power 125','Super Cub','Waves'],
            'Yadea' => ['Other Model','C1S','C1S Pro','E8S','E8S Pro','EPOC','FIERIDER','G5','G5 Pro','G6','GFX','GT30','GT60','KS3','KS3 Lite','KS5','KS5 Pro','M6','M6L','Orla','Owin','RS20','T5','T9','VELAX','VoltGuard','Y1S','YS500','Power 125'],
            'Mahindra' => ['Other Model','Centuro','Duro','Duro DZ','Flyte','Gusto','Gusto 125','Gusto RS','Kine','Mojo','Mojo UT300','Pantero','Rodeo','Rodeo RZ','Rodeo UZO 125'],
            'Senaro' => ['Other Model','Click 150i','Click V150i','GN125','GN125 H','Super Cub 90'],
            'Singer' => ['Other Model','Safari','Safari 4S','ELO7','1.5G','F55','F71','GTO 100'],
            'Kawasaki' => ['Other Model','D Tracker','D Tracker 250','D Tracker D2','D Tracker Mini KSR','D Tracker X','Estrella','KDX','KLE 400','KLX 250','KLX Tracker 250','KSR 110','Ninja 250','Ninja 300','Ninja 400','Ninja ZX','Versys 650','Z900','Z1100'],
            'Loncin' => ['Other Model','48cc','90','CD125','GN','LD90','LX','LX90','LX100','LX110','LX125','Scoby 125','Super','Super Cub','Super Cub 90'],
            'Dyno' => ['Other Model','Scooby 125'],
            'TMR' => ['Other Model','G18','XGW','ZL'],
            'NWOW' => ['Other Model','ARS','GB2','SE','TK10','WSP','Maverick'],
            'AIMA' => ['Other Model','Aria','Breezy','JoyBean','Liberty','Mana'],
            'Royal Enfield' => ['Other Model','Bear 650','Bullet 350','Bullet 650','Classic','Classic 350','Classic 650','Classic 650 Twin','Continental GT 650','Goan Classic 350','Guerrilla 450','Himalayan','Himalayan 410','Himalayan 450','Hunter 350','Interceptor 650','Meteor 350','Rumbler 350','Scram 411','Shotgun 650','Super Meteor 650','Thunderbird 350'],
            'Kinetic' => ['Other Model','Boss 100','Boss 115','Boxer','Challenger 100','GF125','Kinetic Honda','Luna','Safari','Stryker','Velocity 115'],
            'Aprilia' => ['Other Model','RS457','RS4 125','RSV4','RSV4 1100 Factory','SR','SR125','SR150','SR150 Race','Tuono 660'],
            'Singer Lima' => ['Other Model','ELO7','1.5G'],
            'Scooty' => ['Other Model'],
            'BMW' => ['Other Model','C400 GT','G310 GS','G310 R','K1600B','R1250 GS','S1000','S1000RR'],
            'Rise' => ['Other Model','Eco Scooter','Leader','Leader 125'],
            'Vmoto' => ['Other Model','CPX','E-Max 120L','Citi','Sonic','Spark','Volta V2','ZAP'],
            'Alfa' => ['Other Model','E-Pro','Kai'],
            'Electra' => ['Other Model'],
            'KYMCO' => ['Other Model','Downtown 125i','Downtown 350i'],
            'Ather' => ['Other Model','450X'],
            'Harley Davidson' => ['Other Model','X','Sportster'],
            'Piaggio' => ['Other Model'],
            'SUNRA' => ['Other Model'],
            'Triumph' => ['Other Model','SXL'],
            'Vespa' => ['Other Model','Flash'],
            'Hero Electric' => ['Other Model'],
            'HUAIHAI' => ['Other Model'],
            'Minnelli' => ['Other Model'],
            'Ola Electric' => ['Other Model'],
            'Revolt' => ['Other Model'],
            'Super Soco' => ['Other Model'],
        ];

        // ── THREE-WHEEL BRANDS WITH MODELS ──────────────────────────────────
        $threeWheelData = [
            'Bajaj'       => ['Other Model','RE'],
            'TVS'         => ['Other Model','King'],
            'Mahindra'    => ['Other Model','Alfa'],
            'Piaggio'     => ['Other Model','Ape'],
            'NWOW'        => ['Other Model','ERVS Plus'],
            'Vega'        => ['Other Model','ETX'],
        ];

        // ── BICYCLE BRANDS (no models) ───────────────────────────────────────
        $bicycleData = [
            'Lumala'   => ['Other Model'],
            'Tomahawk' => ['Other Model'],
            'DSI'      => ['Other Model'],
            'Kenton'   => ['Other Model'],
        ];

        // ── BUS BRANDS WITH MODELS ───────────────────────────────────────────
        $busData = [
            'Toyota'        => ['Other Model','Coaster'],
            'Tata'          => ['Other Model','Marcopolo','909','Ultra','1510','Starbus'],
            'Mitsubishi'    => ['Other Model','Rosa','FUSO','Dolphin Fuso'],
            'Ashok Leyland' => ['Other Model','Viking','Sunshine'],
            'Isuzu'         => ['Other Model','Journey'],
            'Golden Dragon' => ['Other Model'],
            'Kinglong'      => ['Other Model'],
            'Micro'         => ['Other Model','Higer'],
            'Nissan'        => ['Other Model','Civilian'],
        ];

        // ── EXTRA BRANDS FROM VEHICLE-TYPE TABLE (no models) ─────────────────
        // These brands appear in SUV/Van/Truck/Lorry/etc sub-sections
        $extraBrandsWithCategories = [
            // [brand_name => [cat_ids]]
            'Changan'       => [$CAT_VAN, $CAT_LORRY],
            'Eicher'        => [$CAT_LORRY],
            'Force'         => [$CAT_VAN],
            'Foton'         => [$CAT_PICKUP, $CAT_LORRY],
            'Borgward'      => [$CAT_SUV],
            'Deepal'        => [$CAT_SUV],
            'Haval'         => [$CAT_SUV],
            'Jaecoo'        => [$CAT_SUV],
            'Jonway'        => [$CAT_SUV],
            'GAC'           => [$CAT_SUV],
            'JMC'           => [$CAT_PICKUP, $CAT_LORRY],
            'Willys'        => [$CAT_SUV, $CAT_PICKUP],
            'Ceygra'        => [$CAT_THREE_WHEEL],
            'Daido'         => [$CAT_THREE_WHEEL],
            'Sakai'         => [$CAT_THREE_WHEEL],
            'Smart'         => [$CAT_THREE_WHEEL],
            'TAFE'          => [$CAT_AG, $CAT_LORRY],
            'Wave'          => [$CAT_THREE_WHEEL],
            'Atco'          => [$CAT_LORRY],
            'Massey-Ferguson'=> [$CAT_AG],
            'Kubota'        => [$CAT_AG],
            'Powertrac'     => [$CAT_AG],
            'Sonalika'      => [$CAT_AG],
            'Swaraj'        => [$CAT_AG],
            'New-Holland'   => [$CAT_AG],
            'John-Deere'    => [$CAT_AG],
            'IHI'           => [$CAT_AG, $CAT_HEAVY],
            'Yanmar'        => [$CAT_AG, $CAT_HEAVY],
            'FAW'           => [$CAT_LORRY, $CAT_HEAVY],
            'Dfac'          => [$CAT_HEAVY],
            'Hitachi'       => [$CAT_HEAVY],
            'JCB'           => [$CAT_HEAVY],
            'Kobelco'       => [$CAT_HEAVY],
            'Komatsu'       => [$CAT_HEAVY],
            'Yuejin'        => [$CAT_LORRY],
            'DAF'           => [$CAT_LORRY, $CAT_HEAVY],
            'Ducati'        => [$CAT_MOTO],
            'KMC'           => [$CAT_MOTO],
            'Zongshen'      => [$CAT_MOTO],
            'Reva'          => [$CAT_MOTO],
            'Iveco'         => [$CAT_BUS],
            'JiaLing'       => [$CAT_MOTO, $CAT_AG],
            'MAN'           => [$CAT_HEAVY],
        ];

        // ── HELPER ───────────────────────────────────────────────────────────
        $seedGroup = function(array $data, int $catId) {
            foreach ($data as $brandName => $models) {
                // Upsert brand
                $existing = DB::table('brands')->where('name', $brandName)->first();
                if ($existing) {
                    $brandId = $existing->id;
                } else {
                    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $brandName));
                    $brandId = DB::table('brands')->insertGetId([
                        'name'           => $brandName,
                        'slug'           => $slug,
                        'category_group' => '',
                        'is_active'      => 1,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                    echo "  NEW brand: {$brandName} (id={$brandId})\n";
                }

                // Link brand to category
                $pivotExists = DB::table('brand_category')
                    ->where('brand_id', $brandId)
                    ->where('category_id', $catId)
                    ->exists();
                if (!$pivotExists) {
                    DB::table('brand_category')->insert([
                        'brand_id'    => $brandId,
                        'category_id' => $catId,
                    ]);
                }

                // Seed models (deduplicated)
                $seen = [];
                $newCount = 0;
                foreach (array_unique($models) as $modelName) {
                    if (!$modelName) continue;
                    $key = $brandId . '_' . strtolower($modelName);
                    if (isset($seen[$key])) continue;
                    $seen[$key] = true;

                    $exists = DB::table('brand_models')
                        ->where('brand_id', $brandId)
                        ->where('name', $modelName)
                        ->exists();
                    if (!$exists) {
                        $modelSlug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $modelName));
                        DB::table('brand_models')->insert([
                            'brand_id'   => $brandId,
                            'name'       => $modelName,
                            'slug'       => $modelSlug,
                            'is_active'  => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $newCount++;
                    }
                }
                echo "  {$brandName}: {$newCount} new models\n";
            }
        };

        echo "=== Cars & Light Vehicles ===\n";
        $seedGroup($carData, $CAT_CARS);

        echo "\n=== Motorcycles & Scooters ===\n";
        $seedGroup($bikeData, $CAT_MOTO);

        echo "\n=== Three Wheelers ===\n";
        $seedGroup($threeWheelData, $CAT_THREE_WHEEL);

        echo "\n=== Bicycles ===\n";
        $seedGroup($bicycleData, $CAT_BICYCLE);

        echo "\n=== Buses ===\n";
        $seedGroup($busData, $CAT_BUS);

        // Extra category links for multi-type brands already seeded
        echo "\n=== Extra category links ===\n";
        foreach ($extraBrandsWithCategories as $brandName => $catIds) {
            $existing = DB::table('brands')->where('name', $brandName)->first();
            if (!$existing) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $brandName));
                $brandId = DB::table('brands')->insertGetId([
                    'name'           => $brandName,
                    'slug'           => $slug,
                    'category_group' => '',
                    'is_active'      => 1,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
                DB::table('brand_models')->insert([
                    'brand_id'   => $brandId,
                    'name'       => 'Other Model',
                    'slug'       => 'other-model',
                    'is_active'  => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                echo "  NEW brand: {$brandName}\n";
            } else {
                $brandId = $existing->id;
            }
            foreach ($catIds as $catId) {
                $pivotExists = DB::table('brand_category')
                    ->where('brand_id', $brandId)
                    ->where('category_id', $catId)
                    ->exists();
                if (!$pivotExists) {
                    DB::table('brand_category')->insert([
                        'brand_id'    => $brandId,
                        'category_id' => $catId,
                    ]);
                    echo "  Linked {$brandName} → cat {$catId}\n";
                }
            }
        }

        // Also link multi-category vehicle brands
        echo "\n=== Multi-category links for existing car brands ===\n";
        $multiCatMap = [
            // brand_name => [additional cat_ids beyond the main one]
            'Toyota'     => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_BUS],
            'Suzuki'     => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY],
            'Honda'      => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_MOTO],
            'Nissan'     => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY],
            'Daihatsu'   => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY],
            'Mitsubishi' => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_BUS],
            'Isuzu'      => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_AG],
            'Ford'       => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY],
            'Mahindra'   => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_MOTO, $CAT_AG],
            'Tata'       => [$CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_BUS, $CAT_AG],
            'Volkswagen' => [$CAT_SUV, $CAT_VAN],
            'Hyundai'    => [$CAT_SUV, $CAT_VAN, $CAT_BUS],
            'Kia'        => [$CAT_SUV, $CAT_VAN],
            'Mazda'      => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY],
            'BMW'        => [$CAT_SUV, $CAT_MOTO],
            'Subaru'     => [$CAT_SUV, $CAT_VAN, $CAT_LORRY],
            'Bajaj'      => [$CAT_MOTO, $CAT_THREE_WHEEL],
            'TVS'        => [$CAT_MOTO, $CAT_THREE_WHEEL],
            'Piaggio'    => [$CAT_MOTO, $CAT_THREE_WHEEL],
            'NWOW'       => [$CAT_MOTO, $CAT_THREE_WHEEL],
            'JAC'        => [$CAT_LORRY],
            'Hino'       => [$CAT_LORRY],
            'Mercedes Benz' => [$CAT_SUV, $CAT_VAN, $CAT_BUS, $CAT_HEAVY],
            'Volvo'      => [$CAT_LORRY, $CAT_HEAVY],
            'Micro'      => [$CAT_SUV, $CAT_VAN, $CAT_PICKUP, $CAT_LORRY, $CAT_BUS],
            'Ashok Leyland' => [$CAT_BUS, $CAT_LORRY, $CAT_HEAVY],
        ];
        foreach ($multiCatMap as $brandName => $catIds) {
            $brand = DB::table('brands')->where('name', $brandName)->first();
            if (!$brand) continue;
            foreach ($catIds as $catId) {
                $exists = DB::table('brand_category')
                    ->where('brand_id', $brand->id)
                    ->where('category_id', $catId)
                    ->exists();
                if (!$exists) {
                    DB::table('brand_category')->insert(['brand_id' => $brand->id, 'category_id' => $catId]);
                    echo "  Linked {$brandName} → cat {$catId}\n";
                }
            }
        }

        echo "\nDone.\n";
    }
}
