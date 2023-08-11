<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event;

use Kellenon\Yii2Event\Exception\EventHandlerException;
use ReflectionClass;
use Throwable;
use yii\base\Event;
use yii\base\ModelEvent;

final class EventHandler
{
    /**
     * @var EventHandlerConfig
     */
    private EventHandlerConfig $config;

    /**
     * @var string[]
     */
    private array $listeners;

    /**
     * @param string[] $listeners
     */
    public function __construct(array $listeners = [])
    {
        $this->config = new EventHandlerConfig();
        $this->listeners = $listeners;
    }

    /**
     * @param Event $event
     * @return void
     * @throws EventHandlerException
     * @throws Throwable
     */
    public function __invoke(Event $event): void
    {
        foreach ($this->listeners as $listener) {
            if (!is_string($listener)) {
                throw new EventHandlerException("Event $event->name. Failed to define listener");
            }

            if (!class_exists($listener)) {
                throw new EventHandlerException("Event $event->name. Listener class [$listener] not found");
            }

            if (!(new ReflectionClass($listener))->implementsInterface(EventListenerInterface::class)) {
                throw new EventHandlerException("Event $event->name. Listener class [$listener] not instance of AbstractEventListener class");
            }

            /** @var EventListenerInterface $listener */
            $listener = new $listener();

            if (!$listener->canHandle($event)) {
                continue;
            }

            if (!$listener->handle($event)) {
                if ($event instanceof ModelEvent) {
                    $event->isValid = false;
                }

                if (!$this->config->canContinueProcessingOnError()) {
                    $event->handled = true;
                    break;
                }
            }
        }
    }

    /**
     * @return EventHandlerConfig
     */
    public function getConfig(): EventHandlerConfig
    {
        return $this->config;
    }
}
