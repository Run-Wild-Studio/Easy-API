<?php

namespace runwildstudio\easyapi\services;

use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\db\ActiveQuery;
use craft\db\Query;
use runwildstudio\easyapi\errors\ApiException;
use runwildstudio\easyapi\events\ApiEvent;
use runwildstudio\easyapi\models\ApiModel;
use runwildstudio\easyapi\records\ApiRecord;
use craft\helpers\Json;
use Exception;
use Throwable;

/**
 *
 * @property-read mixed $totalApis
 */
class Apis extends Component
{
    // Properties
    // =========================================================================

    /**
     * @var array
     */
    private array $_overrides = [];

    // Constants
    // =========================================================================

    public const EVENT_BEFORE_SAVE_API = 'onBeforeSaveApi';
    public const EVENT_AFTER_SAVE_API = 'onAfterSaveApi';


    // Public Methods
    // =========================================================================

    /**
     * @param null $orderBy
     * @return array
     */
    public function getApis($orderBy = null): array
    {
        $query = $this->_getQuery();

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        $results = $query->all();

        foreach ($results as $key => $result) {
            $results[$key] = $this->_createModelFromRecord($result);
        }

        return $results;
    }

    /**
     * @return int
     */
    public function getTotalApis(): int
    {
        return count($this->getApis());
    }

    /**
     * @param $apiId
     * @return ApiModel|null
     */
    public function getApiById($apiId): ?ApiModel
    {
        $result = $this->_getQuery()
            ->where(['id' => $apiId])
            ->one();

        return $this->_createModelFromRecord($result);
    }

