<?php

namespace Akaunting\MutableObserver\Tests;

use Akaunting\MutableObserver\Proxy;
use BadMethodCallException;
use Orchestra\Testbench\TestCase;

class ProxyTest extends TestCase
{
    public function testItSwallowsCloakedEvents(): void
    {
        $target = new ProxyTarget;

        $actual = (new Proxy($target, ['cloaked']))->cloaked();

        $this->assertNull($actual);
    }

    public function testItPassesUncloakedEventsToTheObserver(): void
    {
        $target = new ProxyTarget;

        $actual = (new Proxy($target, ['cloaked']))->uncloaked();

        $this->assertSame('uncloaked', $actual);
    }

    public function testItSwallowsAllEvents(): void
    {
        $target = new ProxyTarget;
        $proxy = new Proxy($target, ['*']);

        $this->assertNull($proxy->cloaked());
        $this->assertNull($proxy->uncloaked());
    }

    public function testItRaisesAnExceptionForUnknownMethods(): void
    {
        $target = new ProxyTarget;

        $this->expectException(BadMethodCallException::class);

        (new Proxy($target, ['cloaked']))->unknown();
    }

    public function testItPassesArgumentsToTargetMethod(): void
    {
        $target = new ProxyTarget;
        $proxy = new Proxy($target, ['cloaked']);

        $result = $proxy->withArguments('test', 123);

        $this->assertSame('test-123', $result);
    }

    public function testItSwallowsMethodsWithArguments(): void
    {
        $target = new ProxyTarget;
        $proxy = new Proxy($target, ['withArguments']);

        $result = $proxy->withArguments('test', 123);

        $this->assertNull($result);
    }

    public function testItWorksWithEmptyEventArray(): void
    {
        $target = new ProxyTarget;
        $proxy = new Proxy($target, []);

        $this->assertSame('cloaked', $proxy->cloaked());
        $this->assertSame('uncloaked', $proxy->uncloaked());
    }
}

class ProxyTarget
{
    public function uncloaked(): string
    {
        return 'uncloaked';
    }

    public function cloaked(): string
    {
        return 'cloaked';
    }

    public function withArguments(string $arg1, int $arg2): string
    {
        return $arg1 . '-' . $arg2;
    }
}
