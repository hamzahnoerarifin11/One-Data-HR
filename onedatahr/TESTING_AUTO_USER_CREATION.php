<?php

/**
 * TESTING GUIDE: Auto User Account Creation
 *
 * File ini menunjukkan cara test fitur auto user creation
 * Run menggunakan: php artisan tinker
 */

// ============================================
// 1. TEST: Create User dari Helper
// ============================================

// Ambil karyawan existing
$karyawan = \App\Models\Karyawan::first();
$level = \App\Models\Level::first();

// Test helper create user
$result = \App\Helpers\UserHelper::createUserForKaryawan($karyawan, $level);

// Check result
dd($result);
// Output:
// [
//   'success' => true,
//   'user' => User object,
//   'email' => 'nama.ke-1@company.local',
//   'password' => 'GeneratedPassword123!',
//   'roles' => ['admin'],
//   'message' => 'User berhasil dibuat'
// ]

// ============================================
// 2. TEST: Check User Role Assignment
// ============================================

$user = \App\Models\User::latest()->first();

// Get user roles
$user->roles; // Get all roles
$user->roles->pluck('name'); // Get role names
$user->hasRole('admin'); // Check specific role

// ============================================
// 3. TEST: Map Level to Role
// ============================================

// Get semua level dan lihat role mapping
$levels = \App\Models\Level::all();

foreach ($levels as $level) {
    $roles = \App\Helpers\UserHelper::mapLevelToRole($level);
    echo "Level: {$level->name} → Roles: " . implode(', ', $roles) . "\n";
}

// ============================================
// 4. TEST: Generate Password
// ============================================

// Generate multiple passwords untuk test
for ($i = 0; $i < 5; $i++) {
    echo \App\Helpers\UserHelper::generatePassword(12) . "\n";
}

// Output example:
// aB3cd!@Ef5Gh
// xY9zL@2mNoPq
// ...

// ============================================
// 5. TEST: Email Generation
// ============================================

// Test unique email generation
$karyawan1 = \App\Models\Karyawan::find(1);
$karyawan2 = \App\Models\Karyawan::find(2);

// Jika 2 karyawan dengan nama sama
// Email akan auto-increment: nama.ke-1@, nama.ke-2@, dst

// ============================================
// 6. TEST: Role Update on Level Change
// ============================================

// Mimic update karyawan level
$karyawan = \App\Models\Karyawan::find(1);
$user = \App\Models\User::where('nik', $karyawan->NIK)->first();
$newLevel = \App\Models\Level::find(2);

// Get new roles
$roleNames = \App\Helpers\UserHelper::mapLevelToRole($newLevel);
$roles = \App\Models\Role::whereIn('name', $roleNames)->pluck('id')->toArray();

// Sync roles (replace old with new)
$user->roles()->sync($roles);

// Verify
$user->roles->pluck('name'); // Check updated roles

// ============================================
// 7. TEST: Database Validation
// ============================================

// Check users created
\App\Models\User::count(); // Total users
\App\Models\User::whereNull('email')->count(); // Users without email
\App\Models\User::where('password', 'like', '%:%')->count(); // Check hashed

// Check role assignments
\App\Models\Role::with('users')->get(); // Get roles with users
\App\Models\Role::find(1)->users->count(); // Count users per role

// ============================================
// 8. TEST: Pivot Table
// ============================================

// Check role_user junction table
\DB::table('role_user')->get();
\DB::table('role_user')->where('user_id', 1)->get();
\DB::table('role_user')->where('role_id', 1)->get();

// Count relationships
\DB::table('role_user')->count();

// ============================================
// 9. BULK TEST: Create Multiple Users
// ============================================

// Create multiple karyawan dengan auto user
$karyawans = \App\Models\Karyawan::limit(5)->get();

foreach ($karyawans as $karyawan) {
    $level = $karyawan->pekerjaan->first()->level;
    $result = \App\Helpers\UserHelper::createUserForKaryawan($karyawan, $level);

    echo "✓ Created: {$result['email']} with role: " . implode(',', $result['roles']) . "\n";
}

// ============================================
// 10. TEST: Validation & Error Handling
// ============================================

// Test dengan null level
$result = \App\Helpers\UserHelper::createUserForKaryawan($karyawan, null);
dd($result); // Should still work, use default role

// Test dengan invalid data
try {
    $invalidKaryawan = new \App\Models\Karyawan();
    \App\Helpers\UserHelper::createUserForKaryawan($invalidKaryawan, $level);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}

// ============================================
// 11. QUERY: Find User by Karyawan
// ============================================

// Find user by NIK
$nik = '123456789';
$user = \App\Models\User::where('nik', $nik)->first();

// Find user by email
$user = \App\Models\User::where('email', 'nama@company.local')->first();

// ============================================
// 12. UPDATE: Change User Password
// ============================================

$user = \App\Models\User::find(1);

// Force password change
$user->update([
    'password' => \Hash::make('NewPassword123!')
]);

// ============================================
// 13. DELETE: Cleanup (jika perlu)
// ============================================

// Delete user (cascade akan delete role_user entries)
$user = \App\Models\User::find(1);
$user->delete();

// Delete role (if no users assigned)
$role = \App\Models\Role::where('name', 'test_role')->first();
$role?->delete();

// ============================================
// 14. MONITORING: Check System Health
// ============================================

// Count summary
echo "Total Users: " . \App\Models\User::count() . "\n";
echo "Total Karyawan: " . \App\Models\Karyawan::count() . "\n";
echo "Total Roles: " . \App\Models\Role::count() . "\n";
echo "Total Role Assignments: " . \DB::table('role_user')->count() . "\n";

// Orphaned users (karyawan without user)
$orphanedKaryawans = \App\Models\Karyawan::whereNotIn('NIK',
    \App\Models\User::pluck('nik')
)->get();
echo "Orphaned Karyawans: " . $orphanedKaryawans->count() . "\n";

// Users without role
$usersWithoutRole = \App\Models\User::doesntHave('roles')->get();
echo "Users without role: " . $usersWithoutRole->count() . "\n";

// ============================================
// 15. EXPORT: Get Credentials for Email
// ============================================

$user = \App\Models\User::latest()->first();
$roles = $user->roles->pluck('name')->toArray();

$credentials = [
    'name' => $user->name,
    'email' => $user->email,
    'password' => '(SECURE)', // Password tidak bisa di-retrieve
    'roles' => $roles,
    'login_url' => route('login'),
];

// Bisa diekspor ke email/CSV/PDF
dd($credentials);

?>
