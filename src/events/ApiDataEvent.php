<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class ApiDataEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var string|null
     */
    public ?string $url = null;

    /**
     * @var int|null
     */
    public ?int $apiId = null;

    /**
     * @var
     */
    public mixed $response = null;
}
