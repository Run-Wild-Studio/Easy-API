<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class RegisterEasyApiElementsEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    public array $elements = [];
}
