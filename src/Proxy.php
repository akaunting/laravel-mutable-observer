<?php

declare(strict_types=1);

namespace Akaunting\MutableObserver;

use BadMethodCallException;

class Proxy
{
    /**
     * Create a new proxy instance.
     *
     * @param  object  $target  The target observer to proxy
     * @param  array<int, string>  $events  The events to mute
     */
    public function __construct(
        private readonly object $target,
        private readonly array $events = []
    ) {}

    /**
     * Handle dynamic method calls to the target.
     *
     * @param  string  $name
     * @param  array<int, mixed>  $arguments
     * @return mixed
     * @throws BadMethodCallException
     */

    public function __call($name, $arguments)
    {
        if ($this->isMuted($name)) {
            return null;
        }

        if (method_exists($this->target, $name)) {
            return $this->target->$name(...$arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Unknown method [%s@%s]',
            get_class($this->target),
            $name
        ));
    }

    protected function isMuted($name): bool
    {
        return (in_array('*', $this->events) || in_array($name, $this->events));
    }
}
