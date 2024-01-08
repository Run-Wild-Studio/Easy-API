<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class RegisterEasyApiFieldsEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    public array $fields = [];
}
