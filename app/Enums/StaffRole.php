<?php

namespace App\Enums;

enum StaffRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case Agent = 'agent';
    case Developer = 'developer';

    public function canAccessAllSites(): bool
    {
        return match ($this) {
            self::SuperAdmin, self::Admin => true,
            default => false,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Agent => 'Agent',
            self::Developer => 'Developer',
        };
    }
}
