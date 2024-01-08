<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class RegisterEasyApiDataTypesEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    public array $dataTypes = [];
}
