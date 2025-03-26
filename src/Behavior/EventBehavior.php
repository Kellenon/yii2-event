<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event\Behavior;

use Kellenon\Yii2Event\EventHandler;
use Kellenon\Yii2Event\EventHandlerConfig;
use yii\base\Behavior;
use yii\base\Component;

/**
 * @property Component $owner
 */
final class EventBehavior extends Behavior
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
     * @return array
     */
    public function events()
    {
        $events = [];

        foreach ($this->events as $eventName => $listeners) {
            $events[$eventName] = new EventHandler(
                (new EventHandlerConfig())->setContinueProcessingOnError($this->continueProcessingOnError),
                is_array($listeners) ? $listeners : [$listeners],
            );
        }

        return $events;
    }
}
