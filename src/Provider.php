<?php

declare(strict_types=1);

namespace Akaunting\MutableObserver;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Mutable Observer Service Provider.
 *
 * Registers the proxy manager and proxy bindings for mutable observers.
 */
class Provider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ProxyManager::class, function ($app) {
            return new ProxyManager($app);
        });

        $this->app->bind(Proxy::class, function ($app, $parameters) {
            return new Proxy($parameters['target'], $parameters['events']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [
            ProxyManager::class,
            Proxy::class,
        ];
    }
}
