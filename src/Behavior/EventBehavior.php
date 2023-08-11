<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event\Behavior;

use Kellenon\Yii2Event\EventHandler;
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
     * @param $owner
     * @return void
     */
    public function attach($owner): void
    {
        parent::attach($owner);

        foreach ($this->events as $eventName => $listeners) {
            $eventHandler = new EventHandler(is_array($listeners) ? $listeners : [$listeners]);
            $eventHandler->getConfig()->setContinueProcessingOnError($this->continueProcessingOnError);

            $owner->on($eventName, $eventHandler);
        }
    }
}
