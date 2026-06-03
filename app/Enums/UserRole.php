<?php

namespace App\Enums;

use InvalidArgumentException;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Member = 'member';

    public function slug(): string
    {
        return match ($this) {
            self::SuperAdmin => 'super-admin',
            self::Admin => 'admin',
            self::Member => 'member',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Member => 'Member',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Manage clients and platform settings',
            self::Admin => 'Manage your company team and short URLs',
            self::Member => 'Create and manage your short URLs',
        };
    }

    public static function fromSlug(string $slug): self
    {
        return match ($slug) {
            'super-admin' => self::SuperAdmin,
            'admin' => self::Admin,
            'member' => self::Member,
            default => throw new InvalidArgumentException("Invalid role slug: {$slug}"),
        };
    }

    /** @return list<self> */
    public static function loginOptions(): array
    {
        return [
            self::SuperAdmin,
            self::Admin,
            self::Member,
        ];
    }

    /** @return list<self> */
    public static function registerableRoles(): array
    {
        return [
            self::Admin,
            self::Member,
        ];
    }

    public function canRegister(): bool
    {
        return in_array($this, self::registerableRoles(), true);
    }

    public static function fromRegisterSlug(string $slug): self
    {
        $role = self::fromSlug($slug);

        if (! $role->canRegister()) {
            throw new InvalidArgumentException("Role cannot register: {$slug}");
        }

        return $role;
    }
}
