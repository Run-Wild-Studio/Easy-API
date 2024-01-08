<?php

namespace runwildstudio\easyapi\events;

use craft\events\CancelableEvent;

class ApiProcessEvent extends CancelableEvent
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
    public mixed $contentData = null;

    /**
     * @var
     */
    public mixed $element = null;
}
