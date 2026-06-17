<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UserAdmin;

$admin = UserAdmin::first();
if ($admin) {
    echo $admin->createToken('TestToken')->plainTextToken;
} else {
    echo "ERROR: Aucun administrateur trouvé.";
}
