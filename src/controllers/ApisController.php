<?php

namespace runwildstudio\easyapi\controllers;

use Cake\Utility\Hash;
use Craft;
use craft\errors\MissingComponentException;
use runwildstudio\easyapi\models\ApiModel;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\queue\jobs\ApiImport;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\web\Controller;
use Exception;
use Throwable;
use yii\base\ExitException;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * @property Plugin $module
 */
class ApisController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @var string[]
     */
    protected int|bool|array $allowAnonymous = ['run-task'];


    // Public Methods
    // =========================================================================

    /**
     * @return Response
     */
    public function actionApisIndex(): Response
    {
        $variables['apis'] = EasyApi::$plugin->apis->getApis();

        return $this->renderTemplate('easyapi/apis/index', $variables);
    }

    /**
     * @param null $apiId
     * @param null $api
     * @return Response
     */
    public function actionEditApi($apiId = null, $api = null): Response
    {
        $variables = [];

        if (!$api) {
            if ($apiId) {
                $variables['api'] = EasyApi::$plugin->apis->getApiById($apiId);
            } else {
                $variables['api'] = new ApiModel();
            }
        } else {
            $variables['api'] = $api;
        }

        $variables['dataTypes'] = EasyApi::$plugin->data->dataTypesList();
        $variables['elements'] = EasyApi::$plugin->elements->getRegisteredElements();

        return $this->renderTemplate('easyapi/apis/_edit', $variables);
    }

    /**
     * @param null $apiId
     * @param null $postData
     * @return Response
     */
    public function actionElementApi($apiId = null, $postData = null): Response
    {
        $variables = [];

        $api = EasyApi::$plugin->apis->getApiById($apiId);

        if ($postData) {
            $api = Hash::merge($api, $postData);
        }

        $variables['primaryElements'] = $api->getApiNodes();
        $variables['apiMappingData'] = $api->getApiMapping(false);
        $variables['api'] = $api;

        return $this->renderTemplate('easyapi/apis/_element', $variables);
    }

    /**
     * @param null $apiId
     * @param null $postData
     * @return Response
     */
    public function actionMapApi($apiId = null, $postData = null): Response
    {
        $variables = [];

        $api = EasyApi::$plugin->apis->getApiById($apiId);

        if ($postData) {
            $api = Hash::merge($api, $postData);
        }

        $variables['apiMappingData'] = $api->getApiMapping();
        $variables['api'] = $api;

        return $this->renderTemplate('easyapi/apis/_map', $variables);
    }

    /**
     * @param null $apiId
     * @return Response
     * @throws \yii\base\Exception
     */
    public function actionRunApi($apiId = null): Response
    {
        $request = Craft::$app->getRequest();

        $api = EasyApi::$plugin->apis->getApiById($apiId);

        $return = $request->getParam('return') ?: 'easyapi';

        $variables['api'] = $api;
        $variables['task'] = $this->_runImportTask($api);

        if ($request->getParam('direct')) {
            $view = $this->getView();
            $view->setTemplateMode($view::TEMPLATE_MODE_CP);
            return $this->renderTemplate('easyapi/apis/_direct', $variables);
        }

        return $this->redirect($return);
    }

    /**
     * @param null $apiId
     * @return Response
     */
    public function actionStatusApi($apiId = null): Response
    {
        $api = EasyApi::$plugin->apis->getApiById($apiId);

        $variables['api'] = $api;

        return $this->renderTemplate('easyapi/apis/_status', $variables);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionSaveApi(): ?Response
    {
        $api = $this->_getModelFromPost();

        if ($api->getErrors()) {
            $this->setFailFlash(Craft::t('easyapi', 'Couldn’t save the api.'));

            // Send the category group back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'api' => $api,
            ]);

            return null;
        }

        return $this->_saveAndRedirect($api, 'easyapi/apis/', true);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionSaveAndElementApi(): ?Response
    {
        $api = $this->_getModelFromPost();

        if ($api->getErrors()) {
            $this->setFailFlash(Craft::t('easyapi', 'Couldn’t save the api.'));

            // Send the category group back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'api' => $api,
            ]);

            return null;
        }

        return $this->_saveAndRedirect($api, 'easyapi/apis/element/', true);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionSaveAndMapApi(): ?Response
    {
        $api = $this->_getModelFromPost();

        return $this->_saveAndRedirect($api, 'easyapi/apis/map/', true);
    }

    /**
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionSaveAndReviewApi(): ?Response
    {
        $api = $this->_getModelFromPost();

        return $this->_saveAndRedirect($api, 'easyapi/apis/status/', true);
    }

    /**
     * @return Response
     * @throws MissingComponentException
     */
    public function actionSaveAndDuplicateApi(): Response
    {
        $request = Craft::$app->getRequest();

        $apiId = $request->getParam('apiId');
        $api = EasyApi::$plugin->apis->getApiById($apiId);

        EasyApi::$plugin->apis->duplicateApi($api);

        Craft::$app->getSession()->setNotice(Craft::t('easyapi', 'Api duplicated.'));

        return $this->redirect('easyapi/apis');
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionDeleteApi(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $apiId = $request->getRequiredBodyParam('id');

        EasyApi::$plugin->apis->deleteApiById($apiId);

        return $this->asJson(['success' => true]);
    }

    /**
     * @throws \yii\base\Exception
     * @throws ExitException
     */
    public function actionRunTask(): void
    {
        $request = Craft::$app->getRequest();

        $apiId = $request->getParam('apiId');

        if ($apiId) {
            $this->actionRunApi($apiId);
        }

        Craft::$app->end();
    }

    /**
     * @return false|string
     * @throws Exception
     */
    public function actionDebug(): bool|string
    {
        $request = Craft::$app->getRequest();

        $apiId = $request->getParam('apiId');
        $limit = $request->getParam('limit');
        $offset = $request->getParam('offset');

        $api = EasyApi::$plugin->apis->getApiById($apiId);

        ob_start();

        // Keep track of processed elements here - particularly for paginated apis
        $processedElementIds = [];

        EasyApi::$plugin->process->debugApi($api, $limit, $offset, $processedElementIds);

        return ob_get_clean();
    }

    /**
     * @return Response
     * @throws Throwable
     * @throws BadRequestHttpException
     */
    public function actionReorderApis(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $apiIds = Json::decode(Craft::$app->getRequest()->getRequiredBodyParam('ids'));
        $apiIds = array_filter($apiIds);
        EasyApi::$plugin->getApis()->reorderApis($apiIds);

        return $this->asJson(['success' => true]);
    }


    // Private Methods
    // =========================================================================

    /**
     * @param $api
     * @return bool|null
     * @throws MissingComponentException
     */
    private function _runImportTask($api): ?bool
    {
        $request = Craft::$app->getRequest();

        $direct = $request->getParam('direct');
        $authorization = $request->getParam('authorization');
        $url = $request->getParam('url');

        $limit = $request->getParam('limit');
        $offset = $request->getParam('offset');

        // Keep track of processed elements here - particularly for paginated apis
        $processedElementIds = [];

        // Are we running from the CP?
        if ($request->getIsCpRequest()) {
            // if not using the direct param for this request, do UI stuff
            Craft::$app->getSession()->setNotice(Craft::t('easyapi', 'Api processing started.'));

            // Create the import task
            $this->module->queue->push(new ApiImport([
                'api' => $api,
                'limit' => $limit,
                'offset' => $offset,
                'processedElementIds' => $processedElementIds,
            ]));
        }

        // If not, are we running directly?
        if ($direct) {
            // If a custom URL param is provided (for direct-processing), use that instead of stored URL
            if ($url) {
                $api->apiUrl = $url;
            }

            $proceed = $authorization == $api['authorization'];

            // Create the import task only if provided the correct authorization
            if ($proceed) {
                $this->module->queue->push(new ApiImport([
                    'api' => $api,
                    'limit' => $limit,
                    'offset' => $offset,
                    'processedElementIds' => $processedElementIds,
                ]));
            }

            return $proceed;
        }

        return null;
    }

    /**
     * @param $api
     * @param $redirect
     * @param false $withId
     * @return Response|null
     * @throws MissingComponentException
     */
    private function _saveAndRedirect($api, $redirect, bool $withId = false): ?Response
    {
        if (!EasyApi::$plugin->apis->saveApi($api)) {
            Craft::$app->getSession()->setError(Craft::t('easyapi', 'Unable to save api.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'api' => $api,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('easyapi', 'Api saved.'));

        if ($withId) {
            $redirect .= $api->id;
        }

        return $this->redirect($redirect);
    }

    /**
     * @return ApiModel
     * @throws BadRequestHttpException
     */
    private function _getModelFromPost(): ApiModel
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        if ($request->getBodyParam('apiId')) {
            $api = EasyApi::$plugin->apis->getApiById($request->getBodyParam('apiId'));
        } else {
            $api = new ApiModel();
        }

        $api->name = $request->getBodyParam('name', $api->name);
        $api->apiUrl = $request->getBodyParam('apiUrl', $api->apiUrl);
        $api->contentType = $request->getBodyParam('contentType', $api->contentType);
        $api->authorization = $request->getBodyParam('authorization', $api->authorization);
        $api->httpAction = $request->getBodyParam('httpAction', $api->httpAction);
        $api->direction = $request->getBodyParam('direction', $api->direction);
        $api->primaryElement = $request->getBodyParam('primaryElement', $api->primaryElement);
        $api->elementType = $request->getBodyParam('elementType', $api->elementType);
        $api->elementGroup = $request->getBodyParam('elementGroup', $api->elementGroup);
        $api->requestHeader = $request->getBodyParam('requestHeader', $api->requestHeader);
        $api->requestBody = $request->getBodyParam('requestBody', $api->requestBody);
        $api->siteId = $request->getBodyParam('siteId', $api->siteId);
        $api->parentElement = $request->getBodyParam('parentElement', $api->parentElement);
        $api->parentElementType = $request->getBodyParam('parentElementType', $api->parentElementType);
        $api->parentElementGroup = $request->getBodyParam('parentElementGroup', $api->parentElementGroup);
        $api->parentElementIdField = $request->getBodyParam('parentElementIdField', $api->parentElementIdField);
        $api->parentFilter = $request->getBodyParam('parentFilter', $api->parentFilter);
        $api->queueRequest = $request->getBodyParam('queueRequest', $api->queueRequest);
        $api->useLive = $request->getBodyParam('useLive', $api->useLive);
        $api->singleton = $request->getBodyParam('singleton', $api->singleton);
        $api->duplicateHandle = $request->getBodyParam('duplicateHandle', $api->duplicateHandle);
        $api->updateSearchIndexes = (bool)$request->getBodyParam('updateSearchIndexes', $api->updateSearchIndexes);
        $api->paginationNode = $request->getBodyParam('paginationNode', $api->paginationNode);

        if ($request->getBodyParam('queueOrder', $api->queueOrder) != '')
        {
            $api->queueOrder = $request->getBodyParam('queueOrder', $api->queueOrder);
        } else {
            $api->queueOrder = null;
        }
        // Don't overwrite mappings when saving from first screen
        if ($request->getBodyParam('fieldMapping')) {
            $api->fieldMapping = $request->getBodyParam('fieldMapping');
        }

        if ($request->getBodyParam('fieldUnique')) {
            $api->fieldUnique = $request->getBodyParam('fieldUnique');
        }

        // Check conditionally on Element Group fields - depending on the Element Type selected
        if (isset($api->elementGroup[$api->elementType])) {
            $elementGroup = $api->elementGroup[$api->elementType];

            if (($api->elementType === 'craft\elements\Category') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Category Group is required'));
            }

            if ($api->elementType === 'craft\elements\Entry') {
                if (empty($elementGroup['section']) || empty($elementGroup['entryType'])) {
                    $api->addError('elementGroup', Craft::t('easyapi', 'Entry Section and Type are required'));
                }
            }

            if (($api->elementType === 'craft\commerce\elements\Product') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Commerce Product Type is required'));
            }

            if (($api->elementType === 'craft\digitalproducts\elements\Product') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Digital Product Group is required'));
            }

            if (($api->elementType === 'craft\elements\Asset') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Asset Volume is required'));
            }

            if (($api->elementType === 'craft\elements\Tag') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Tag Group is required'));
            }

            if (($api->elementType === 'Solspace\Calendar\Elements\Event') && empty($elementGroup)) {
                $api->addError('elementGroup', Craft::t('easyapi', 'Calendar is required'));
            }
        }

        return $api;
    }
}
