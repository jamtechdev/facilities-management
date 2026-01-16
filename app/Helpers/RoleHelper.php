<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Map database role names to display names
     *
     * @param string|null $roleName
     * @return string
     */
    public static function getDisplayName(?string $roleName): string
    {
        if (!$roleName) {
            return 'User';
        }

        $mapping = [
            'SuperAdmin' => 'Admin',
            'superadmin' => 'Admin',
            'Admin' => 'Manager',
            'admin' => 'Manager',
            'Staff' => 'Staff',
            'staff' => 'Staff',
            'Client' => 'Client',
            'client' => 'Client',
            'Lead' => 'Lead',
            'lead' => 'Lead',
        ];

        return $mapping[$roleName] ?? $roleName;
    }

    /**
     * Get display name for current user's role
     *
     * @return string
     */
    public static function getCurrentUserRoleDisplay(): string
    {
        $user = auth()->user();
        if (!$user) {
            return 'Guest';
        }

        $role = $user->roles->first();
        if (!$role) {
            return 'User';
        }

        return self::getDisplayName($role->name);
    }
}
