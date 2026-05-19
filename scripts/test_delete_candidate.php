<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\AdminCandidateController;

$email = 'test-delete-'.time()."@example.com";
$matricule = 'tdel'.time();

// Créer l'utilisateur de test
$u = User::create([
    'nom' => 'Test',
    'prenom' => 'Delete',
    'matricule' => $matricule,
    'telephone' => '000',
    'email' => $email,
]);

echo "CREATED:" . $u->id . PHP_EOL;

// Appeler la méthode destroy du contrôleur
$controller = new AdminCandidateController();
try {
    $controller->destroy($u);
    echo "DESTROY_CALLED" . PHP_EOL;
} catch (\Exception $e) {
    echo "DESTROY_ERROR:" . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}

$exists = User::where('email', $email)->exists();
echo "EXISTS:" . ($exists ? 1 : 0) . PHP_EOL;

return 0;
