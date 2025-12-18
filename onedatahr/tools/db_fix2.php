<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    Illuminate\Support\Facades\DB::statement('ALTER TABLE rekrutmen_daily MODIFY posisi_id INT NOT NULL');
    echo "Modified posisi_id to INT (signed)\n";
    Illuminate\Support\Facades\DB::statement('ALTER TABLE rekrutmen_daily ADD CONSTRAINT rekrutmen_daily_posisi_id_foreign FOREIGN KEY (posisi_id) REFERENCES posisi(id_posisi) ON DELETE CASCADE');
    echo "Added foreign key constraint to posisi(id_posisi)\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
