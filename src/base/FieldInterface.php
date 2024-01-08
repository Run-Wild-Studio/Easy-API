<?php

namespace runwildstudio\easyapi\base;

use craft\base\ComponentInterface;

interface FieldInterface extends ComponentInterface
{
    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function getMappingTemplate(): string;

    /**
     * @return mixed
     */
    public function parseField(): mixed;
}
