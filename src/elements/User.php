<?php

namespace runwildstudio\easyapi\elements;

use Cake\Utility\Hash;
use Craft;
use craft\base\ElementInterface;
use craft\db\Query;
use craft\elements\Asset as AssetElement;
use craft\elements\User as UserElement;
use craft\errors\VolumeException;
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\helpers\AssetHelper;
use runwildstudio\easyapi\helpers\DataHelper;
use craft\helpers\UrlHelper;
use craft\records\User as UserRecord;
use Throwable;
use yii\base\Exception;

/**
 *
 * @property-read string $mappingTemplate
 * @property-read bool $groups
 * @property-write mixed $model
 * @property-read string $groupsTemplate
 * @property-read string $columnTemplate
 */
class User extends Element
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'User';

    /**
     * @var string
     */
    public static string $class = UserElement::class;

    /**
     * @var
     */
    public mixed $status = null;

    // Templates
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroupsTemplate(): string
    {
        return 'easyapi/_includes/elements/user/groups';
    }

    /**
     * @inheritDoc
     */
    public function getColumnTemplate(): string
    {
        return 'easyapi/_includes/elements/user/column';
    }

    /**
     * @inheritDoc
     */
    public function getMappingTemplate(): string
    {
        return 'easyapi/_includes/elements/user/map';
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getGroups(): array
    {
        $result = [];

        // User are only allowed for Craft Pro
        if (Craft::$app->getEdition() == Craft::Pro) {
            $groups = Craft::$app->userGroups->getAllGroups();

            $result = count($groups) ? $groups : [];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getQuery($settings, array $params = []): mixed
    {
        $query = UserElement::find()
            ->status(null)
            ->siteId(Hash::get($settings, 'siteId'));
        Craft::configure($query, $params);
        return $query;
    }

    /**
     * @inheritDoc
     */
    public function setModel($settings): ElementInterface
    {
        $this->element = new UserElement();

        $this->status = null;

        $siteId = Hash::get($settings, 'siteId');

        if ($siteId) {
            $this->element->siteId = $siteId;
        }

        return $this->element;
    }
}
