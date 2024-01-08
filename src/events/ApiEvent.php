<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class ApiEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var
     */
    public mixed $api = null;

    /**
     * @var bool
     */
    public bool $isNew = false;
}
