<?php

namespace runwildstudio\easyapi\authtypes;

use Craft;
use runwildstudio\easyapi\base\AuthType;
use runwildstudio\easyapi\EasyApi;
use Exception;

class basic extends AuthType
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
        if ($api->authorization != '') {
            return ['success' => true, 'value' => 'Authorization: ' . $api->authorization];
        }

        return ['success' => false, 'error' => 'Authorization value not specified'];
    }

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getFieldsTemplate(): string
    {
        return 'easyapi/_includes/authtypes/basic/fields';
    }
}