    /**
     * @param ApiModel $model
     * @param bool $runValidation
     * @return bool
     * @throws Exception
     */
    public function saveApi(ApiModel $model, bool $runValidation = true): bool
    {
        $isNewModel = !$model->id;

        // Fire a 'beforeSaveApi' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_API)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_API, new ApiEvent([
                'api' => $model,
                'isNew' => $isNewModel,
            ]));
        }

        if ($runValidation && !$model->validate()) {
            Craft::info('Api not saved due to validation error.', __METHOD__);
            return false;
        }

        if ($isNewModel) {
            $record = new ApiRecord();
        } else {
            $record = ApiRecord::findOne($model->id);

            if (!$record) {
                throw new Exception(Craft::t('easyapi', 'No api exists with the ID “{id}”', ['id' => $model->id]));
            }
        }

        $record->name = $model->name;
        $record->apiUrl = $model->apiUrl;
        $record->contentType = $model->contentType;
        $record->authorization = $model->authorization;
        $record->httpAction = $model->httpAction;
        $record->updateElementIdField = $model->updateElementIdField;
        $record->direction = $model->direction;
        $record->primaryElement = $model->primaryElement;
        $record->elementType = $model->elementType;
        $record->elementGroup = $model->elementGroup;
        $record->requestHeader = $model->requestHeader;
        $record->requestBody = $model->requestBody;
        $record->siteId = $model->siteId;
        $record->parentElement = $model->parentElement;
        $record->parentElementType = $model->parentElementType;
        $record->parentElementGroup = $model->parentElementGroup;
        $record->parentElementIdField = $model->parentElementIdField;
        $record->parentFilter = $model->parentFilter;
        $record->queueRequest = $model->queueRequest;
        $record->queueOrder = $model->queueOrder;
        $record->useLive = $model->useLive;
        $record->duplicateHandle = $model->duplicateHandle;
        $record->updateSearchIndexes = $model->updateSearchIndexes;
        $record->paginationNode = $model->paginationNode;

        if ($model->elementGroup) {
            $record->setAttribute('elementGroup', Json::encode($model->elementGroup));
        }

        if ($model->parentElementGroup) {
            $record->setAttribute('parentElementGroup', Json::encode($model->parentElementGroup));
        }

        if ($model->fieldMapping) {
            $record->setAttribute('fieldMapping', Json::encode($model->fieldMapping));
        }

        if ($model->fieldUnique) {
            $record->setAttribute('fieldUnique', Json::encode($model->fieldUnique));
        }

        if ($isNewModel) {
            $maxSortOrder = (new Query())
                ->from(['{{%easyapi_apis}}'])
                ->max('[[sortOrder]]');

            $record->sortOrder = $maxSortOrder ? $maxSortOrder + 1 : 1;
        }

        $record->save(false);

        if (!$model->id) {
            $model->id = $record->id;
            $model->fieldMapping = $record->fieldMapping;
            $model->fieldUnique = $record->fieldUnique;
        }

        // Fire an 'afterSaveApi' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_API)) {
            $this->trigger(self::EVENT_AFTER_SAVE_API, new ApiEvent([
                'api' => $model,
                'isNew' => $isNewModel,
            ]));
        }

        return true;
    }

    /**
     * @param $apiId
     * @return int
     * @throws \yii\db\Exception
     */
    public function deleteApiById($apiId): int
    {
        return Craft::$app->getDb()->createCommand()
            ->delete('{{%easyapi_apis}}', ['id' => $apiId])
            ->execute();
    }

    /**
     * @param $api
     * @return bool
     * @throws Exception
     */
    public function duplicateApi($api): bool
    {
        $api->id = null;

        return $this->saveApi($api);
    }

    /**
     * @param $handle
     * @param $apiId
     * @return mixed|null
     */
    public function getModelOverrides($handle, $apiId): mixed
    {
        if (empty($this->_overrides[$apiId])) {
            $this->_overrides[$apiId] = Hash::get(Craft::$app->getConfig()->getConfigFromFile('easyapi'), 'apiOptions.' . $apiId);
        }

        return $this->_overrides[$apiId][$handle] ?? null;
    }

    /**
     * @param array $apiIds
     * @return bool
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function reorderApis(array $apiIds): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($apiIds as $apiOrder => $apiId) {
                $apiRecord = $this->_getApiRecordById($apiId);
                $apiRecord->sortOrder = $apiOrder + 1;
                $apiRecord->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }


    // Private Methods
    // =========================================================================

    /**
     * @return ActiveQuery
     */
    private function _getQuery(): ActiveQuery
    {
        return ApiRecord::find()
            ->select([
                'id',
                'name',
                'apiUrl',
                'contentType',
                'authorization',
                'httpAction',
                'updateElementIdField',
                'direction',
                'primaryElement',
                'elementType',
                'elementGroup',
                'requestHeader',
                'requestBody',
                'siteId',
                'sortOrder',
                'fieldMapping',
                'fieldUnique',
                'parentElement',
                'parentElementType',
                'parentElementGroup',
                'parentElementIdField',
                'parentFilter',
                'queueRequest',
                'queueOrder',
                'useLive',
                'dateCreated',
                'dateUpdated',
                'uid',
                'duplicateHandle',
                'updateSearchIndexes',
                'paginationNode',
                'paginationUrl',
                'passkey',
            ])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    /**
     * @param ApiRecord|null $record
     * @return ApiModel|null
     */
    private function _createModelFromRecord(ApiRecord $record = null): ?ApiModel
    {
        if (!$record) {
            return null;
        }

        $attributes = $record->toArray();

        $attributes['elementGroup'] = Json::decode($attributes['elementGroup']);
        $attributes['duplicateHandle'] = Json::decode($attributes['duplicateHandle']);
        $attributes['fieldMapping'] = Json::decode($attributes['fieldMapping']);
        $attributes['fieldUnique'] = Json::decode($attributes['fieldUnique']);

        foreach ($attributes as $attribute => $value) {
            $override = $this->getModelOverrides($attribute, $record['id']);

            if ($override) {
                $attributes[$attribute] = $override;
            }
        }

        return new ApiModel($attributes);
    }

    /**
     * @param int|null $apiId
     * @return ApiRecord
     * @throws Exception
     */
    private function _getApiRecordById(int $apiId = null): ApiRecord
    {
        if ($apiId !== null) {
            $apiRecord = ApiRecord::findOne(['id' => $apiId]);

            if (!$apiRecord) {
                throw new ApiException(Craft::t('easyapi', 'No api exists with the ID “{id}”.', ['id' => $apiId]));
            }
        } else {
            $apiRecord = new ApiRecord();
        }

        return $apiRecord;
    }
}
