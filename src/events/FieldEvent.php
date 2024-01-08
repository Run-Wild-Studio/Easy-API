<?php

namespace runwildstudio\easyapi\events;

use yii\base\Event;

class FieldEvent extends Event
{
    // Properties
    // =========================================================================

    /**
     * @var
     */
    public mixed $api = null;

    /**
     * @var
     */
    public mixed $apiData = null;

    /**
     * @var
     */
    public mixed $fieldHandle = null;

    /**
     * @var
     */
    public mixed $fieldInfo = null;

    /**
     * @var
     */
    public mixed $parsedValue = null;

    /**
     * @var
     */
    public mixed $element = null;
}
