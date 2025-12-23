<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\PosisiController;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "Running Posisi smoke tests...\n";
$ctl = new PosisiController();

// Ensure we have admin
$admin = User::where('role','admin')->first();
if (!$admin) {
    echo "No admin user found — cannot run posisi tests\n";
    exit(0);
}
Auth::setUser($admin);

// Create a posisi (use unique name to avoid collisions)
$uniqueName = 'Smoke Test Position ' . time();
try {
    $req = Request::create('/rekrutmen/posisi', 'POST', ['nama_posisi' => $uniqueName]);
    $res = $ctl->store($req);
    echo "Store status: " . $res->getStatusCode() . "\n";
    $body = $res->getData();
    print_r($body);
    $id = $body->posisi->id_posisi ?? null;

    if ($id) {
        // Update
        $req2 = Request::create('/rekrutmen/posisi/'.$id, 'PUT', ['nama_posisi' => $uniqueName . ' Updated']);
        $res2 = $ctl->update($req2, $id);
        echo "Update status: " . $res2->getStatusCode() . "\n";
        print_r($res2->getData());

        // Delete
        $reqDel = Request::create('/rekrutmen/posisi/'.$id, 'DELETE', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $res3 = $ctl->destroy($reqDel, $id);
        echo "Delete status: " . $res3->getStatusCode() . "\n";
        if (method_exists($res3, 'getData')) {
            print_r($res3->getData());
        } else {
            echo "Delete response not JSON, class: " . get_class($res3) . "\n";
        }
    } else {
        echo "No id returned from store; aborting update/delete\n";
    }
} catch (\Illuminate\Validation\ValidationException $ve) {
    echo "Store validation failed: " . $ve->getMessage() . "\n";
}

// Test non-admin can't store
$nonAdmin = User::where('role',null)->first();
if ($nonAdmin) {
    Auth::setUser($nonAdmin);
    try {
        $req3 = Request::create('/rekrutmen/posisi', 'POST', ['nama_posisi'=>'Should Fail']);
        $ctl->store($req3);
        echo "ERROR: non-admin was able to create posisi\n";
    } catch (\Exception $e) {
        echo "Non-admin create failed as expected: " . $e->getMessage() . "\n";
    }
}
