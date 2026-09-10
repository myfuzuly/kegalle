<?php
// Brand-Category Seeder — self-neutralizes on completion

// --- Load .env ---
$envPath = '/home/kegalle/app_core/.env';
$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}

$host = $env['DB_HOST']     ?? '127.0.0.1';
$port = $env['DB_PORT']     ?? '3306';
$db   = $env['DB_DATABASE'] ?? 'kegalle_kegalle';
$user = $env['DB_USERNAME'] ?? 'kegalle_fuzz';
$pass = $env['DB_PASSWORD'] ?? '';

// --- Connect ---
$pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
echo "Connected to $db\n";

// --- Create pivot table ---
$pdo->exec("CREATE TABLE IF NOT EXISTS brand_category (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    brand_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY uq_bc (brand_id, category_id)
)");
echo "brand_category table ready\n";

// --- Brand data ---
$data = [
    'Agriculture, Farming & Livestock' => ['Bayer','Syngenta','BASF','Corteva','Dhanuka','FMC','John Deere','Kubota','Mahindra','Massey Ferguson','New Holland','Honda','Yanmar','Stihl','Husqvarna','Echo','Oleo-Mac','Oregon','Makita','Bosch','Total','Abans','Singer','Damro','Atlas','DIMO','CIC','Hayleys Agriculture','Lankem','CIC Agribusiness','Hemas','ACL','Browns'],
    'Beauty & Cosmetics' => ['Axe','Bioderma','CeraVe','Cetaphil','COSRX','Dove','Garnier','Himalaya','Johnson\'s','L\'Oreal Paris','Maybelline','Nivea','Neutrogena','Olay','Oriflame','Pond\'s','Revlon','Sebamed','Simple','The Body Shop','The Ordinary','TRESemme','Vaseline','Victoria\'s Secret','Nature\'s Secrets','Spa Ceylon','Siddhalepa','Kumarika'],
    'Baby, Kids & Toys' => ['Aden + Anais','Aptamil','Avent','Baby Alive','Baby Cheramy','Baby Dove','Babyhug','B.Box','Beaba','Bebesup','Bebeconfort','Bright Starts','Bumkins','Burt\'s Bees Baby','Carter\'s','Cetaphil Baby','Chicco','Combi','Crayola','Cybex','Disney','Disney Baby','Dr. Brown\'s','Drypers','Farlin','Fisher-Price','FridaBaby','Funskool','Gerber','Graco','Hape','Huggies','Himalaya Baby','Hot Wheels','Infantino','Jada Toys','Janod','Johnson\'s Baby','Joie','Jockey Kids','Lego','LeapFrog','Little Tikes','LOL Surprise','LuvLap','MAM','MamyPoko','Manhattan Toy','Matchbox','Maxi-Cosi','Medela','Mee Mee','Melissa & Doug','Mega Bloks','Mothercare','Mustela','Nerf','Nuby','NUK','Olababy','OshKosh B\'gosh','Pampers','Panda','Pears','Peppa Pig','Philips Avent','Pigeon','Play-Doh','Playgro','Playmobil','Pokemon','R for Rabbit','Ravensburger','Richell','Safety 1st','Sebamed Baby','Skip Hop','Softlove','Sylvanian Families','Taf Toys','Thomas & Friends','Tommee Tippee','Ubbi','VTech','Velona Cuddles','Weleda Baby'],
    'Business, Office & Industrial' => ['3M','Abans','ABB','APC','Bosch','Brother','Canon','Caterpillar','Cummins','Dell','DeWalt','Epson','Festo','Fujitsu','Haier','Hitachi','Honeywell','HP','Hyundai','JCB','Karcher','Komatsu','Kyocera','Lenovo','LG','Makita','Mettler Toledo','Panasonic','Philips','Ricoh','Samsung','Sharp','Siemens','Stanley','Thermo Fisher Scientific','Toshiba','Toyota','Wacker Neuson','Xerox','Zebra'],
    'Community & Classifieds' => ['Apple','Samsung','Xiaomi','Huawei','Nokia','HP','Dell','Lenovo','Canon','Epson','Singer','Damro','Abans','Atlas','Bata','Adidas','Nike','Pigeon','Fisher-Price','Disney','Toyota','Kia'],
    'Computers & Printers' => ['Acer','Adobe','AMD','Apple','APC','ASRock','Asus','Brother','Canon','Cisco','Cooler Master','Corsair','Crucial','Dell','Epson','Fujitsu','Gigabyte','Google','HP','Huawei','Intel','Kingston','Kyocera','Lenovo','LG','Logitech','Microsoft','MSI','Nvidia','OKI','Panasonic','Philips','Razer','Realme','Ricoh','Samsung','SanDisk','Seagate','Sharp','Sony','Toshiba','TP-Link','Transcend','ViewSonic','Western Digital','Xerox','Zebra'],
    'Digital Products' => ['Adobe','Amazon','Apple','Canva','Daraz','Dialog','Discord','Disney+','eZ Cash','Figma','Google','Google Play','Hutch','Microsoft','Netflix','PayPal','PickMe','PlayStation','Samsung','Spotify','Steam','Uber','Udemy','Visa','WhatsApp','Xiaomi','Xbox','YouTube','Zoom','Zoho'],
    'Education, Books & Stationery' => ['Atlas','Faber Castell','Maped','Staedtler','Doms','Camlin','Cello','Artline','Pilot','Pentel','Montex','Parker','Bic','Deli','Kangaro','Kokuyo','Classmate','Oxford','Cambridge','Scholastic','Disney','Smiggle','Samsonite','American Tourister','Fisher-Price','Lego Education','VTech'],
    'Electrical & Lighting' => ['Abans','ABB','ACL','Anchor','Anker','Ariston','Bajaj','Beko','Black+Decker','Bosch','Bticino','Brown','CIC','Crompton','Daikin','Damro','Danfoss','Dawlance','De\'Longhi','Dimplex','Eaton','Electrolux','Eureka Forbes','Faber','Fuji Electric','General','Haier','Hager','Havells','Hitachi','Honeywell','Hoover','IKEA','Innovex','Karcher','KDK','Kelani Cables','Kenwood','Legrand','LG','Luminous','Makita','Midea','Microtek','Mitsubishi','Mitsubishi Electric','Nilfisk','Omron','Orange Electric','Orient','Osram','Panasonic','Philips','Philips Hue','Preethi','Rinnai','Samsung','Schneider Electric','Sharp','Siemens','Singer','SMA','Smeg','Softlogic','Sonoff','Stanley','Syska','Tefal','Toshiba','TP-Link','Trina Solar','Tuya','Usha','V-Guard','Varta','Vikram Solar','Whirlpool','Wipro','Wurth','Xiaomi','Yaskawa','Yeelight'],
    'Electronics' => ['Acer','AEE','Akaso','Amazon','Anker','Apple','Aqara','Arlo','Autel Robotics','Axis','BenQ','Blackmagic Design','Bosch','Canon','Casio','CP Plus','Creality','Dahua','DJI','D-Link','Epson','Eufy','EZVIZ','Fimi','Fujifilm','Garmin','GoPro','Google','Hasselblad','Hikmicro','Hikvision','Honeywell','Holy Stone','Hubsan','Imou','Insta360','iRobot','JBL','Jimu','Kasa','Kodak','Leica','LG','Logitech','Makeblock','Minolta','Mobotix','Nikon','Netatmo','Oculus','Olympus','Panasonic','Parrot','Pentax','Philips Hue','Polaroid','QNAP','Reolink','Ricoh','Ring','Roborock','Rode','Ryze Tech','Samsung','Sanyo','Schneider Electric','Shark','Sigma','Skydio','Sony','Sphero','Synology','Syma','Tamron','Tello','TP-Link','Trendnet','Tuya','Ubiquiti','Vivotek','Viltrox','Wyze','Xiaomi','Yale','Yeelight','Yuneec','ZKTeco','Zeiss'],
    'Events, Tickets & Experiences' => ['Airbnb','BMICH','Booking.com','Dialog','Eventbrite','Klook','Nelum Pokuna','PickMe','Sri Lanka Tourism','Ticketmaster','Tripadvisor','Uber','Visa'],
    'Fashion & Lifestyle' => ['Adidas','Aldo','American Tourister','Bata','Calvin Klein','Casio','Charles & Keith','Clarks','Converse','Crocs','Fossil','G-Shock','H&M','Hush Puppies','Jockey','Kipling','Lacoste','Lee','Levi\'s','Marks & Spencer','Nike','Puma','Ray-Ban','Reebok','Samsonite','Skechers','Skybags','Swiss Military','Titan','Tommy Hilfiger','U.S. Polo Assn','Zara','Abdeen Jewellers','Aida Gems','Arpico Jewellery','Ceylon Gems','Colombo Jewellery Stores','Daya Gems','EAP Jewellery','Hemachandra Brothers','Kandy Jewellery','Manjari','Raja Jewellers','Swarnamahal','Tiesh','Avirate','Buddhi Batiks','CIB Shopping Centre','Cool Planet','Emerald','Fashion Bug','Glitz','House of Fashion','Kelly Felder','Mimosa','Nolimit','ODEL','Paradise Road'],
    'Food & Beverages' => ['Aachi','Anchor','Anil','Araliya','BakersChoice','Cadbury','Cargills','CBL','CIC','Coca-Cola','Dilmah','Elephant House','Ferrero','Fonterra','Hershey\'s','Highland','Horlicks','Kellogg\'s','Kist','Kotmale','Lipton','Maliban','Mars','MD','Milo','Munchee','Nestle','Nescafe','Nutella','Ovaltine','Pepsi','Prima','Ritzbury','Sunquick','Supreme','Tilda','Unilever','Watawala','Wijaya'],
    'Furniture & Home Living' => ['Avant','Arpico','Damro','IKEA','Nilkamal','Singer','Kohler','Grohe','Duravit','Roca','Franke','Smeg','Bosch','Electrolux','Philips','Panasonic','LG','Samsung','Toshiba','Mitsubishi','Whirlpool','Stanley','Makita','3M','Intex','JBL','Yale','Xiaomi'],
    'Gaming & Entertainment' => ['Acer','AMD','Alienware','Amazon','Apple','Asus','BenQ','Bose','Corsair','Cooler Master','Dell','Elgato','Fantech','Gigabyte','Google','HP','HyperX','Intel','JBL','Kingston','Lenovo','LG','Logitech','Meta','Microsoft','MSI','Nintendo','Nvidia','Oculus','PlayStation','Razer','Realme','Redragon','Samsung','SanDisk','Seagate','Sennheiser','Sony','SteelSeries','Xbox','Xiaomi'],
    'Grocery' => ['Aachi','Anchor','Anil','Araliya','Cadbury','Cargills','CBL','CIC','Coca-Cola','Dalda','Dilmah','Fonterra','Fortune','Harischandra','Highland','Horlicks','Kellogg\'s','Kist','Kotmale','Lipton','Maliban','MD','Milo','Munchee','Nestle','Nescafe','Nutella','Ovaltine','Pepsi','Prima','Ritzbury','Sunquick','Tilda','Unilever','Wijaya','Watawala'],
    'Health & Wellness' => ['Abbott','AccuChek','Bayer','Beurer','Bioderma','Braun','CeraVe','Cetaphil','Cipla','Colgate','Dabur','Dettol','Dove','Eucerin','Fitbit','Garmin','Himalaya','Johnson\'s','La Roche-Posay','Lifebuoy','Medela','Microlife','Nestle','Neutrogena','Nivea','Omron','Pampers','Philips','Pigeon','Sebamed','Siddhalepa','Unilever','Vaseline','Vicks','Weleda','Xiaomi'],
    'Home & Kitchen Appliances' => ['Abans','Ariston','Bajaj','Beko','Bosch','Braun','Breville','Butterfly','Carrier','Daikin','Damro','De\'Longhi','Dyson','Electrolux','Faber','Fujitsu','Gree','Haier','Havells','Hitachi','Hisense','Hoover','IKEA','Innovex','Karcher','Kenwood','KitchenAid','LG','Midea','Miele','Mitsubishi','Ninja','Panasonic','Philips','Preethi','Prestige','Rinnai','Samsung','Sharp','Singer','Smeg','Tefal','Toshiba','Usha','Voltas','Westinghouse','Whirlpool','Xiaomi','Yamaha'],
    'Home Improvement, Garden, Tools & DIY' => ['3M','ABB','ACL','Alumex','Asian Paints','Atlas','Bosch','British Paints','CERA','Ceylon Cement','CIC','Crompton','Daikin','DeWalt','Dorma','Dulux','Eaton','Fosroc','Franke','Fujitsu','Geberit','Grohe','Grundfos','Hager','Haycarb','Hayleys','Hettich','Hitachi','Holcim','Honeywell','Husqvarna','Hyundai','Ingco','Jaquar','Jotun','Karcher','Kelani Cables','Kohler','KSB','Lanka Tiles','Legrand','Makita','Mapei','Mitsubishi Electric','Nippon Paint','Norton','Panasonic','Philips','Pidilite','Rocell','Royal Ceramics','Schneider Electric','Sika','Singer','S-Lon','Stanley','Teka','Tokyo Cement','TP-Link','Trina Solar','V-Guard','Weber','Wilo','Wipro','Wurth','Yale','ZKTeco'],
    'Kitchen & Dining' => ['Ariston','Bajaj','Beko','Black+Decker','Bosch','Borosil','Braun','Breville','Butterfly','Corelle','Cuisinart','Damro','De\'Longhi','Electrolux','Faber','Fissler','Gorenje','Haier','Havells','Hitachi','Hoover','IKEA','Innovex','Karcher','Kenwood','KitchenAid','LG','Liebherr','Midea','Miele','Mitsubishi','Ninja','Panasonic','Philips','Preethi','Prestige','Pyrex','Samsung','Sanyo','Sharp','Singer','Smeg','Tefal','Thermos','Toshiba','Tupperware','Usha','Whirlpool','Xiaomi','Zojirushi'],
    'Mobile Phones & Tablets' => ['Acer','Alcatel','Amazon','Apple','Asus','BlackBerry','BLU','CAT','Coolpad','Dell','Doogee','Fairphone','Google','Honor','HP','HTC','Huawei','Infinix','iQOO','Itel','Lava','Lenovo','LG','Meizu','Micromax','Microsoft','Motorola','Nokia','Nothing','Nubia','OnePlus','Oppo','Oukitel','Panasonic','Poco','Realme','Redmi','Razer','ROG','Samsung','Sharp','Sony','TCL','Tecno','Toshiba','Ulefone','Vivo','Wiko','Xiaomi','ZTE'],
    'Pets & Animals' => ['Acana','Advance','Beaphar','Bayer','Blue Buffalo','Canidae','Catit','Eukanuba','Feliway','Ferplast','Fluval','Frontline','Furminator','Hill\'s','Iams','JBL','KONG','Mars','Me-O','Monge','NexGard','Orijen','Pedigree','PetSafe','Purina','Purina One','Rogz','Royal Canin','Sera','Sheba','SmartHeart','Tetra','Whiskas','Zoetis'],
    'Property & Real Estate' => ['Aitken Spence','Arpico','Browns','Ceylinco','Damro','Fairway','HomeKEY','John Keells','Lanka Property Web','Laugfs','Prime Group','Softlogic'],
    'Services' => ['Antes','Access Engineering','Aitken Spence','Arpico','Asiri Health','Cargills','Dialog','DHL','DIMO','Hemas','Hayleys','HNB','John Keells','MAS','Mobitel','Nawaloka','PickMe','Samsung','Singer','Softlogic','Sri Lankan Airlines','Unilever'],
    'Sports & Hobbies' => ['Adidas','Apple','Asics','Babolat','Bose','Canon','Casio','Coleman','Converse','Cornilleau','Decathlon','Denon','DJI','Fender','Fitbit','Garmin','GoPro','Google','Head','JBL','Kawasaki','Kettler','Korg','Lacoste','LG','Logitech','Marshall','Mikasa','Mizuno','New Balance','Nike','Nintendo','Nikon','Olympus','Panasonic','Petzl','Philips','Pioneer','Puma','Reebok','Rode','Roland','Salomon','Samsung','Sennheiser','Shimano','Shure','Sigma','Sony','Spalding','Speedo','Suunto','The North Face','Thule','Toshiba','Trek','Under Armour','Vans','Victorinox','Wilson','Xiaomi','Yamaha','Yonex'],
    'Travel, Tourism & Accommodation' => ['Aitken Spence','Amaya','Anantara','Booking.com','Cinnamon','Citrus','Galle Face','Jetwing','Qatar Airways','Emirates','Shangri-La','Sri Lankan Airlines','Taj Hotels','TripAdvisor'],
    'TV, Audio & Home Entertainment' => ['Aiwa','Amazon','Anker','Apple','Arcam','Asus','Audio-Technica','Bang & Olufsen','BenQ','Blaupunkt','Bose','Bowers & Wilkins','Cambridge Audio','Canon','Creative','Denon','Epson','FiiO','Focal','Google','Harman Kardon','Hisense','Hitachi','Huawei','JBL','JVC','Kenwood','Klipsch','LG','Logitech','Marantz','Marshall','Microsoft','Mitsubishi','Onkyo','Panasonic','Philips','Pioneer','Polk Audio','Razer','Samsung','Sanyo','Sharp','Skyworth','Sonos','Sony','SonicGear','TCL','Technics','Toshiba','TP-Link','ViewSonic','Xiaomi','Yamaha'],
    'Vehicle Parts' => ['3M','ACDelco','AISIN','Akebono','Amaron','Apollo','Bosch','Bridgestone','Bando','Castrol','Caterpillar','Continental','Cummins','Denso','Dunlop','Exide','FAG','Gates','Goodyear','Gulf','Hankook','Hella','Hitachi','Honda','Hyundai','Isuzu','JCB','Kawasaki','Kia','Koyo','Lucas','Mahindra','Mann Filter','Mazda','Mercedes-Benz','Michelin','Mitsubishi','Mobil','Monroe','NGK','Nissan','Pirelli','Renault','Sachs','Shell','SKF','Suzuki','Tata','Tesla','Toyota','TVS','Valeo','Volkswagen','Volvo','Wurth','Yamaha','Yokohama','ZF'],
    'Vehicles & Automotive' => ['Audi','Bajaj','Benelli','Bentley','BMW','BYD','Caterpillar','Chevrolet','Chery','Citroen','Dacia','Daihatsu','Ducati','Eicher','Fiat','Ford','Geely','GMC','Harley-Davidson','Hero','Hino','Honda','Husqvarna','Hyundai','Infiniti','Isuzu','Iveco','Jaguar','JCB','Jeep','Kawasaki','Keeway','Kia','KTM','Kubota','Land Rover','Lamborghini','Lexus','Mahindra','Maserati','Mazda','Mercedes-Benz','MG','Mitsubishi','Nissan','Opel','Peugeot','Piaggio','Polaris','Porsche','Proton','Renault','Royal Enfield','Scania','Seat','Skoda','Smart','Suzuki','SYM','Tata','Tesla','Toyota','Triumph','TVS','Vespa','Volkswagen','Volvo','Yamaha','Yadea','Zero Motorcycles'],
    // Accessories sub-categories
    'Computer Accessories' => ['A4Tech','Acer','Acer Predator','ADATA','Anker','Antec','Apple','Arctic','ASRock','ASUS','ASUS ROG','ASUS TUF Gaming','AOC','Audio-Technica','Baseus','Belkin','BenQ','Bloody','Bose','Brother','Canon','Case Logic','Cooler Master','Corsair','Creative','Crucial','D-Link','Dareu','Dell','DeepCool','Edifier','Epson','Ergotron','Essager','EvoFox','Fantech','Frontech','Genius','Gigabyte','Glorious','Goliathus','Havit','Hoco','HP','HyperX','iMICE','JBL','Joyroom','Kensington','Kingston','Kioxia','Kyocera','LaCie','Lian Li','Lexar','LG','Lenovo','Lenovo Legion','Logitech','Logitech G','Microsoft','MSI','Meetion','NZXT','Noctua','North Bayou','Orico','Pantum','Portronics','Razer','Rapoo','Redragon','Remax','Ricoh','RivaCase','Samsung','SanDisk','Samsonite','Seagate','Sennheiser','Silicon Power','Sony','SteelSeries','SwissGear','Targus','TeamGroup','Thermaltake','Toshiba','TP-Link','Transcend','Tomtoc','UAG','UGREEN','Verbatim','Vention','ViewSonic','WD','Western Digital','WIWU','Xerox','Zebronics','Zowie'],
    'Electronic Accessories' => ['Anker','Apple','Aukey','Baseus','Belkin','Bose','Braun','Braven','Canon','Casio','D-Link','Duracell','Energizer','Essager','Fantech','Fujifilm','Garmin','GP Batteries','Hoco','Honor','Huawei','Havit','JBL','JVC','Joyroom','Kioxia','Kodak','Lenovo','LG','Logitech','Marshall','Maxell','Mitsubishi','Motorola','Nikon','Nokia','Oppo','Orico','Panasonic','Philips','Portronics','Realme','Remax','Rode','Samsung','SanDisk','Sanyo','Saramonic','Sennheiser','Sharp','Shure','Skullcandy','Sony','Soundcore','Syska','TCL','Tenda','TP-Link','Transcend','Ugreen','Vention','Verbatim','Vivo','Xiaomi','Yamaha','Zebronics','ZTE'],
    'Fashion & Personal Accessories' => ['Adidas','Aldo','American Tourister','Armani Exchange','Arrow','Bata','Beverly Hills Polo Club','Calvin Klein','Casio','Charles & Keith','Charles Tyrwhitt','Clarks','Converse','Crocs','Daniel Wellington','Delsey','Diesel','Dior','DKNY','Dolce & Gabbana','Dunhill','Ecco','Ellesse','Emporio Armani','Fastrack','Fossil','Furla','G-Shock','Giordano','Guess','Hanes','Havaianas','Herschel','Hugo Boss','Hush Puppies','Jockey','Jordan','Kangol','Kipling','Lacoste','Lee','Levi\'s','Louis Vuitton','Mango','Marc Jacobs','Marks & Spencer','Michael Kors','Mizuno','New Balance','Nike','Nine West','Oakley','Paco Rabanne','Palladium','Puma','Ray-Ban','Reebok','Ralph Lauren','Roxy','Samsonite','Skechers','Slazenger','Steve Madden','Superdry','Swarovski','Swiss Military','Tag Heuer','The North Face','Timberland','Titan','Tommy Hilfiger','Tory Burch','Tumi','Under Armour','United Colors of Benetton','U.S. Polo Assn','Vans','Versace','Victoria\'s Secret','Wildcraft','Wrangler','Yves Saint Laurent','Zara'],
    'Hobby & Pet Accessories' => ['AquaClear','Aquarium Systems','Aqueon','Arcadia','API','Aquael','Beaphar','Bestway','Biorb','Bosch','Catit','Coleman','Dennerle','Dewalt','Eheim','Exo Terra','Fluval','Ferplast','Fiskars','Gardena','Hagen','Hikari','Intex','JBL','Juwel','Karcher','KONG','Kurgo','Leatherman','Marina','Marukan','Meow Mix','Midwest','Nylabone','Oase','Omega One','Orijen','Pawise','Pedigree','PetSafe','Purina','Purina Pro Plan','Reptizoo','Royal Canin','Saki-Hikari','Savic','Schleich','Seresto','Sera','Sheba','Sicce','Tetra','The North Face','Trixie','TropiClean','Tunze','Vitakraft','Whiskas','World\'s Best Cat Litter','Yeti','Zolux'],
    'Home & Kitchen Accessories' => ['3M','Bajaj','Beko','Black+Decker','Borosil','Bormioli Rocco','Brita','Butterfly','Calphalon','Cambro','Clikon','Conair','Corelle','Cuisinart','Dawlance','De\'Longhi','Duralex','Electrolux','Elica','Ember','Faber','Fissler','Hario','Havells','Hettich','Honeywell','IKEA','Ingco','Joseph Joseph','Kenwood','Karcher','Kitchenaid','Korkmaz','Kuvings','Luminarc','Milton','Morphy Richards','Midea','Nespresso','Ninja','Nova','Oster','Panasonic','Prestige','Philips','Pyrex','Rinnai','Russell Hobbs','Samsung','Scotch-Brite','Sharp','Singer','Smeg','Sunbeam','Tefal','Thermos','Toshiba','Tupperware','Usha','V-Guard','Westinghouse','Wonderchef','Zojirushi'],
    'Kids & Baby Accessories' => ['Avent','Baby Cheramy','Babyhug','B.Box','Beaba','Bebesup','Bebeconfort','Chicco','Combi','Cybex','Disney Baby','Dr. Brown\'s','Farlin','Fisher-Price','Graco','Huggies','Himalaya Baby','Infantino','Johnson\'s Baby','Joie','LuvLap','MAM','MamyPoko','Mee Mee','Mothercare','Mustela','NUK','Pampers','Panda','Pears','Pigeon','Philips Avent','Playgro','R for Rabbit','Richell','Safety 1st','Sebamed Baby','Softlove','Taf Toys','Tommee Tippee','VTech','Velona Cuddles','Weleda Baby'],
    'Mobile & Tablet Accessories' => ['Adata','Acer','Anker','Apple','Aukey','Baseus','Belkin','Borofone','Canyon','Casetify','Choetech','Dux Ducis','Essager','Energizer','Hoco','Havit','Honor','Huawei','Infinix','Joyroom','JBL','Kingston','Lenovo','Logitech','Mcdodo','Motorola','Nillkin','Nothing','OnePlus','Oppo','Orico','Portronics','Realme','Remax','Ringke','Samsung','Sandisk','Spigen','Targus','TP-Link','Tronsmart','Ugreen','Vention','Verbatim','Vivo','WiWU','Xiaomi','Zebronics','ZTE'],
    'Sports Accessories' => ['Adidas','Asics','Babolat','Bauer','Black Diamond','Body Sculpture','Bullpadel','CamelBak','Canterbury','Champion','Cosco','Decathlon','Diadora','Dunlop','Everlast','Fitbit','Garmin','Gilbert','Head','Hummel','Joma','Kappa','Kookaburra','Li-Ning','Macron','Mikasa','Molten','Nike','Nivia','NordicTrack','Puma','Reebok','RDX','Skechers','Slazenger','Spalding','Speedo','Stag','Sunflex','The North Face','Titleist','Tunturi','Under Armour','Wilson','Yonex'],
    'Travel Accessories' => ['American Tourister','Antler','Arctic Hunter','Away','Baggallini','Baseus','Bellroy','Briggs & Riley','Cabeau','Case Logic','Cocoon','Delsey','Deuter','Eagle Creek','Fjallraven','Fossil','Go Travel','Herschel','High Sierra','Kipling','Lojel','Lowe Alpine','Mark Ryden','Master Lock','Montblanc','Moshi','Nomatic','Osprey','Pacsafe','Piquadro','Roncato','Samsonite','Sea To Summit','Skybags','Swiss Gear','Swiss Military','The North Face','Thule','Timbuk2','Targus','Travelpro','Tumi','Victorinox','Wildcraft','Wenger','WiWU','Xiaomi','Zomake'],
    'Vehicle Accessories' => ['3M','Abro','Armor All','Autoglym','Bosch','Bridgestone','Castrol','Continental','CRC','Denso','Energizer','Exide','Febreze','Focal','Gates','Goodyear','GS Yuasa','Hella','Honda','Hyundai','JBL','Karcher','K&N','Kenwood','Liqui Moly','Lucas','Mahle','Mann Filter','Meguiar\'s','Michelin','Mobil','Motul','NGK','Nilfisk','Osram','Panasonic','Philips','Pioneer','Prestone','Rain-X','Shell','Sonax','STP','Turtle Wax','Valeo','Varta','WD-40','Wurth','Yokohama'],
];

