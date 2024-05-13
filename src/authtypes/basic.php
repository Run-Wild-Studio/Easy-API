<?php

namespace runwildstudio\easyapi\authtypes;

use Craft;
use runwildstudio\easyapi\base\AuthType;
use runwildstudio\easyapi\base\AuthTypeInterface;
use runwildstudio\easyapi\EasyApi;
use Exception;

class basic extends AuthType implements AuthTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Basic';


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getAuthValue($api): array
    {
        // Make sure auth has been populated!
        if ($api->authorization === undefined || $api->authorization === '') {
            return ['success' => false, 'error' => 'Authorization value not specified'];
        }

        return ['success' => true, 'value' => $api->authorization];
    }
}
