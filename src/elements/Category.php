<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Craft;
use craft\base\ElementInterface;
use craft\elements\Category as CategoryElement;
use craft\errors\ElementNotFoundException;
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
}
