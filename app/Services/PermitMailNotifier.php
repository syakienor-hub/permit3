<?php

namespace App\Services;

use App\Mail\PermitDibatalkanMail;
use App\Mail\PermitMasukMail;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PermitMailNotifier
{
    /**
     * Kirim notifikasi email ke semua Staff HSE aktif.
     * Dipanggil saat permit baru masuk ke status "Review Staff".
     */
    public static function notifyStaff(Permit $permit): void
    {
        self::sendToRole($permit, 'staff');
    }

    /**
     * Kirim notifikasi email ke semua Manager HSE aktif.
     * Dipanggil saat permit diteruskan ke status "Review Manager".
     */
    public static function notifyManager(Permit $permit): void
    {
        self::sendToRole($permit, 'manager');
    }

    /**
     * Kirim notifikasi ke HSE bahwa permit dibatalkan oleh pemohon publik.
     */
    public static function notifyCancellation(Permit $permit): void
    {
        try {
            $recipients = array_values(array_unique(array_merge(
                self::recipientsForRole($permit, 'staff'),
                self::recipientsForRole($permit, 'manager'),
            )));

            if (empty($recipients)) {
                Log::warning("PermitMailNotifier: tidak ada penerima aktif untuk pembatalan permit {$permit->no_permit}.");
                return;
            }

            Mail::to($recipients)->send(new PermitDibatalkanMail($permit));
        } catch (\Throwable $e) {
            Log::warning('PermitMailNotifier gagal mengirim email pembatalan: ' . $e->getMessage(), [
                'permit_id' => $permit->id,
            ]);
        }
    }

    protected static function sendToRole(Permit $permit, string $role): void
    {
        try {
            $recipients = self::recipientsForRole($permit, $role);

            if (empty($recipients)) {
                Log::warning("PermitMailNotifier: tidak ada penerima aktif untuk role {$role}, permit {$permit->no_permit} dilewati.");
                return;
            }

            Mail::to($recipients)->send(new PermitMasukMail($permit, $role));
        } catch (\Throwable $e) {
            // Jangan gagalkan alur submit/approve hanya karena email gagal.
            Log::warning('PermitMailNotifier gagal mengirim email: ' . $e->getMessage(), [
                'permit_id' => $permit->id,
                'role' => $role,
            ]);
        }
    }

    /**
     * @return array<int, string>
     */
    protected static function recipientsForRole(Permit $permit, string $role): array
    {
        $query = User::where('role', $role)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        // Staff HSE hanya menerima notif untuk site-nya sendiri.
        // Manager (site null) menerima semua site.
        if ($role === 'staff' && ! empty($permit->site)) {
            $query->where('site', $permit->site);
        }

        return $query->pluck('email')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
