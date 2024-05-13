<?php

namespace runwildstudio\easyapi\datatypes;

use Cake\Utility\Hash;
use Craft;
use runwildstudio\easyapi\base\DataType;
use runwildstudio\easyapi\base\DataTypeInterface;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Json as JsonHelper;
use Seld\JsonLint\JsonParser;
use yii\base\InvalidArgumentException;

class Json extends DataType implements DataTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'JSON';


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