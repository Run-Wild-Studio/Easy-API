<?php

namespace runwildstudio\easyapi\events;

use craft\services\Elements;
use runwildstudio\easyapi\models\ApiModel;
use yii\base\Event;


// Event::on(
//     Elements::class,
//     Elements::EVENT_BEFORE_SAVE_ELEMENT,
//     function (ElementEvent $event) {
//         $element = $event->sender;


//     }
// );

class ElementEvent extends Event
{
    // Properties
    // =========================================================================

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
}
