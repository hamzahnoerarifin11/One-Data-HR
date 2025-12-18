<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$pages = [
    '/' => 'Sign In or Dashboard redirect',
    '/dashboard' => 'Dashboard',
    '/rekrutmen' => 'Recruitment Dashboard',
    '/rekrutmen/posisi' => 'Manage Posisi',
    '/rekrutmen/calendar' => 'Kalender Rekrutmen Page',
    '/rekrutmen/metrics/cv-page' => 'CV Page',
    '/rekrutmen/metrics/psikotes-page' => 'Psikotes Page',
    '/rekrutmen/metrics/kompetensi-page' => 'Kompetensi Page',
    '/rekrutmen/metrics/interview-hr-page' => 'Interview HR Page',
    '/rekrutmen/metrics/interview-user-page' => 'Interview User Page',
    '/rekrutmen/metrics/pemberkasan-page' => 'Pemberkasan Monitor Page',
];

// login as admin for pages that require auth
$admin = \App\Models\User::where('role','admin')->first();
if ($admin) Auth::setUser($admin);

foreach ($pages as $path => $name) {
    $req = Request::create($path, 'GET');
    $resp = $kernel->handle($req);
    $status = $resp->getStatusCode();
    $content = (string) $resp->getContent();
    $ok = $status === 200 ? 'OK' : 'FAIL';
    echo "$name ($path) -> $status [$ok]\n";
    // look for likely strings
    if ($status === 200) {
        if (stripos($content, 'Kalender') !== false) echo "  contains 'Kalender'\n";
        if (stripos($content, 'Posisi') !== false) echo "  contains 'Posisi'\n";
        if (stripos($content, 'Pemberkasan') !== false) echo "  contains 'Pemberkasan'\n";
    }
}
