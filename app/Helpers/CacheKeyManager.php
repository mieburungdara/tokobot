<?php

namespace TokoBot\Helpers;

/**
 * Class CacheKeyManager
 * 
 * A central place to manage cache keys for the application.
 * This prevents key collisions and makes refactoring easier.
 */
class CacheKeyManager
{
    /**
     * Generates the cache key for dashboard statistics.
     *
     * @return string
     */
    public static function forDashboardStats(): string
    {
        return 'dashboard_stats';
    }

    /**
     * Generates the cache key for bot analytics data.
     *
     * @return string
     */
    public static function forBotAnalytics(): string
    {
        return 'bot_analytics_stats';
    }

    /**
     * Generates a cache key for a specific user's profile.
     *
     * @param int $userId
     * @return string
     */
    public static function forUserProfile(int $userId): string
    {
        return 'user_profile_' . $userId;
    }

    /**
     * Generates a cache key for the list of all users in the admin area.
     *
     * @return string
     */
    public static function forAllUsersAdmin(): string
    {
        return 'admin_all_users';
    }
}
