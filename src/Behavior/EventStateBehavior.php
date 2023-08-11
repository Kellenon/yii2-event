<?php

declare(strict_types=1);

namespace Kellenon\Yii2Event\Behavior;

use yii\base\Behavior;
use yii\db\AfterSaveEvent;
use yii\db\BaseActiveRecord;

/**
 * @property BaseActiveRecord $owner
 */
final class EventStateBehavior extends Behavior
{
    /**
     * @var string
     */
    public string $attribute;

    /**
     * @var array
     */
    public array $transitionEventStates = [];

    /**
     * @var mixed
     */
    private $oldAttributeValue;

    public function events(): array
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'handleBeforeUpdate',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'handleAfterUpdate',
        ];
    }

    /**
     * @return void
     */
    public function handleBeforeUpdate(): void
    {
        $this->oldAttributeValue = $this->owner->getOldAttribute($this->attribute);
    }

    /**
     * @param AfterSaveEvent $event
     * @return void
     */
    public function handleAfterUpdate(AfterSaveEvent $event): void
    {
        $this->trigger($event);
    }

    /**
     * @param AfterSaveEvent $event
     * @return void
     */
    private function trigger(AfterSaveEvent $event): void
    {
        $currentSetValue = $this->owner->getAttribute($this->attribute);

        foreach ($this->transitionEventStates as $eventName => $attributeStates) {
            if (empty($attributeStates)) {
                if ($currentSetValue !== $this->oldAttributeValue) {
                    $this->owner->trigger($eventName, $event);

                    return;
                }
            }

            foreach ($attributeStates as $oldAttributeState => $newAttributeState) {
                if ($oldAttributeState !== $this->oldAttributeValue) {
                    continue;
                }

                if (is_array($newAttributeState) && in_array($currentSetValue, $newAttributeState, true)) {
                    $this->owner->trigger($eventName, $event);
                } elseif ($currentSetValue === $newAttributeState) {
                    $this->owner->trigger($eventName, $event);
                }
            }
        }
    }
}
