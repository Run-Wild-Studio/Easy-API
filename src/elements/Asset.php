<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Craft;
use craft\base\ElementInterface;
use craft\elements\Asset as AssetElement;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\helpers\AssetHelper;
use runwildstudio\easyapi\helpers\DuplicateHelper;
use runwildstudio\easyapi\services\Process;
use craft\helpers\Assets as AssetsHelper;
use craft\helpers\UrlHelper;
use craft\models\VolumeFolder;
use yii\base\Event;
use yii\base\Exception;

/**
 *
 * @property-read string $mappingTemplate
 * @property-read mixed $groups
 * @property-write mixed $model
 * @property-read string $groupsTemplate
 * @property-read string $columnTemplate
 */
class Asset extends Element
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'Asset';

    /**
     * @var string
     */
    public static string $class = AssetElement::class;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroupsTemplate(): string
    {
        return 'easyapi/_includes/elements/assets/groups';
    }

    /**
     * @inheritDoc
     */
    public function getColumnTemplate(): string
    {
        return 'easyapi/_includes/elements/assets/column';
    }

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/elements/assets/map';
    }


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        return Craft::$app->volumes->getAllVolumes();
    }

    /**
     * @inheritDoc
     */
    public function getQuery($settings, array $params = []): mixed
    {
        $query = AssetElement::find()
            ->status(null)
            ->volumeId($settings['elementGroup'][AssetElement::class])
            ->includeSubfolders()
            ->siteId(Hash::get($settings, 'siteId') ?: Craft::$app->getSites()->getPrimarySite()->id);
        Craft::configure($query, $params);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function setModel($settings): ElementInterface
    {
        $this->element = new AssetElement();
        $this->element->volumeId = $settings['elementGroup'][AssetElement::class];

        $siteId = Hash::get($settings, 'siteId');

        if ($siteId) {
            $this->element->siteId = $siteId;
        }

        return $this->element;
    }
}
