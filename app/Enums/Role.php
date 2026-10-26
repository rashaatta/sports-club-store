<?php

namespace App\Enums;

use App\Support\Permission;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case StoreManager = 'store_manager';
    case ProductManager = 'product_manager';
    case InventoryManager = 'inventory_manager';
    case ContentManager = 'content_manager';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'مدير عام',
            self::StoreManager => 'مدير المتجر',
            self::ProductManager => 'مسؤول منتجات',
            self::InventoryManager => 'مسؤول مخزون',
            self::ContentManager => 'مسؤول محتوى',
        };
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => Permission::all(),
            self::StoreManager => [
                Permission::VIEW_ADMIN,
                Permission::VIEW_DASHBOARD,
                Permission::MANAGE_PRODUCTS,
                Permission::MANAGE_CATEGORIES,
                Permission::MANAGE_INVENTORY,
                Permission::MANAGE_CONTENT,
                Permission::MANAGE_SETTINGS,
                Permission::VIEW_MESSAGES,
                Permission::VIEW_ANALYTICS,
            ],
            self::ProductManager => [
                Permission::VIEW_ADMIN,
                Permission::VIEW_DASHBOARD,
                Permission::MANAGE_PRODUCTS,
                Permission::MANAGE_CATEGORIES,
                Permission::MANAGE_INVENTORY,
                Permission::VIEW_ANALYTICS,
            ],
            self::InventoryManager => [
                Permission::VIEW_ADMIN,
                Permission::VIEW_DASHBOARD,
                Permission::MANAGE_INVENTORY,
            ],
            self::ContentManager => [
                Permission::VIEW_ADMIN,
                Permission::VIEW_DASHBOARD,
                Permission::MANAGE_CONTENT,
                Permission::VIEW_ANALYTICS,
            ],
        };
    }

    public function has(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }
}
