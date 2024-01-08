<?php

namespace runwildstudio\easyapi\fields;

use runwildstudio\easyapi\base\Field;
use runwildstudio\easyapi\base\FieldInterface;
use runwildstudio\easyapi\helpers\BaseHelper;
use craft\fields\Lightswitch as LightswitchField;

/**
 *
 * @property-read string $mappingTemplate
 */
class Lightswitch extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Lightswitch';

    /**
     * @var string
     */
    public static string $class = LightswitchField::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/fields/lightswitch';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function parseField(): mixed
    {
        $value = $this->fetchValue();

        if ($value === null) {
            return null;
        }

        return $this->parseValue($value);
    }

    public function parseValue($value)
    {
        return BaseHelper::parseBoolean($value);
    }
}
