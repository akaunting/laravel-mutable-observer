<?php

namespace Akaunting\MutableObserver\Tests;

use Akaunting\MutableObserver\Proxy;
use Akaunting\MutableObserver\ProxyManager;
use Orchestra\Testbench\TestCase;

class ProxyManagerTest extends TestCase
{
    public function testItRegistersProxyManager(): void
    {
        $app = $this->resolveApplication();

        $manager = new ProxyManager($app);
        $manager->register(new ProxyManagerTarget, ['deleted', 'saved']);

        $this->assertInstanceOf(Proxy::class, $app->make(ProxyManagerTarget::class));
    }

    public function testItUnregistersProxyManager(): void
    {
        $app = $this->resolveApplication();

        $manager = new ProxyManager($app);
        $manager->unregister(new ProxyManagerTarget);

        $this->assertInstanceOf(ProxyManagerTarget::class, $app->make(ProxyManagerTarget::class));
    }

    public function testItRegistersAndUnregistersMultipleTimes(): void
    {
        $app = $this->resolveApplication();
        $manager = new ProxyManager($app);
        $target = new ProxyManagerTarget;

        $manager->register($target, ['created']);
        $this->assertInstanceOf(Proxy::class, $app->make(ProxyManagerTarget::class));

        $manager->unregister($target);
        $this->assertInstanceOf(ProxyManagerTarget::class, $app->make(ProxyManagerTarget::class));

        $manager->register($target, ['updated']);
        $this->assertInstanceOf(Proxy::class, $app->make(ProxyManagerTarget::class));
    }

    public function testItRegistersProxyWithMultipleEvents(): void
    {
        $app = $this->resolveApplication();
        $manager = new ProxyManager($app);

        $manager->register(new ProxyManagerTarget, ['created', 'updated', 'deleted']);

        $this->assertInstanceOf(Proxy::class, $app->make(ProxyManagerTarget::class));
    }

    public function testItRegistersProxyWithWildcard(): void
    {
        $app = $this->resolveApplication();
        $manager = new ProxyManager($app);

        $manager->register(new ProxyManagerTarget, ['*']);

        $this->assertInstanceOf(Proxy::class, $app->make(ProxyManagerTarget::class));
    }
}

class ProxyManagerTarget
{
}
