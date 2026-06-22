<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check all roles
echo "=== ALL ROLES ===\n";
$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $r) {
    echo "- " . $r->name . "\n";
}

echo "\n=== USERS WITH DIREKTUR/DIRECTOR ROLE ===\n";
$users = App\Models\User::with('roles')->get();
foreach ($users as $u) {
    $roleNames = $u->roles->pluck('name')->toArray();
    if (in_array('Direktur', $roleNames) || in_array('Director', $roleNames) || in_array('GM', $roleNames)) {
        echo $u->name . " => [" . implode(', ', $roleNames) . "]\n";
    }
}
