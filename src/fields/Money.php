<?php

namespace runwildstudio\easyapi\fields;

use Cake\Utility\Hash;
use Craft;
use runwildstudio\easyapi\base\Field;
use runwildstudio\easyapi\base\FieldInterface;
use craft\fields\Money as MoneyField;
use craft\helpers\Localization;

/**
 *
 * @property-read string $mappingTemplate
 */
class Money extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Money';

    /**
     * @var string
     */
    public static string $class = MoneyField::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/fields/money';
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

        $localized = Hash::get($this->fieldInfo, 'options.localized');

        if ($localized) {
            // value provided in the api should be localised (like with the Number field)

            // for example if the site you're importing to is Dutch (nl),
            // you checked the "Data provided for this localized for the site the api is for" checkbox on the api mapping screen
            // and your money field is in EUR,
            // the amount of: one thousand two hundred thirty-four euro and fifty-six cents
            // should be: 1.234,56 in your api;
            $site = Craft::$app->getSites()->getSiteById($this->api['siteId']);
            $siteLocaleId = $site->getLocale()->id;
        } else {
            // the values in the api are in a float-like notation

            // for example if the site you're importing to is Dutch (nl),
            // you DIDN'T check the "Data provided for this localized for the site the api is for" checkbox on the api mapping screen
            // and your money field is in EUR,
            // one thousand two hundred thirty-four euro and fifty-six cents
            // should be: 1234.56 in your api;
            $siteLocaleId = 'en';
        }

        return [
            'value' => Localization::normalizeNumber($value, $siteLocaleId),
            'locale' => 'en',
        ];
    }
}
