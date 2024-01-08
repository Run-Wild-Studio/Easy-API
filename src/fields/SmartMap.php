<?php

namespace runwildstudio\easyapi\fields;

use Cake\Utility\Hash;
use runwildstudio\easyapi\base\Field;
use runwildstudio\easyapi\base\FieldInterface;
use runwildstudio\easyapi\helpers\DataHelper;

/**
 *
 * @property-read string $mappingTemplate
 */
class SmartMap extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'SmartMap';

    /**
     * @var string
     */
    public static string $class = 'doublesecretagency\smartmap\fields\Address';

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/fields/smart-map';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function parseField(): mixed
    {
        $preppedData = [];

        $fields = Hash::get($this->fieldInfo, 'fields');

        if (!$fields) {
            return null;
        }

        foreach ($fields as $subFieldHandle => $subFieldInfo) {
            $value = DataHelper::fetchValue($this->apiData, $subFieldInfo, $this->api);
            if ($value !== null) {
                $preppedData[$subFieldHandle] = $value;
            }
        }

        // Protect against sending an empty array
        if (!$preppedData) {
            return null;
        }

        return $preppedData;
    }
}
