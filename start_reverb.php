<?php

/*
 * Script de démarrage manuel du serveur Reverb WebSocket
 * À utiliser si artisan reverb:start ne fonctionne pas
 */

// Charger l'autoloader
require __DIR__.'/vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__.'/bootstrap/app.php';

// Créer le répertoire cache s'il n'existe pas
$cacheDir = __DIR__.'/bootstrap/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Créer les fichiers de cache s'ils n'existent pas
$packagesFile = $cacheDir.'/packages.php';
$servicesFile = $cacheDir.'/services.php';

if (!file_exists($packagesFile)) {
    file_put_contents($packagesFile, "<?php return [];\n");
}

if (!file_exists($servicesFile)) {
    file_put_contents($servicesFile, "<?php return [];\n");
}

echo "✅ Cache directory created successfully\n";
echo "✅ Starting Reverb WebSocket Server...\n";
echo "\n";
echo "Server will run on: ws://127.0.0.1:8080\n";
echo "Press Ctrl+C to stop\n";
echo "\n";

// Bootloader l'application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Exécuter la commande reverb:start
$status = $kernel->call('reverb:start', [
    '--host' => '127.0.0.1',
    '--port' => 8080
]);

exit($status);
