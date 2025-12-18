<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$count = Illuminate\Support\Facades\DB::table('rekrutmen_daily')->count();
echo "rekrutmen_daily count: $count\n";
