<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Craft;
use craft\base\ElementInterface;
use craft\elements\Asset as AssetElement;
//use craft\feedme\elements\Asset as FeedMeAsset;
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
    public function init(): void
    {
        // If we are adding a new asset, it has to be done before the content is populated on the element.
        // We of course, want content to be populated on the newly-created element, not one that won't be uploaded
        Event::on(Process::class, Process::EVENT_STEP_BEFORE_PARSE_CONTENT, function(ApiProcessEvent $event) {
            $this->_handleImageCreation($event);
        });
    }

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

    public function beforeSave($element, $settings): bool
    {
        parent::beforeSave($element, $settings);

        // If we don't have an ID for this element, we don't want to continue. The reason is that we only want Easy API to
        // update an asset, not create a new one. Instead, asset-creation (upload from remote) is handled earlier in processing.
        // If this were to proceed, we'd get invalid assets created.
        if (!$element->id) {
            return false;
        }

        return true;
    }


    // Private Methods
    // =========================================================================

    /**
     * @param $event
     * @throws Exception
     */
    private function _handleImageCreation($event): void
    {
        $api = $event->api;
        $apiData = $event->apiData;

        // If we're not adding new assets, skip this altogether
        if (!DuplicateHelper::isAdd($api)) {
            return;
        }

        $fieldInfo = $api['fieldMapping']['urlOrPath'] ?? [];

        // Just in case...
        if (!$fieldInfo) {
            return;
        }

        $folderIdInfo = $api['fieldMapping']['folderId'] ?? [];
        $fileNameInfo = $api['fieldMapping']['filename'] ?? [];

        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $folderId = $this->parseFolderId($apiData, $folderIdInfo);
        $newFilename = $this->fetchSimpleValue($apiData, $fileNameInfo);

        $conflict = Hash::get($fieldInfo, 'options.conflict');

        // Do we want to match existing element? If one exists, we need to set our element to be that
        if ($conflict === AssetElement::SCENARIO_INDEX) {
            // Make sure to parse the URL into a filename to find the asset by
            $filename = $this->_getFilename($value);

            $filename = AssetsHelper::prepareAssetName($filename);

            $foundElement = AssetElement::find()
                ->folderId($folderId)
                ->filename($filename)
                ->includeSubfolders(true)
                ->siteId($api['siteId'])
                ->one();

            if ($foundElement) {
                $event->element = $foundElement;
                return;
            }
        }

        // We can't find an existing asset, we need to download from a remote URL, or local path
        $uploadedElementIds = AssetHelper::fetchRemoteImage([$value], $fieldInfo, $this->api, null, $this->element, $folderId, $newFilename);

        if ($uploadedElementIds) {
            $foundElement = AssetElement::find()
                ->id($uploadedElementIds[0])
                ->siteId($api['siteId'])
                ->one();

            if ($foundElement) {
                $event->element = $foundElement;
            }
        }
    }

    /**
     * @param $value
     * @return string
     */
    private function _getFilename($value): string
    {
        // If this is an absolute URL, we're uploading the asset. Parse it to just get the filename
        if (UrlHelper::isAbsoluteUrl($value)) {
            return AssetHelper::getRemoteUrlFilename($value);
        }

        // Otherwise, probably a local path
        return basename($value);
    }


    // Protected Methods
    // =========================================================================

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return string
     */
    protected function parseFilename($apiData, $fieldInfo): string
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);

        return $this->_getFilename($value);
    }

    /**
     * @param $apiData
     * @param $fieldInfo
     * @return int|null
     * @throws \craft\errors\AssetException
     * @throws \craft\errors\FsException
     * @throws \craft\errors\FsObjectExistsException
     */
    protected function parseFolderId($apiData, $fieldInfo): ?int
    {
        $value = $this->fetchSimpleValue($apiData, $fieldInfo);
        $create = Hash::get($fieldInfo, 'options.create');

        $assets = Craft::$app->getAssets();

        $volumeId = $this->element->volumeId;
        $rootFolder = $assets->getRootFolderByVolumeId($volumeId);

        if (is_numeric($value)) {
            return $value;
        }

        $folder = $assets->findFolder([
            'name' => $value,
            'volumeId' => $volumeId,
        ]);

        if ($folder) {
            return $folder->id;
        }

        if ($create) {
            $lastCreatedFolder = null;

            // Process all folders (create them)
            foreach (explode('/', $value) as $folderName) {
                $existingFolder = $assets->findFolder([
                    'name' => $folderName,
                    'volumeId' => $volumeId,
                ]);

                if ($existingFolder) {
                    $lastCreatedFolder = $existingFolder;
                    continue;
                }

                $parentFolder = $lastCreatedFolder ?? $rootFolder;

                $folderModel = new VolumeFolder();
                $folderModel->name = $folderName;
                $folderModel->parentId = $parentFolder->id;
                $folderModel->volumeId = $volumeId;
                $folderModel->path = $parentFolder->path . $folderName . '/';

                $assets->createFolder($folderModel);

                $lastCreatedFolder = $folderModel;
            }

            // Then, we just want the lowest level folder to use
            return $lastCreatedFolder->id;
        }

        // If we've provided a bad folder, just return the root - we always need a folderId
        return $rootFolder->id;
    }
}
