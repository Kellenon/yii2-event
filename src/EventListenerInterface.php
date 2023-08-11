<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event;

use yii\base\Event;

interface EventListenerInterface
{
    /**
     * @param Event $event
     * @return bool
     * Can a handler handle an event
     *
     * Return Values:
     * - return true  --> Yes
     * - return false --> No
     *
     * Further:
     * Invocation of the next handlers
     */
    public function canHandle(Event $event): bool;

    /**
     * @param Event $event
     * @return bool
     * Handle the event
     *
     * Return Values:
     * - return true  --> Continue the process
     * - return false --> If a handler needs to stop the invocation of the handlers that follow it
     */
    public function handle(Event $event): bool;
}
