<?php

namespace App\Support;

final class Permission
{
    public const VIEW_ADMIN = 'view_admin';

    public const VIEW_DASHBOARD = 'view_dashboard';

    public const MANAGE_PRODUCTS = 'manage_products';

    public const MANAGE_CATEGORIES = 'manage_categories';

    public const MANAGE_INVENTORY = 'manage_inventory';

    public const MANAGE_CONTENT = 'manage_content';

    public const MANAGE_SETTINGS = 'manage_settings';

    public const MANAGE_USERS = 'manage_users';

    public const VIEW_MESSAGES = 'view_messages';

    public const VIEW_ANALYTICS = 'view_analytics';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::VIEW_ADMIN,
            self::VIEW_DASHBOARD,
            self::MANAGE_PRODUCTS,
            self::MANAGE_CATEGORIES,
            self::MANAGE_INVENTORY,
            self::MANAGE_CONTENT,
            self::MANAGE_SETTINGS,
            self::MANAGE_USERS,
            self::VIEW_MESSAGES,
            self::VIEW_ANALYTICS,
        ];
    }
}
