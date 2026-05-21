<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Creating order_settings table...\n";

// Check if table exists
if (Schema::hasTable('order_settings')) {
    echo "Table already exists!\n";
} else {
    // Create the table
    Schema::create('order_settings', function ($table) {
        $table->id();
        $table->boolean('order_status')->default(true);
        $table->text('order_off_message')->nullable();
        $table->timestamps();
    });
    
    echo "Table created successfully!\n";
}

// Check if default record exists
$exists = DB::table('order_settings')->exists();
if (!$exists) {
    DB::table('order_settings')->insert([
        'order_status' => true,
        'order_off_message' => 'Order creation is currently disabled. Please try again later.',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Default record inserted!\n";
} else {
    echo "Default record already exists!\n";
}

// Add migration record
$migrationExists = DB::table('migrations')
    ->where('migration', '2025_01_22_000001_create_order_settings_table')
    ->exists();

if (!$migrationExists) {
    $batch = DB::table('migrations')->max('batch') + 1;
    DB::table('migrations')->insert([
        'migration' => '2025_01_22_000001_create_order_settings_table',
        'batch' => $batch,
    ]);
    echo "Migration record added!\n";
} else {
    echo "Migration record already exists!\n";
}

echo "\n✅ Setup complete! You can now use the Order Settings feature.\n";
