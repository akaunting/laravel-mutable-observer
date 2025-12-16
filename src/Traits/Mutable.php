<?php

declare(strict_types=1);

namespace Akaunting\MutableObserver\Traits;

use Akaunting\MutableObserver\ProxyManager;

/**
 * Mutable Trait.
 *
 * Adds mute/unmute functionality to observer classes.
 * Allows temporarily disabling observer events.
 */
trait Mutable
{
    /**
     * Wildcard event that mutes all events.
     *
     * @var string
     */
    public const WILDCARD_EVENT = '*';

    /**
     * Mute the specified observer events.
     *
     * @param  string|array<int, string>|null  $events  The events to mute, or null for all events
     * @return void
     */
    public static function mute(string|array|null $events = null): void
    {
        $instance = new static();

        app(ProxyManager::class)->register($instance, static::normalizeEvents($events));
    }

    /**
     * Unmute all observer events.
     *
     * @return void
     */
    public static function unmute(): void
    {
        $instance = new static();

        app(ProxyManager::class)->unregister($instance);
    }

    /**
     * Normalize events to array format.
     *
     * @param  string|array<int, string>|null  $events  The events to normalize
     * @return array<int, string>  The normalized events array
     */
    protected static function normalizeEvents(string|array|null $events): array
    {
        if (is_null($events)) {
            $events = [self::WILDCARD_EVENT];
        }

        if (! is_array($events)) {
            $events = [$events];
        }

        return $events;
    }
}
