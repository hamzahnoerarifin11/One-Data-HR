<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
// ensure migrations table exists
try {
    DB::statement('CREATE TABLE IF NOT EXISTS migrations (id int unsigned not null auto_increment primary key, migration varchar(255) not null, batch int not null)');
    echo "Ensured migrations table exists\n";
    $mig = '2025_12_17_000000_create_rekrutmen_daily_table';
    $exists = DB::table('migrations')->where('migration', $mig)->exists();
    if (!$exists) {
        $max = DB::table('migrations')->max('batch') ?: 0;
        DB::table('migrations')->insert(['migration' => $mig, 'batch' => $max+1]);
        echo "Inserted migration entry for $mig\n";
    } else {
        echo "Migration entry already exists\n";
    }
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage().'\n';
}
