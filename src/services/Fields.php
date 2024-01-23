<?php

namespace runwildstudio\easyapi\services;

use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\base\ComponentInterface;
use craft\errors\MissingComponentException;
use runwildstudio\easyapi\base\FieldInterface;
use runwildstudio\easyapi\events\FieldEvent;
use runwildstudio\easyapi\events\RegisterEasyApiFieldsEvent;
use runwildstudio\easyapi\fields\Assets;
use runwildstudio\easyapi\fields\CalendarEvents;
use runwildstudio\easyapi\fields\Categories;
use runwildstudio\easyapi\fields\Checkboxes;
use runwildstudio\easyapi\fields\CommerceProducts;
use runwildstudio\easyapi\fields\CommerceVariants;
use runwildstudio\easyapi\fields\Date;
use runwildstudio\easyapi\fields\DefaultField;
use runwildstudio\easyapi\fields\DigitalProducts;
use runwildstudio\easyapi\fields\Dropdown;
use runwildstudio\easyapi\fields\Entries;
use runwildstudio\easyapi\fields\EntriesSubset;
use runwildstudio\easyapi\fields\GoogleMaps;
use runwildstudio\easyapi\fields\Lightswitch;
use runwildstudio\easyapi\fields\Linkit;
use runwildstudio\easyapi\fields\Matrix;
use runwildstudio\easyapi\fields\MissingField;
use runwildstudio\easyapi\fields\Money;
use runwildstudio\easyapi\fields\MultiSelect;
use runwildstudio\easyapi\fields\Number;
use runwildstudio\easyapi\fields\RadioButtons;
use runwildstudio\easyapi\fields\SimpleMap;
use runwildstudio\easyapi\fields\SmartMap;
use runwildstudio\easyapi\fields\SuperTable;
use runwildstudio\easyapi\fields\Table;
use runwildstudio\easyapi\fields\Tags;
use runwildstudio\easyapi\fields\TypedLink;
use runwildstudio\easyapi\fields\Users;
use craft\helpers\Component as ComponentHelper;
use yii\base\InvalidConfigException;

/**
 *
 * @property-read array $registeredFields
 */
class Fields extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_EASY_API_FIELDS = 'registerEasyApiFields';
    public const EVENT_BEFORE_PARSE_FIELD = 'onBeforeParseField';
    public const EVENT_AFTER_PARSE_FIELD = 'onAfterParseField';


    // Properties
    // =========================================================================

    /**
     * @var array
     */
    private array $_fields = [];

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        foreach ($this->getRegisteredFields() as $fieldClass) {
            $field = $this->createField($fieldClass);

            // Does this field exist in Craft right now?
            if (!class_exists($field::$class)) {
                continue;
            }

            $handle = $field::$class;

            $this->_fields[$handle] = $field;
        }
    }

    /**
     * @param $handle
     * @return ComponentInterface|MissingDataType|mixed
     * @throws InvalidConfigException
     */
    public function getRegisteredField($handle): mixed
    {
        return $this->_fields[$handle] ?? $this->createField(DefaultField::class);
    }

    /**
     * @return array
     */
    public function fieldsList(): array
    {
        $list = [];

        foreach ($this->_fields as $handle => $field) {
            $list[$handle] = $field::$name;
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getRegisteredFields(): array
    {
        if (count($this->_fields)) {
            return $this->_fields;
        }

        $event = new RegisterEasyApiFieldsEvent([
            'fields' => [
                Assets::class,
                Categories::class,
                Checkboxes::class,
                CommerceProducts::class,
                CommerceVariants::class,
                Date::class,
                Dropdown::class,
                Entries::class,
                Lightswitch::class,
                Matrix::class,
                MultiSelect::class,
                Number::class,
                Money::class,
                RadioButtons::class,
                Table::class,
                Tags::class,
                Users::class,

                // Third-Party
                CalendarEvents::class,
                DigitalProducts::class,
                EntriesSubset::class,
                GoogleMaps::class,
                Linkit::class,
                SimpleMap::class,
                SmartMap::class,
                SuperTable::class,
                TypedLink::class,
            ],
        ]);

        $this->trigger(self::EVENT_REGISTER_EASY_API_FIELDS, $event);

        return $event->fields;
    }

    /**
     * @param $config
     * @return FieldInterface
     * @throws InvalidConfigException
     */
    public function createField($config): FieldInterface
    {
        if (is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            $field = ComponentHelper::createComponent($config, FieldInterface::class);
        } catch (MissingComponentException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);

            $field = new MissingField($config);
        }

        /** @var FieldInterface $field */
        return $field;
    }

    /**
     * @param $api
     * @param $element
     * @param $apiData
     * @param $fieldHandle
     * @param $fieldInfo
     * @return mixed
     */
    public function parseField($api, $element, $apiData, $fieldHandle, $fieldInfo): mixed
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_PARSE_FIELD)) {
            $this->trigger(self::EVENT_BEFORE_PARSE_FIELD, new FieldEvent([
                'apiData' => $apiData,
                'fieldHandle' => $fieldHandle,
                'fieldInfo' => $fieldInfo,
                'element' => $element,
                'api' => $api,
            ]));
        }

        $fieldClassHandle = Hash::get($fieldInfo, 'field');

        // if category groups or tag groups have been entrified, the fields for them could have been entrified too;
        // get the field by handle, check if the type hasn't changed since the api was last saved;
        // if it hasn't changed - proceed as before
        // if it has changed - assume that we've entrified and adjust the $fieldClassHandle
        $field = Craft::$app->getFields()->getFieldByHandle($fieldHandle);
        if (!$field instanceof $fieldClassHandle) {
            $fieldClassHandle = \craft\fields\Entries::class;
        }

        // Find the class to deal with the attribute
        $class = $this->getRegisteredField($fieldClassHandle);
        $class->apiData = $apiData;
        $class->fieldHandle = $fieldHandle;
        $class->fieldInfo = $fieldInfo;
        $class->field = $field;
        $class->element = $element;
        $class->api = $api;

        // Get that sweet data
        $parsedValue = $class->parseField();

        // We don't really want to set an empty array on fields, which is dangerous for existing date (elements)
        // But empty strings and booleans are totally fine, and desirable.
        // if (is_array($parsedValue) && empty($parsedValue)) {
        //     $parsedValue = null;
        // }

        $event = new FieldEvent([
            'apiData' => $apiData,
            'fieldHandle' => $fieldHandle,
            'fieldInfo' => $fieldInfo,
            'element' => $element,
            'api' => $api,
            'parsedValue' => $parsedValue,
        ]);
        $this->trigger(self::EVENT_AFTER_PARSE_FIELD, $event);
        return $event->parsedValue;
    }
}
