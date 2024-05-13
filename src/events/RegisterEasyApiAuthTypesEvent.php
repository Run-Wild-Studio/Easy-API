<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class RegisterEasyApiAuthTypesEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    public array $authTypes = [];
}
