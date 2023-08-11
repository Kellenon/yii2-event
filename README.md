<div>
    <p align="center">
        <a href="https://www.yiiframework.com/" target="_blank">
            <img src="https://www.yiiframework.com/image/design/logo/yii3_sign.svg" width="150" alt="Yii Framework" />
        </a>
    </p>
    <h1 align="center">Yii2 Event</h1>
    <h5 align="center">Convenient event system for Yii2.</h5>
    <br>
</div>

This component will help create a system of events, which will be as simple to use as a system of events in [Laravel](https://laravel.com/docs/8.x/events).

## Installation

To install, either run

```bash
composer require kellenon/yii2-event
```

or add

```
"kellenon/yii2-event": "@dev"
```

to the ```require``` section of your `composer.json` file.

## Usage

You can attach a behavior to a component and declare events and listeners in it.

The listener class must implement the interface `EventListenerInterface`

### Behaviors

**EventBehavior**

```php
<?php

use Kellenon\Yii2Event\Behavior\EventBehavior;
use yii\base\Model;

class Order extends ActiveRecord
{
    public const EVENT_ORDER_CREATED = 'order-created';
    
    public function behaviors(): array
    {
        return [
            [
                'class' => EventBehavior::class,
                'events' => [
                    static::EVENT_ORDER_CREATED => [
                        Listener1::class,
                        Listener2::class,
                        Listener3::class,
                    ],
                ],
            ],
        ];
    }
}
```

```php
// client code
$order = new Order();
$order->load(Yii::$app->request->post());

if ($order->save()) {
    $order->trigger(Order::EVENT_ORDER_CREATED);
}
```

**EventStateBehavior**

```php
<?php

use Kellenon\Yii2Event\Behavior\EventStateBehavior;

class Order extends ActiveRecord
{
    public const EVENT_AFTER_COMPLETE = 'after-complete';
    public const STATUS_NEW = 'new';
    public const STATUS_CONFIRMATION = 'confirmation';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAYMENT_COMPLETED = 'payment-completed';
    public const STATUS_COMPLETE = 'complete';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * @return array[]
     * If the transition condition is met, the event will be triggered automatically.
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => EventBootstrap::class,
                'events' => [
                    static::EVENT_AFTER_COMPLETE => [
                        Listener1::class,
                        Listener2::class,
                        Listener3::class,
                    ],
                ],
            ],
            [
                'class' => EventStateBehavior::class,
                'attribute' => 'status',
                'transitionEventStates' => [
                    static::EVENT_AFTER_COMPLETE => [
                        static::STATUS_NEW => [static::STATUS_COMPLETE, static::STATUS_CANCELLED],
                        static::STATUS_CONFIRMATION => [static::STATUS_COMPLETE, static::STATUS_CANCELLED],
                        static::STATUS_PROCESSING => [static::STATUS_COMPLETE, static::STATUS_CANCELLED],
                        static::STATUS_PAYMENT_COMPLETED => [static::STATUS_COMPLETE, static::STATUS_CANCELLED],
                    ],
                ],
            ],
        ];
    }
}
```

```php
// client code
$order = new Order();
$order->status = Order::STATUS_COMPLETE;
// save() -> Event 'after-complete' will be triggered automatically
$order->save();
```

### Bootstrap

```php
<?php

use Kellenon\Yii2Event\EventBootstrap;

return [
    'bootstrap' => [
        [
            'class' => EventBootstrap::class,
            'events' => [
                'eventName' => [
                    Listener1::class,
                    Listener2::class,
                    Listener3::class,
                ],
            ],
        ],
    ],
];
```

```php
// client code
Yii::$app->trigger('eventName');
```
