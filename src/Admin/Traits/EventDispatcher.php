<?php

namespace Arbory\Base\Admin\Traits;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

/**
 * Class EventDispatcher.
 */
trait EventDispatcher
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @param  Dispatcher  $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }


    /**
     * @return Dispatcher|null
     */
    public function getDispatcher(): ?Dispatcher
    {
        return $this->dispatcher;
    }

    /**
     * @param $event
     * @param  array  ...$parameters
     *
     * @return array|null
     */
    protected function trigger($event, ...$parameters)
    {
        if(! $this->getDispatcher()) {
            return null;
        }

        return $this->dispatcher->dispatch($event, $parameters);
    }

    /**
     * @param $event
     * @param Closure $callback
     */
    public function on($event, Closure $callback): void
    {
        $this->addEventListeners(Arr::wrap($event), $callback);
    }

    /**
     * @param array $events
     * @param Closure $callback
     */
    public function addEventListeners(array $events, Closure $callback): void
    {
        if(! $this->getDispatcher()) {
            return;
        }

        $this->dispatcher->listen($events, $callback);
    }

    /**
     * @param $event
     * @param Closure $callback
     */
    public function addEventListener($event, Closure $callback): void
    {
        $this->addEventListeners(Arr::wrap($event), $callback);
    }
}