// --- Prepared statements ---
$stmtBrandCheck  = $pdo->prepare("SELECT id FROM brands WHERE name = ? LIMIT 1");
$stmtBrandInsert = $pdo->prepare("INSERT IGNORE INTO brands (name, slug, is_active, sort_order) VALUES (?, ?, 1, 0)");
$stmtCatFindRoot = $pdo->prepare("SELECT id FROM categories WHERE parent_id IS NULL AND name LIKE ? LIMIT 1");
$stmtCatFindAny  = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ? LIMIT 1");
$stmtPivot       = $pdo->prepare("INSERT IGNORE INTO brand_category (brand_id, category_id) VALUES (?, ?)");
$stmtRootCatIds  = $pdo->query("SELECT id FROM categories WHERE parent_id IS NULL");

$totalBrands   = 0;
$totalPivot    = 0;
$missingCats   = [];

foreach ($data as $categoryName => $brands) {
    // Try root first, then any sub-category
    $stmtCatFindRoot->execute([$categoryName]);
    $catId = $stmtCatFindRoot->fetchColumn();
    if (!$catId) {
        $stmtCatFindAny->execute([$categoryName]);
        $catId = $stmtCatFindAny->fetchColumn();
    }

    if (!$catId) {
        echo "WARNING: Category not found: $categoryName\n";
        $missingCats[] = $categoryName;
        $catId = null;
    } else {
        echo "Category: $categoryName (id=$catId)\n";
    }

    foreach ($brands as $name) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower($name)));
        $slug = trim($slug, '-');

        // Upsert brand
        $stmtBrandCheck->execute([$name]);
        $brandId = $stmtBrandCheck->fetchColumn();

        if (!$brandId) {
            $stmtBrandInsert->execute([$name, $slug]);
            $brandId = $pdo->lastInsertId();
            $totalBrands++;
        }

        // Link to category
        if ($catId && $brandId) {
            $stmtPivot->execute([$brandId, $catId]);
            $totalPivot++;
        }
    }
}

// --- Insert "Other" brand and link to ALL root categories ---
$stmtBrandCheck->execute(['Other']);
$otherId = $stmtBrandCheck->fetchColumn();
if (!$otherId) {
    $stmtBrandInsert->execute(['Other', 'other']);
    $otherId = $pdo->lastInsertId();
    $totalBrands++;
    echo "Inserted brand: Other\n";
}
$rootIds = $stmtRootCatIds->fetchAll(PDO::FETCH_COLUMN);
foreach ($rootIds as $rid) {
    $stmtPivot->execute([$otherId, $rid]);
    $totalPivot++;
}
echo "Linked 'Other' to " . count($rootIds) . " root categories\n";

echo "\n--- Done ---\n";
echo "New brands inserted : $totalBrands\n";
echo "Pivot rows inserted : $totalPivot\n";
if ($missingCats) {
    echo "Missing categories  : " . implode(', ', $missingCats) . "\n";
}

// --- Self-neutralize ---
file_put_contents(__FILE__, '<?php // neutralized ' . date('Y-m-d H:i:s'));
echo "Script neutralized.\n";
