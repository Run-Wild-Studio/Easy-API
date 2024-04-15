<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Craft;
use craft\base\ElementInterface;
use craft\elements\Category as CategoryElement;
use craft\errors\ElementNotFoundException;
// use craft\feedme\elements\Category as FeedMeCategory;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\helpers\DataHelper;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Json;
use Throwable;
use yii\base\Exception;

/**
 *
 * @property-read string $mappingTemplate
 * @property-read mixed $groups
 * @property-write mixed $model
 * @property-read string $groupsTemplate
 * @property-read string $columnTemplate
 */
class Category extends Element
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Category';

    /**
     * @var string
     */
    public static string $class = CategoryElement::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroupsTemplate(): string
    {
        return 'easyapi/_includes/elements/categories/groups';
    }

    /**
     * @inheritDoc
     */
    public function getColumnTemplate(): string
    {
        return 'easyapi/_includes/elements/categories/column';
    }

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/elements/categories/map';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        return Craft::$app->categories->getEditableGroups();
    }

    /**
     * @inheritDoc
     */
    public function getQuery($settings, array $params = []): mixed
    {
        $query = CategoryElement::find()
            ->status(null)
            ->groupId($settings['elementGroup'][CategoryElement::class])
            ->siteId(Hash::get($settings, 'siteId') ?: Craft::$app->getSites()->getPrimarySite()->id);
        Craft::configure($query, $params);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function setModel($settings): ElementInterface
    {
        $this->element = new CategoryElement();
        $this->element->groupId = $settings['elementGroup'][CategoryElement::class];

        $siteId = Hash::get($settings, 'siteId');

        if ($siteId) {
            $this->element->siteId = $siteId;
        }

        return $this->element;
    }

    /**
     * @inheritDoc
     */
    public function afterSave($data, $settings): void
    {
        $parent = Hash::get($data, 'parent');

        if ($parent && $parent !== $this->element->id) {
            $parentCategory = CategoryElement::find()->status(null)->id($parent)->one();

            if ($parentCategory) {
                Craft::$app->getStructures()->append($this->element->group->structureId, $this->element, $parentCategory);
            }
        }
    }

    // Protected Methods
    // =========================================================================

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

        $query = CategoryElement::find()
            ->status(null)
            ->andWhere(['=', $match, $value]);

        if (isset($this->api['siteId']) && $this->api['siteId']) {
            $query->siteId($this->api['siteId']);
        }

        if (!empty($this->element->groupId)) {
            $query->groupId($this->element->groupId);
        }

        $element = $query->one();

        if ($element) {
            return $element->id;
        }

        // Check if we should create the element. But only if title is provided (for the moment)
        if ($create && $match === 'title') {
            $element = new CategoryElement();
            $element->title = $value;
            $element->groupId = $this->element->groupId;

            if (!Craft::$app->getElements()->saveElement($element, true, true, Hash::get($this->api, 'updateSearchIndexes'))) {
                EasyApi::error('Category error: Could not create parent - `{e}`.', ['e' => Json::encode($element->getErrors())]);
            } else {
                EasyApi::info('Category `#{id}` added.', ['id' => $element->id]);
            }

            return $element->id;
        }

        if (!empty($default)) {
            $this->element->parentId = $default[0];

            return $default[0];
        }

        return null;
    }
}
