<?php

namespace runwildstudio\easyapi\authtypes;

use Craft;
use runwildstudio\easyapi\base\AuthType;
use runwildstudio\easyapi\base\AuthTypeInterface;
use runwildstudio\easyapi\EasyApi;
use Exception;

class none extends AuthType implements AuthTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'None';


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getAuthValue($api): array
    {
        // Make sure auth has been populated!
        if (!($api->authorization === undefined || $api->authorization === '')) {
            return ['success' => false, 'error' => 'Authorization value has been specified incorrectly.'];
        }

        return ['success' => true, 'value' => $api->authorization];
    }
}
