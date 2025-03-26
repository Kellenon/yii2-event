<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event;

use yii\base\Application;
use yii\base\BootstrapInterface;

final class EventBootstrap implements BootstrapInterface
{
    /**
     * @var array
     *
     * ```php
     * 'eventName' => [
     *     Listener1::class,
     *     Listener2::class,
     *     Listener3:class,
     * ]
     * ```
     */
    public array $events = [];

    /**
     * @var bool
     * Whether to continue processing the event in case of an error.
     * Error => (new Listener())->handler() --> return false
     */
    public bool $continueProcessingOnError = false;

    /**
     * @param Application $app
     * @return void
     */
    public function bootstrap($app): void
    {
        foreach ($this->events as $eventName => $listeners) {
            $app->on(
                $eventName,
                new EventHandler((new EventHandlerConfig())->setContinueProcessingOnError($this->continueProcessingOnError), is_array($listeners) ? $listeners : [$listeners]),
            );
        }
    }
}
