<?php
// One-time: ensure roles table + permissions column exist, seed built-in roles.
$lock = __DIR__ . '/setup_roles.lock';
if (file_exists($lock)) die('Already run.');
file_put_contents($lock, date('Y-m-d H:i:s'));

require __DIR__ . '/../app_core/vendor/autoload.php';
$app = require __DIR__ . '/../app_core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasTable('roles')) {
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->string('name');
        $table->text('description')->nullable();
        $table->boolean('is_admin_level')->default(false);
        $table->boolean('is_super')->default(false);
        $table->boolean('is_protected')->default(false);
        $table->unsignedInteger('sort_order')->default(0);
        $table->text('permissions')->nullable();
        $table->timestamps();
    });
    echo "Created roles table.<br>";
} elseif (!Schema::hasColumn('roles', 'permissions')) {
    Schema::table('roles', function (Blueprint $table) {
        $table->text('permissions')->nullable();
    });
    echo "Added permissions column.<br>";
} else {
    echo "Table and column already exist.<br>";
}

$allPermissions = array_keys(\App\Models\Role::PERMISSIONS);
$adminPermissions = array_values(array_diff($allPermissions, ['roles']));

$seed = [
    ['key' => 'user', 'name' => 'User', 'description' => 'Regular buyer/seller account with dashboard access only.', 'is_admin_level' => 0, 'is_super' => 0, 'is_protected' => 1, 'sort_order' => 1, 'permissions' => json_encode([])],
    ['key' => 'admin', 'name' => 'Admin', 'description' => 'Full admin panel access except role management.', 'is_admin_level' => 1, 'is_super' => 0, 'is_protected' => 1, 'sort_order' => 2, 'permissions' => json_encode($adminPermissions)],
    ['key' => 'super_admin', 'name' => 'Super Admin', 'description' => 'Full control including roles and settings.', 'is_admin_level' => 1, 'is_super' => 1, 'is_protected' => 1, 'sort_order' => 3, 'permissions' => json_encode($allPermissions)],
];

foreach ($seed as $row) {
    $exists = DB::table('roles')->where('key', $row['key'])->first();
    if (!$exists) {
        $row['created_at'] = now();
        $row['updated_at'] = now();
        DB::table('roles')->insert($row);
        echo "Seeded role: {$row['name']}<br>";
    } else {
        // make sure built-in admin role has its permissions populated
        if (in_array($row['key'], ['admin', 'super_admin']) && empty(json_decode($exists->permissions ?? '[]'))) {
            DB::table('roles')->where('key', $row['key'])->update(['permissions' => $row['permissions']]);
            echo "Updated permissions for: {$row['name']}<br>";
        } else {
            echo "Role exists: {$row['name']}<br>";
        }
    }
}

echo "<br><b>Done.</b>";
