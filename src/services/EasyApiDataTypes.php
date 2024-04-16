<?php

namespace runwildstudio\easyapi\services;

use ArrayAccess;
use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use craft\errors\MissingComponentException;
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

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $api->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $api->httpAction,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/' . $api->contentType,
                    'Authorization: ' . $api->authorization
                ),
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
     * @param $data
     * @return array
     */
    public function getApiNodes($data): array
    {
        if (!is_array($data)) {
            return [];
        }

        $tree = [];
        $this->_parseNodeTree($tree, $data);
        $nodes = [];

        $elements = (count($data) > 1) ? ' elements' : ' element';
        $nodes[''] = '/root (x' . count($data) . $elements . ')';

        foreach ($tree as $key => $value) {
            $elements = ($value > 1) ? ' elements' : ' element';
            $index = array_values(array_slice(explode('/', $key), -1))[0];

            if (!isset($nodes[$index])) {
                $nodes[$index] = $key . ' (x' . $value . $elements . ')';
            }
        }

        return $nodes;
    }

    /**
     * @param $data
     * @return array
     */
    public function getApiMapping($data): array
    {
        if (!is_array($data)) {
            return [];
        }

        $mappingPaths = [];

        // Go through entire api and grab all nodes - that way, it's normalised across the entire api
        // as some nodes don't exist on the first primary element, but do throughout the api.
        foreach (Hash::flatten($data, '/') as $nodePath => $value) {
            $apiPath = preg_replace('/(\/\d+\/)/', '/', $nodePath);
            $apiPath = preg_replace('/^(\d+\/)|(\/\d+)/', '', $apiPath);

            // The above is used to normalise repeatable nodes. Paths to nodes will look similar to:
            // 0.Assets.Asset.0.Img.0 - we want to change this to Assets/Asset/Img, This is mostly
            // for user-friendliness, we don't need to keep specific details on what is repeatable
            // or not. That's for the api-parsing stage (and is greatly improved from our first iteration!)

            if (!isset($mappingPaths[$apiPath])) {
                $mappingPaths[$apiPath] = $value;
            }
        }

        return $mappingPaths;
    }

    /**
     * @param $element
     * @param $parsed
     * @return array|bool
     */
    public function findPrimaryElement($element, $parsed): array|bool
    {
        if (empty($parsed)) {
            return false;
        }

        // If no primary element, return root
        if (!$element) {
            return $parsed;
        }

        // Ensure we return an array - even if only one element found
        if (isset($parsed[$element]) && is_array($parsed[$element])) {
            if (array_key_exists('0', $parsed[$element])) { // is multidimensional
                return $parsed[$element];
            }

            return [$parsed[$element]];
        }

        foreach ($parsed as $val) {
            if (is_array($val)) {
                $return = $this->findPrimaryElement($element, $val);

                if ($return !== false) {
                    return $return;
                }
            }
        }

        return false;
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
     * @param $tree
     * @param $array
     * @param string $index
     */
    private function _parseNodeTree(&$tree, $array, string $index = ''): void
    {
        foreach ($array as $key => $val) {
            if (!is_numeric($key)) {
                if (is_array($val)) {
                    $count = count($val);

                    if (Hash::dimensions($val) == 1) {
                        $count = 1;
                    }

                    $tree[$index . '/' . $key] = $count;

                    $this->_parseNodeTree($tree, $val, $index . '/' . $key);
                }
            } elseif (is_array($val)) {
                $this->_parseNodeTree($tree, $val, $index);
            }
        }
    }

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