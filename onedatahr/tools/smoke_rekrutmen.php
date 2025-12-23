<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\RekrutmenDailyController;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "Running RekrutmenDaily smoke tests...\n";
$ctl = new RekrutmenDailyController();

// 1) index: this should return existing rows
$req = Request::create('/rekrutmen/daily', 'GET', ['month' => date('n'), 'year' => date('Y')]);
$res = $ctl->index($req);
echo "Index response HTTP status: " . ($res->getStatusCode()) . "\n";
$data = $res->getData();
echo "Index returned " . count($data) . " rows for current month/year\n";

// 2) store: simulate admin
$admin = User::where('role','admin')->first();
if (!$admin) {
    echo "No admin user found — cannot run store/update/destroy smoke tests\n";
    exit(0);
}
Auth::setUser($admin);
$pos = Posisi::first();
if (!$pos) {
    echo "No posisi found — cannot run store test\n";
    exit(0);
}
$date = date('Y-m-d');
$req2 = Request::create('/rekrutmen/daily', 'POST', ['posisi_id' => $pos->id_posisi, 'date' => $date, 'count' => 3]);
$res2 = $ctl->store($req2);
echo "Store response: status=" . $res2->getStatusCode() . "\n";
$body = $res2->getData();
print_r($body);
$entryId = $body->entry->id ?? null;
if ($entryId) {
    // update
    $req3 = Request::create('/rekrutmen/daily/'.$entryId, 'PUT', ['count'=>5, 'notes'=>'smoke update']);
    $res3 = $ctl->update($req3, $entryId);
    echo "Update status: " . $res3->getStatusCode() . "\n";
    print_r($res3->getData());

    // destroy
    $res4 = $ctl->destroy($entryId);
    echo "Destroy status: " . (is_object($res4) && method_exists($res4,'getStatusCode') ? $res4->getStatusCode() : 'n/a') . "\n";
    if (is_object($res4) && method_exists($res4,'getData')) print_r($res4->getData()); else echo "Destroy returned non-JSON response\n";
} else {
    echo "No entry id returned from store — skipping update/destroy\n";
}

// 3) confirm non-admin cannot store
$nonAdmin = User::whereNull('role')->first();
if (!$nonAdmin) {
    echo "No non-admin user found to test authorization negative case\n";
} else {
    Auth::setUser($nonAdmin);
    try {
        $req5 = Request::create('/rekrutmen/daily', 'POST', ['posisi_id' => $pos->id_posisi, 'date' => $date, 'count' => 2]);
        $ctl->store($req5);
        echo "ERROR: non-admin was able to store RekrutmenDaily\n";
    } catch (\Exception $e) {
        echo "Non-admin store failed as expected: " . $e->getMessage() . "\n";
    }
}
