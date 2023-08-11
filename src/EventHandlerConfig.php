<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event;

final class EventHandlerConfig
{
    private bool $continueProcessingOnError = false;

    /**
     * @return bool
     */
    public function canContinueProcessingOnError(): bool
    {
        return $this->continueProcessingOnError;
    }

    /**
     * @param bool $continueProcessingOnError
     * @return $this
     */
    public function setContinueProcessingOnError(bool $continueProcessingOnError): self
    {
        $this->continueProcessingOnError = $continueProcessingOnError;

        return $this;
    }
}
