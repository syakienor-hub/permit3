<?php

namespace App\Services;

use App\Models\Permit;
use DomainException;
use Illuminate\Support\Facades\DB;

class PermitCancellationService
{
    /**
     * Batalkan permit secara langsung dan simpan jejak tanda tangan.
     *
     * @param  array{role: string, name: string, signature: string, date?: string, ip?: string|null}  $signature
     *
     * @throws DomainException jika permit sudah ditutup atau dibatalkan
     */
    public function cancel(Permit $permit, string $reason, array $signature): Permit
    {
        return DB::transaction(function () use ($permit, $reason, $signature) {
            $locked = Permit::whereKey($permit->getKey())->lockForUpdate()->firstOrFail();

            if (in_array($locked->status, ['Closed', 'Cancelled'], true)) {
                throw new DomainException('Permit yang sudah ditutup atau dibatalkan tidak dapat dibatalkan lagi.');
            }

            $signatures = $locked->cancellation_signatures ?? [];
            $signature['date'] = $signature['date'] ?? now()->toDateTimeString();
            $signatures[] = $signature;

            $locked->forceFill([
                'status' => 'Cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
                'cancellation_signatures' => $signatures,
            ])->save();

            return $locked;
        });
    }
}
