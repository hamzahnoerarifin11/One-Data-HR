<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\RecruitmentDashboardController;
use App\Http\Controllers\PosisiController;
use Illuminate\Support\Facades\Auth;

$admin = \App\Models\User::where('role','admin')->first();
if ($admin) Auth::setUser($admin);

$recruit = new RecruitmentDashboardController();
$pos = new PosisiController();

$pages = [
    'dashboard' => [$recruit, 'index'],
    'posisi_manage' => [$pos, 'manage'],
    'calendar' => [$recruit, 'calendarPage'],
    'cv_page' => [$recruit, 'cvPage'],
    'psikotes_page' => [$recruit, 'psikotesPage'],
    'kompetensi_page' => [$recruit, 'kompetensiPage'],
    'interview_hr_page' => [$recruit, 'interviewHrPage'],
    'interview_user_page' => [$recruit, 'interviewUserPage'],
    'pemberkasan_page' => [$recruit, 'pemberkasanPage'],
];

foreach ($pages as $name => $call) {
    try {
        $res = call_user_func($call);
        $status = method_exists($res, 'getStatusCode') ? $res->getStatusCode() : 200;
        echo "$name -> status $status" . PHP_EOL;
    } catch (Exception $e) {
        echo "$name -> Exception: " . $e->getMessage() . PHP_EOL;
    }
}
