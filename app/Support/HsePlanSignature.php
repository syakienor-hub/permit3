<?php

namespace App\Support;

class HsePlanSignature
{
    public static function manager(): ?string
    {
        $path = config('hse_plan.manager_signature_path');

        if (! $path || ! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
