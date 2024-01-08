<?php

namespace runwildstudio\easyapi\fields;

use runwildstudio\easyapi\base\Field;
use runwildstudio\easyapi\base\FieldInterface;
use craft\fields\Number as NumberField;
use craft\helpers\Localization;

/**
 *
 * @property-read string $mappingTemplate
 */
class Number extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Number';

    /**
     * @var string
     */
    public static string $class = NumberField::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/fields/default';
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

    /**
     * @param $value
     * @return mixed
     */
    public function parseValue($value): mixed
    {
        return Localization::normalizeNumber($value);
    }
}
