<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['admin', 'manager', 'chef', 'cashier', 'customer'] as $role) {
            Role::findOrCreate($role, 'web');
        }

        User::query()
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $user->syncLegacyRole();
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data backfill migration; no safe reverse operation needed.
    }
};
