<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Carbon\Carbon;
use Craft;
use craft\base\ElementInterface;
use craft\elements\Entry as EntryElement;
use craft\elements\User as UserElement;
use craft\errors\ElementNotFoundException;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\helpers\DataHelper;
use runwildstudio\easyapi\models\ElementGroup;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\ElementHelper;
use craft\helpers\Json;
use craft\models\Section;
use DateTime;
use Throwable;
use yii\base\Exception;

/**
 *
 * @property-read string $mappingTemplate
 * @property-read array $groups
 * @property-write mixed $model
 * @property-read string $groupsTemplate
 * @property-read string $columnTemplate
 */
class Entry extends Element
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Entry';

    /**
     * @var string
     */
    public static string $class = EntryElement::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroupsTemplate(): string
    {
        return 'easyapi/_includes/elements/entries/groups';
    }

    /**
     * @inheritDoc
     */
    public function getColumnTemplate(): string
    {
        return 'easyapi/_includes/elements/entries/column';
    }

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/elements/entries/map';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        $editable = Craft::$app->getSections()->getEditableSections();
        $groups = [];

        foreach ($editable as $section) {
            $groups[] = new ElementGroup([
                'id' => $section->id,
                'model' => $section,
            ]);
        }

        return $groups;
    }

    /**
     * @inheritDoc
     */
    public function getQuery($settings, array $params = []): mixed
    {
        $targetSiteId = Hash::get($settings, 'siteId') ?: Craft::$app->getSites()->getPrimarySite()->id;
        if ($this->element !== null) {
            $section = $this->element->getSection();
        }

        $query = EntryElement::find()
            ->status(null)
            ->sectionId($settings['elementGroup'][EntryElement::class]['section'])
            ->typeId($settings['elementGroup'][EntryElement::class]['entryType']);

        if (isset($section) && $section->propagationMethod === Section::PROPAGATION_METHOD_CUSTOM) {
            $query->site('*')
                ->preferSites([$targetSiteId])
                ->unique();
        } else {
            $query->siteId($targetSiteId);
        }

        Craft::configure($query, $params);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function setModel($settings): ElementInterface
    {
        $this->element = new EntryElement();
        $this->element->sectionId = $settings['elementGroup'][EntryElement::class]['section'];
        $this->element->typeId = $settings['elementGroup'][EntryElement::class]['entryType'];

        $section = Craft::$app->getSections()->getSectionById($this->element->sectionId);
        $siteId = Hash::get($settings, 'siteId');

        if ($siteId) {
            $this->element->siteId = $siteId;
        }

        // Set the default site status based on the section's settings
        $enabledForSite = [];
        foreach ($section->getSiteSettings() as $siteSettings) {
            if (
                $section->propagationMethod !== Section::PROPAGATION_METHOD_CUSTOM ||
                $siteSettings->siteId == $siteId
            ) {
                $enabledForSite[$siteSettings->siteId] = $siteSettings->enabledByDefault;
            }
        }
        $this->element->setEnabledForSite($enabledForSite);

        return $this->element;
    }

    /**
     * Checks if $existingElement should be propagated to the target site.
     *
     * @param $existingElement
     * @param array $api
     * @return ElementInterface|null
     * @throws Exception
     * @throws \craft\errors\SiteNotFoundException
     * @throws \craft\errors\UnsupportedSiteException
     * @since 5.1.3
     */
    public function checkPropagation($existingElement, array $api)
    {
        $targetSiteId = Hash::get($api, 'siteId') ?: Craft::$app->getSites()->getPrimarySite()->id;

        // Did the entry come back in a different site?
        if ($existingElement->siteId != $targetSiteId) {
            // Skip it if its section doesn't use the `custom` propagation method
            if ($existingElement->getSection()->propagationMethod !== Section::PROPAGATION_METHOD_CUSTOM) {
                return $existingElement;
            }

            // Give the entry a status for the import's target site
            // (This is how the `custom` propagation method knows which sites the entry should support.)
            $siteStatuses = ElementHelper::siteStatusesForElement($existingElement);
            $siteStatuses[$targetSiteId] = $existingElement->getEnabledForSite();
            $existingElement->setEnabledForSite($siteStatuses);

            // Propagate the entry, and swap $entry with the propagated copy
            return Craft::$app->getElements()->propagateElement($existingElement, $targetSiteId);
        }

        return $existingElement;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return array|Carbon|DateTime|false|string|null
     * @throws \Exception
     */
    protected function parsePostDate($apiData, $fieldInfo): DateTime|bool|array|Carbon|string|null
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $formatting = Hash::get($fieldInfo, 'options.match');

        return $this->parseDateAttribute($value, $formatting);
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return array|Carbon|DateTime|false|string|null
     * @throws \Exception
     */
    protected function parseExpiryDate($apiData, $fieldInfo): DateTime|bool|array|Carbon|string|null
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $formatting = Hash::get($fieldInfo, 'options.match');

        return $this->parseDateAttribute($value, $formatting);
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return int|null
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    protected function parseParent($apiData, $fieldInfo): ?int
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $default = DataHelper::fetchDefaultArrayValue($fieldInfo);

        $match = Hash::get($fieldInfo, 'options.match');
        $create = Hash::get($fieldInfo, 'options.create');
        $node = Hash::get($fieldInfo, 'node');

        // Element lookups must have a value to match against
        if ($value === null || $value === '') {
            return null;
        }

        if ($node === 'usedefault' || $value === $default) {
            $match = 'elements.id';
        }

        if (is_array($value)) {
            $value = $value[0];
        }

        $query = EntryElement::find()
            ->status(null)
            ->andWhere(['=', $match, $value]);

        if (isset($this->api['siteId']) && $this->api['siteId']) {
            $query->siteId($this->api['siteId']);
        }

        // fix for https://github.com/runwildstudio/easyapi/issues/1154#issuecomment-1429622276
        if (!empty($this->element->sectionId)) {
            $query->sectionId($this->element->sectionId);
        }

        $element = $query->one();

        if ($element) {
            $this->element->setParentId($element->id);

            return $element->id;
        }

        // Check if we should create the element. But only if title is provided (for the moment)
        if ($create && $match === 'title') {
            $element = new EntryElement();
            $element->title = $value;
            $element->sectionId = $this->element->sectionId;
            $element->typeId = $this->element->typeId;

            if (!Craft::$app->getElements()->saveElement($element, true, true, Hash::get($this->api, 'updateSearchIndexes'))) {
                EasyApi::error('Entry error: Could not create parent - `{e}`.', ['e' => Json::encode($element->getErrors())]);
            } else {
                EasyApi::info('Entry `#{id}` added.', ['id' => $element->id]);
                $this->element->newParentId = $element->id;
            }

            return $element->id;
        }

        // use the default value if it's provided and none of the above worked
        // https://github.com/runwildstudio/easyapi/issues/1154
        if (!empty($default)) {
            $this->element->parentId = $default[0];

            return $default[0];
        }

        return null;
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return int|null
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    protected function parseAuthorId($apiData, $fieldInfo): ?int
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $default = DataHelper::fetchDefaultArrayValue($fieldInfo);

        $match = Hash::get($fieldInfo, 'options.match');
        $create = Hash::get($fieldInfo, 'options.create');
        $node = Hash::get($fieldInfo, 'node');

        // Element lookups must have a value to match against
        if ($value === null || $value === '') {
            return null;
        }

        if ($node === 'usedefault' || $value === $default) {
            $match = 'elements.id';
        }

        if (is_array($value)) {
            $value = $value[0];
        }

        if ($match === 'fullName') {
            $element = UserElement::findOne(['search' => $value, 'status' => null]);
        } else {
            $element = UserElement::find()
                ->status(null)
                ->andWhere(['=', $match, $value])
                ->one();
        }

        if ($element) {
            return $element->id;
        }

        // Check if we should create the element. But only if email is provided (for the moment)
        if ($create && $match === 'email') {
            $element = new UserElement();
            $element->username = $value;
            $element->email = $value;

            if (!Craft::$app->getElements()->saveElement($element, true, true, Hash::get($this->api, 'updateSearchIndexes'))) {
                EasyApi::error('Entry error: Could not create author - `{e}`.', ['e' => Json::encode($element->getErrors())]);
            } else {
                EasyApi::info('Author `#{id}` added.', ['id' => $element->id]);
            }

            return $element->id;
        }

        return null;
    }
}
