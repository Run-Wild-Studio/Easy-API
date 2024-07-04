<?php

namespace runwildstudio\easyapi\services;

use ArrayAccess;
use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\GlobalSet;
use craft\elements\Tag;
use craft\errors\MissingComponentException;
use craft\feedme\events\FeedDataEvent;
use runwildstudio\easyapi\base\DataTypeInterface;
use runwildstudio\easyapi\datatypes\Json;
use runwildstudio\easyapi\datatypes\Xml;
use runwildstudio\easyapi\events\ApiDataEvent;
use runwildstudio\easyapi\events\RegisterEasyApiDataTypesEvent;
use runwildstudio\easyapi\models\ApiModel;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Component as ComponentHelper;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 *
 * @property-read mixed $registeredDataTypes
 */
class EasyApiDataTypes extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_EASY_API_DATA_TYPES = 'registerEasyApiDataTypes';
    public const EVENT_BEFORE_FETCH_API = 'onBeforeFetchApi';
    public const EVENT_AFTER_FETCH_API = 'onAfterFetchApi';
    public const EVENT_AFTER_PARSE_API = 'onAfterParseApi';


    // Properties
    // =========================================================================

    /**
     * @var array
     */
    private array $_dataTypes = [];

    /**
     * @var
     */
    private mixed $_headers = null;

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        foreach ($this->getRegisteredApiDataTypes() as $dataTypeClass) {
            $dataType = $this->createDataType($dataTypeClass);

            // Does this data type exist in Craft right now?
            if (!class_exists($dataType->getClass())) {
                continue;
            }

            // strtolower for backwards compatibility
            $handle = strtolower($dataType::displayName());

            $this->_dataTypes[$handle] = $dataType;
        }
    }

    /**
     * @return array
     */
    public function dataTypesList(): array
    {
        $list = [];

        foreach ($this->_dataTypes as $handle => $dataType) {
            $list[$handle] = $dataType::$name;
        }

        return $list;
    }

    /**
     * @param $handle
     * @return mixed|null
     */
    public function getRegisteredApiDataType($handle): mixed
    {
        return $this->_dataTypes[$handle] ?? null;
    }

    /**
     * @return array
     */
    public function getRegisteredApiDataTypes(): array
    {
        $event = new RegisterEasyApiDataTypesEvent([
            'dataTypes' => [
                Json::class,
                Xml::class,
            ],
        ]);

        $this->trigger(self::EVENT_REGISTER_EASY_API_DATA_TYPES, $event);

        return $event->dataTypes;
    }

    /**
     * @param $config
     * @return DataTypeInterface
     * @throws InvalidConfigException
     */
    public function createDataType($config): DataTypeInterface
    {
        if (is_string($config)) {
            $config = ['type' => $config];
        }

        try {
            $dataType = ComponentHelper::createComponent($config, DataTypeInterface::class);
        } catch (MissingComponentException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);

            $dataType = new MissingDataType($config);
        }

        /** @var DataTypeInterface $dataType */
        return $dataType;
    }

    /**
     * @param $url
     * @param null $apiId
     * @return array
     */
    public function getRawData($url, $apiId = null): array
    {
        $event = new ApiDataEvent([
            'url' => $url,
            'apiId' => $apiId,
        ]);

        Event::trigger(static::class, self::EVENT_BEFORE_FETCH_API, $event);

        if ($event->response) {
            return $event->response;
        }

        $url = $event->url;
        $url = Craft::getAlias($url);
        $api = EasyApi::$plugin->apis->getApiById($apiId);
        if ($url != "") {
            $api->apiUrl = $url;
        }

        $auth = $api->getAuthType()->getAuthValue($api);
        if (!$auth['success']) {
            $response = ['success' => false, 'error' => $auth['error']];
            Craft::$app->getErrorHandler()->logException($auth['error']);
        }

        try {
            $curl = curl_init();
            $curl_Header = [];
            $curl_Header[] = 'Content-Type: application/' . $api->contentType;
            $curl_Header[] = $auth['value'];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $api->httpAction,
                CURLOPT_HTTPHEADER => $curl_Header,
            ));

            $data = curl_exec($curl);
            curl_close($curl);

            $response = ['success' => true, 'data' => $data];
        } catch (Exception $e) {
            $response = ['success' => false, 'error' => $e->getMessage()];
            Craft::$app->getErrorHandler()->logException($e);
        }

        return $this->_triggerEventAfterFetchApi([
            'url' => $url,
            'apiId' => $apiId,
            'response' => $response,
        ]);
    }

    public function getDataForFeedMe(FeedDataEvent $event) {
        $api = EasyApi::$plugin->apis->getApiByFeedId($event->feedId);

        if ($api) {
            $responseData = null;
            try {
                if ($api->parentElementType != null && $api->parentElementType != "") {
                    $originalUrl = $api->apiUrl;
                    switch ($api->parentElementType) {
                        case 'craft\\elements\\Asset':
                            $assetId = $api->parentElementGroup[$api->parentElementType];
                
                            $parents = Asset::find()
                                ->siteId($api->siteId)
                                ->assetId($assetId)
                                ->all();
                            break;
        
                        case 'craft\\elements\\Category':
                            $groupId = $api->parentElementGroup[$api->parentElementType];
                            
                            $parents = Category::find()
                                ->siteId($api->siteId)
                                ->groupId($groupId)
                                ->all();
                            break;
        
                        case 'craft\\elements\\Entry':
                            $sectionId = $api->parentElementGroup[$api->parentElementType]["section"];
                            $entryTypeId = $api->parentElementGroup[$api->parentElementType]["entryType"];
                
                            $parents = Entry::find()
                                ->siteId($api->siteId)
                                ->sectionId($sectionId)
                                ->typeId($entryTypeId)
                                ->all();
                            break;
                            
                        case 'craft\\elements\\Tag':
                            $tagId = $api->parentElementGroup[$api->parentElementType];
                
                            $parents = Tag::find()
                                ->siteId($api->siteId)
                                ->tagId($tagId)
                                ->all();
                            break;
                            
                            case 'craft\\elements\\GlobalSet':
                                $globalSetId = $api->parentElementGroup[parentElementType]->globalSet;
                    
                                $parents = Glogal::find()
                                    ->siteId($api->siteId)
                                    ->globalSetId($globalSetId)
                                    ->all();
                                break;
        
                        default:
                            # shouldn't get here
                            break;
                    }
                    foreach ($parents as $parent) {
                        // Access entry fields
                        $dynamicValue = $parent->getFieldValue($api->parentElementIdField); // Replace 'yourDynamicField' with the handle of your dynamic field
    
                        // Original string with placeholder
                        $originalString = $originalUrl;
    
                        // Replace the placeholder with the dynamic value
                        $modifiedString = str_replace('{{ Id }}', $dynamicValue, $originalString);
                        
                        $apiData = $this->getRawData($modifiedString, $api->id);

                        if ($responseData != null) {
                            $array1 = json_decode($responseData['data'], true);
                            $array2 = json_decode($apiData['data'], true);
                            
                            // Merge arrays
                            $mergedArray = array_merge_recursive($array1, $array2);
                            
                            // Encode merged array back to JSON
                            $responseData['data'] = json_encode($mergedArray);
                        } else {
                            $responseData = $apiData;
                        }
                    }
                    $api->apiUrl = $originalUrl;
                } else {
                    $responseData = $this->getRawData($api->apiUrl, $api->id);
                }

                $event->response = $responseData;
            } catch (Throwable $e) {
                // Even though we catch errors on each step of the loop, make sure to catch errors that can be anywhere
                // else in this function, just to be super-safe and not cause the queue job to die.
                EasyApi::error('`{e} - {f}: {l}`.', ['e' => $e->getMessage(), 'f' => basename($e->getFile()), 'l' => $e->getLine()]);
                Craft::$app->getErrorHandler()->logException($e);

                $event->response = [
                    'success' => false,
                    'data' => $e->getMessage(),
                ];
            }
        }
    }

    /**
     * @param $apiModel
     * @param bool $usePrimaryElement
     * @return mixed
     */
    public function getApiData($apiModel, bool $usePrimaryElement = true): mixed
    {
        $apiDataResponse = $apiModel->getDataType()->getApi($apiModel->apiUrl, $apiModel, $usePrimaryElement);

        $event = new ApiDataEvent([
            'url' => $apiModel->apiUrl,
            'response' => $apiDataResponse,
            'apiId' => $apiModel->id,
        ]);

        Event::trigger(static::class, self::EVENT_AFTER_PARSE_API, $event);

        return $event->response;
    }

    /**
     * @param array $options
     * @return array|ArrayAccess|mixed|null
     */
    public function getApiForTemplate(array $options = []): mixed
    {
        $pluginSettings = EasyApi::$plugin->getSettings();

        $url = Hash::get($options, 'url');
        $type = Hash::get($options, 'type');
        $element = Hash::get($options, 'element');
        $cache = Hash::get($options, 'cache', true);

        $limit = Hash::get($options, 'limit');
        $offset = Hash::get($options, 'offset');

        // We can additionally fetch just the headers for the request if required
        $headers = Hash::get($options, 'headers');

        $cacheId = ($headers) ? $url . '#' . $element : $url . '#headers-' . $element;

        // Check for some required options
        if (!$url || !$type) {
            return [];
        }

        $api = new ApiModel();
        $api->apiUrl = $url;
        $api->contentType = $type;

        if ($element) {
            $api->primaryElement = $element;
        }

        // If cache explicitly set to false, always return latest data
        if ($cache === false) {
            if ($headers) {
                $data = $this->_headers;
            } else {
                $data = Hash::get($this->getApiData($api), 'data');
            }

            if ($offset) {
                $data = array_slice($data, $offset);
            }

            if ($limit) {
                $data = array_slice($data, 0, $limit);
            }

            return $data;
        }

        // We want some caching action!
        if (is_numeric($cache) || $cache === true) {
            $cache = (is_numeric($cache)) ? $cache : $pluginSettings->cache;

            $cachedRequest = $this->_getCache($cacheId);

            if ($cachedRequest) {
                return $cachedRequest;
            }

            if ($headers) {
                $data = $this->_headers;
            } else {
                $data = Hash::get($this->getApiData($api), 'data');
            }

            if ($offset) {
                $data = array_slice($data, $offset);
            }

            if ($limit) {
                $data = array_slice($data, 0, $limit);
            }

            $this->_setCache($cacheId, $data, $cache);
            return $data;
        }

        return [];
    }


    // Private
    // =========================================================================

    /**
     * @param $url
     * @param $value
     * @param $duration
     * @return void
     */
    private function _setCache($url, $value, $duration): void
    {
        Craft::$app->cache->set(base64_encode(urlencode($url)), $value, $duration, null);
    }

    /**
     * @param $url
     * @return mixed
     */
    private function _getCache($url): mixed
    {
        return Craft::$app->cache->get(base64_encode(urlencode($url)));
    }

    /**
     * Trigger EVENT_AFTER_FETCH_API
     *
     * @param $data
     * @return mixed
     */
    private function _triggerEventAfterFetchApi($data)
    {
        $event = new ApiDataEvent($data);

        Event::trigger(static::class, self::EVENT_AFTER_FETCH_API, $event);

        return $event->response;
    }
}
