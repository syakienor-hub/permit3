<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $signaturePath = public_path('assets/images/ttd-manager.png');
        $signature = is_file($signaturePath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath))
            : null;

        DB::table('hse_plans')
            ->where('status', 'Review Manager')
            ->update([
                'status' => 'Final',
                'manager_signature' => $signature,
                'reviewed_at' => DB::raw('COALESCE(reviewed_at, submitted_at, created_at)'),
                'updated_at' => now(),
            ]);

        DB::table('contractors')
            ->whereNotNull('hse_plan_id')
            ->whereIn('status', ['Review Manager', 'HSE Plan Diajukan'])
            ->update([
                'status' => 'HSE Plan Final',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
