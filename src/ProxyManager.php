<?php

declare(strict_types=1);

namespace Akaunting\MutableObserver;

use Illuminate\Contracts\Container\Container;

class ProxyManager
{
    /**
     * Create a new proxy manager instance.
     *
     * @param  Container  $app  The application container
     */
    public function __construct(
        private readonly Container $app
    ) {}

    /**
     * Register a proxy for the given target observer.
     *
     * @param  object  $target  The target observer
     * @param  array<int, string>  $events  The events to mute
     * @return void
     */

    public function register(object $target, array $events): void
    {
        $proxy = $this->app->make(Proxy::class, ['target' => $target, 'events' => $events]);

        $this->app->instance(get_class($target), $proxy);
    }

    /**
     * Unregister a proxy for the given target observer.
     *
     * @param  object  $target  The target observer
     * @return void
     */
    public function unregister(object $target): void
    {
        $this->app->instance(get_class($target), $target);
    }
}
