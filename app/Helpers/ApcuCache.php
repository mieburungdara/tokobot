<?php

namespace TokoBot\Helpers;

use Psr\SimpleCache\CacheInterface;
use \DateInterval;
use \DateTime;

/**
 * Class ApcuCache
 *
 * An APCu implementation of PSR-16 Simple Cache interface.
 *
 * @package TokoBot\Helpers
 */
class ApcuCache implements CacheInterface
{
    /**
     * ApcuCache constructor.
     *
     * @throws \Exception if APCu extension is not loaded or enabled.
     */
    public function __construct()
    {
        if (!extension_loaded('apcu')) {
            throw new \Exception("APCu extension is not loaded.");
        }
        if (!apcu_enabled()) {
            throw new \Exception("APCu is not enabled. Check apc.enabled in your php.ini.");
        }
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = apcu_fetch($key, $success);
        return $success ? $value : $default;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                     the driver supports TTL then the library may set a default value
     *                                     for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $seconds = $this->convertTtl($ttl);
        return apcu_store($key, $value, $seconds);
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete(string $key): bool
    {
        return apcu_delete($key);
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return apcu_clear_cache();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable<string> $keys A list of keys that can be requested.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable<string, mixed> A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $values = apcu_fetch($keys, $success);
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $values[$key] ?? $default;
        }
        return $result;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item.
     *
     * @return bool True on success and false on failure.
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $seconds = $this->convertTtl($ttl);
        $errors = apcu_store($values, null, $seconds);
        return empty($errors);
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable<string> $keys A list of string-based keys to be deleted.
     *
     * @return bool True if all items were successfully removed. False if there was an error.
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $result = apcu_delete($keys);
        return empty($result); // apcu_delete returns an array of failed keys.
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return apcu_exists($key);
    }

    /**
     * Converts TTL from PSR-16 format to seconds for APCu.
     *
     * @param null|int|\DateInterval $ttl
     * @return int
     */
    private function convertTtl(null|int|\DateInterval $ttl): int
    {
        if ($ttl === null) {
            return 0; // 0 means forever in APCu
        }

        if ($ttl instanceof \DateInterval) {
            return (new DateTime())->add($ttl)->getTimestamp() - time();
        }

        return (int)$ttl;
    }
}
