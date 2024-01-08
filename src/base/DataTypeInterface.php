<?php

namespace runwildstudio\easyapi\base;

use craft\base\ComponentInterface;

interface DataTypeInterface extends ComponentInterface
{
    // Public Methods
    // =========================================================================

    /**
     * @param $url
     * @param $settings
     * @param bool $usePrimaryElement
     * @return mixed
     */
    public function getApi($url, $settings, bool $usePrimaryElement = true): mixed;
}
