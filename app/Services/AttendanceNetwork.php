<?php

namespace App\Services;

class AttendanceNetwork
{
    public function isAllowed(?string $ip, array $allowedNetworks): bool
    {
        if (! $ip) {
            return false;
        }

        // If no networks are configured, deny access (must be explicitly configured)
        if ($allowedNetworks === []) {
            return false;
        }

        foreach ($allowedNetworks as $network) {
            if ($this->matches($ip, $network)) {
                return true;
            }
        }

        return false;
    }

    private function matches(string $ip, string $network): bool
    {
        if (! str_contains($network, '/')) {
            return $ip === $network;
        }

        [$subnet, $mask] = explode('/', $network, 2);

        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
            ! filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $mask = (int) $mask;

        if ($mask < 0 || $mask > 32) {
            return false;
        }

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - $mask);

        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
}
