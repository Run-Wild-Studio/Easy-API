<?php

namespace runwildstudio\easyapi\datatypes;

use Cake\Utility\Hash;
use Cake\Utility\Xml as XmlParser;
use Craft;
use runwildstudio\easyapi\base\DataType;
use runwildstudio\easyapi\base\DataTypeInterface;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Json;
use Exception;

class Xml extends DataType implements DataTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'XML';


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getApi($url, $settings, bool $usePrimaryElement = true): array
    {
        // Function to make pages work but no functionality needed as Feed Me does the heavy lifting
    }
}
