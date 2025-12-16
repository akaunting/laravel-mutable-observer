<?php

namespace Akaunting\MutableObserver\Tests;

use Akaunting\MutableObserver\Provider;
use Akaunting\MutableObserver\Proxy;
use Akaunting\MutableObserver\ProxyManager;
use Orchestra\Testbench\TestCase;

class ProviderTest extends TestCase
{
    public function testProviderRegistersProxyManager(): void
    {
        $provider = new Provider($this->app);
        $provider->register();

        $this->assertTrue($this->app->bound(ProxyManager::class));
        $this->assertInstanceOf(ProxyManager::class, $this->app->make(ProxyManager::class));
    }

    public function testProviderRegistersProxyManagerAsSingleton(): void
    {
        $provider = new Provider($this->app);
        $provider->register();

        $instance1 = $this->app->make(ProxyManager::class);
        $instance2 = $this->app->make(ProxyManager::class);

        $this->assertSame($instance1, $instance2);
    }

    public function testProviderRegistersProxyBinding(): void
    {
        $provider = new Provider($this->app);
        $provider->register();

        $this->assertTrue($this->app->bound(Proxy::class));
    }

    public function testProviderCreatesProxyWithParameters(): void
    {
        $provider = new Provider($this->app);
        $provider->register();

        $target = new ProviderTestTarget();
        $events = ['created', 'updated'];

        $proxy = $this->app->make(Proxy::class, ['target' => $target, 'events' => $events]);

        $this->assertInstanceOf(Proxy::class, $proxy);
    }
}

class ProviderTestTarget
{
    public function created(): string
    {
        return 'created';
    }

    public function updated(): string
    {
        return 'updated';
    }
}
