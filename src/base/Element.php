<?php

namespace runwildstudio\easyapi\base;

use ArrayAccess;
use Cake\Utility\Hash;
use Carbon\Carbon;
use Craft;
use craft\base\Component;
use craft\base\Element as BaseElement;
use craft\base\ElementInterface as CraftElementInterface;
use craft\elements\db\ElementQuery;
use runwildstudio\easyapi\events\ElementEvent;
use runwildstudio\easyapi\helpers\BaseHelper;
use runwildstudio\easyapi\helpers\DataHelper;
use runwildstudio\easyapi\helpers\DateHelper;
use runwildstudio\easyapi\models\ApiModel;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Db;
use craft\helpers\ElementHelper;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use DateTime;
use Exception;

/**
 *
 * @property-read mixed $name
 * @property-read mixed $elementClass
 * @property-read mixed $class
 */
abstract class Element extends Component implements ElementInterface
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_PARSE_ATTRIBUTE = 'onBeforeParseAttribute';
    public const EVENT_AFTER_PARSE_ATTRIBUTE = 'onParseAttribute';

    // Properties
    // =========================================================================


    /**
     * @var ApiModel|null
     */
    public ?ApiModel $api = null;

    /**
     * @var CraftElementInterface
     */
    public $element;


    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function getName(): string
    {
        /** @phpstan-ignore-next-line */
        return static::$name;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return get_class($this);
    }

    /**
     * @inheritDoc
     */
    public function getElementClass(): string
    {
        /** @phpstan-ignore-next-line */
        return static::$class;
    }

    /**
     * @param $apiData
     * @param $fieldHandle
     * @param $fieldInfo
     * @return array|ArrayAccess|mixed|string|null
     */
    public function parseAttribute($apiData, $fieldHandle, $fieldInfo): mixed
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_PARSE_ATTRIBUTE)) {
            $this->trigger(self::EVENT_BEFORE_PARSE_ATTRIBUTE, new ElementEvent([
                'apiData' => $apiData,
                'fieldHandle' => $fieldHandle,
                'fieldInfo' => $fieldInfo,
            ]));
        }

        // Find the class to deal with the attribute
        $name = 'parse' . ucwords($fieldHandle);

        // Set a default handler for non-specific attribute classes
        if (!method_exists($this, $name)) {
            return $this->fetchSimpleValue($apiData, $fieldInfo);
        }

        $parsedValue = $this->$name($apiData, $fieldInfo);

        // Give plugins a chance to modify parsed attributes
        $event = new ElementEvent([
            'apiData' => $apiData,
            'fieldHandle' => $fieldHandle,
            'fieldInfo' => $fieldInfo,
            'parsedValue' => $parsedValue,
        ]);

        $this->trigger(self::EVENT_AFTER_PARSE_ATTRIBUTE, $event);

        return $event->parsedValue;
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return array|ArrayAccess|mixed|string|null
     */
    public function fetchSimpleValue($apiData, $fieldInfo): mixed
    {
        return DataHelper::fetchSimpleValue($apiData, $fieldInfo);
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return array|ArrayAccess|mixed
     */
    public function fetchArrayValue($apiData, $fieldInfo): mixed
    {
        return DataHelper::fetchArrayValue($apiData, $fieldInfo);
    }


    // Interface Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function matchExistingElement($data, $settings): mixed
    {
        $criteria = [];

        foreach ($settings['fieldUnique'] as $handle => $value) {
            $apiValue = Hash::get($data, $handle);

            if (!is_null($apiValue)) {
                if (is_object($apiValue) && get_class($apiValue) === 'DateTime') {
                    $apiValue = $apiValue->format('Y-m-d H:i:s');
                }

                // We need a value to check against
                if (is_string($apiValue) && $apiValue === '') {
                    continue;
                }

                if ($handle === 'parent') {
                    $criteria['descendantOf'] = Db::escapeParam($apiValue);
                } else {
                    $criteria[$handle] = Db::escapeParam($apiValue);
                }
            }
        }

        // Make sure we have data to match on, otherwise it'll just grab the first found entry
        // without matching against anything. Not what we want at all!
        if (count($criteria) === 0) {
            throw new Exception('Unable to match an existing element. Have you set a unique identifier for ' . Json::encode(array_keys($settings['fieldUnique'])) . '? Make sure you are also mapping this in your api and it has a value.');
        }

        // Check against elements that may be disabled for site
        // $criteria['enabledForSite'] = false;

        return $this->getQuery($settings, $criteria)->one();
    }

    /**
     * @inheritDoc
     */
    public function delete($elementIds): bool
    {
        /** @var CraftElementInterface|string $class */
        $class = $this->getElementClass();
        $elementsService = Craft::$app->getElements();

        foreach ($elementIds as $elementId) {
            $elementsService->deleteElementById($elementId, $class);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function disable($elementIds): bool
    {
        /** @var CraftElementInterface|string $class */
        $class = $this->getElementClass();
        $elementsService = Craft::$app->getElements();

        foreach ($elementIds as $elementId) {
            /** @var BaseElement $element */
            $element = $elementsService->getElementById($elementId, $class);
            if ($element->enabled) {
                $element->enabled = false;
                $elementsService->saveElement($element, true, true, Hash::get($this->api, 'updateSearchIndexes'));
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function disableForSite(array $elementIds): bool
    {
        /** @var CraftElementInterface|string $class */
        $class = $this->getElementClass();

        /** @var ElementQuery $query */
        $query = $class::find()
            ->id($elementIds)
            ->siteId($this->api->siteId)
            ->status(null);

        $elementsService = Craft::$app->getElements();

        foreach ($query->each() as $element) {
            /** @var BaseElement $element */
            if ($element->enabledForSite) {
                $element->enabledForSite = false;
                $elementsService->saveElement($element, false, false, Hash::get($this->api, 'updateSearchIndexes'));
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function save($element, $settings): bool
    {
        // Setup some stuff before the element saves, and also give a chance to prevent saving
        if (!$this->beforeSave($element, $settings)) {
            return true;
        }

        if (!Craft::$app->getElements()->saveElement($this->element, true, true, Hash::get($this->api, 'updateSearchIndexes'))) {
            return false;
        }

        return true;
    }

    public function beforeSave($element, $settings): bool
    {
        $this->element = $element;
        $this->element->setScenario(BaseElement::SCENARIO_ESSENTIALS);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($data, $settings): void
    {
    }

    // Protected Methods
    // =========================================================================

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return string
     */
    protected function parseTitle($apiData, $fieldInfo): string
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);

        // Truncate if need be
        if (is_string($value) && strlen($value) > 255) {
            $value = StringHelper::safeTruncate($value, 255);
        }

        return $value;
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return string
     */
    protected function parseSlug($apiData, $fieldInfo): string
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);

        if (Craft::$app->getConfig()->getGeneral()->limitAutoSlugsToAscii) {
            $value = $this->_asciiString($value);
        }

        // normalize the slug and check if it's valid;
        // if it is - use it, otherwise _createSlug()
        if (is_string($value) && ($value = ElementHelper::normalizeSlug($value)) !== '') {
            return $value;
        }

        return $this->_createSlug($value);
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return bool
     */
    protected function parseEnabled($apiData, $fieldInfo): bool
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);

        return BaseHelper::parseBoolean($value);
    }

    /**
     * @param $value
     * @param $formatting
     * @return DateTime|null
     * @throws Exception
     */
    protected function parseDateAttribute($value, $formatting): ?DateTime
    {
        $dateValue = DateHelper::parseString($value, $formatting);
        if ($dateValue instanceof Carbon) {
            $dateValue = $dateValue->toDateTime();
        }

        if (!empty($dateValue)) {
            return $dateValue;
        }

        return null;
    }

    /**
     * @param string $str
     * @return string
     */
    private function _createSlug(string $str): string
    {
        // Remove HTML tags
        $str = StringHelper::stripHtml($str);

        // Convert to kebab case
        $glue = Craft::$app->getConfig()->getGeneral()->slugWordSeparator;
        $lower = !Craft::$app->getConfig()->getGeneral()->allowUppercaseInSlug;
        return StringHelper::toKebabCase($str, $glue, $lower);
    }

    /**
     * @param $str
     * @return string
     */
    private function _asciiString($str): string
    {
        $charMap = StringHelper::asciiCharMap(true, Craft::$app->language);

        $asciiStr = '';

        $iMax = mb_strlen($str);
        for ($i = 0; $i < $iMax; $i++) {
            $char = mb_substr($str, $i, 1);
            $asciiStr .= $charMap[$char] ?? $char;
        }

        return $asciiStr;
    }
}
