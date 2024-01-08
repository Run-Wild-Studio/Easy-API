<?php

namespace runwildstudio\easyapi\datatypes;

use Cake\Utility\Hash;
use Craft;
use runwildstudio\easyapi\base\DataType;
use runwildstudio\easyapi\base\DataTypeInterface;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\Json as JsonHelper;
use Seld\JsonLint\JsonParser;
use yii\base\InvalidArgumentException;

class Json extends DataType implements DataTypeInterface
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static string $name = 'JSON';


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getApi($url, $settings, bool $usePrimaryElement = true): array
    {
        $apiId = Hash::get($settings, 'id');
        $response = EasyApi::$plugin->data->getRawData($url, $apiId);

        if (!$response['success']) {
            $error = 'Unable to reach ' . $url . '. Message: ' . $response['error'];

            EasyApi::error($error);

            return ['success' => false, 'error' => $error];
        }

        $data = $response['data'];

        // Parse the JSON string
        try {
            $array = JsonHelper::decode($data);
        } catch (InvalidArgumentException $e) {
            // See if we can get a better error with JsonParser
            $e = (new JsonParser())->lint($data) ?: $e;
            $error = 'Invalid JSON: ' . $e->getMessage();
            EasyApi::error($error);
            Craft::$app->getErrorHandler()->logException($e);
            return ['success' => false, 'error' => $error];
        }

        // Make sure it's indeed an array!
        if (!is_array($array)) {
            $error = 'Invalid JSON: ' . json_last_error_msg();
            EasyApi::error($error);
            return ['success' => false, 'error' => $error];
        }

        // If using pagination, set it up here - we need to do this before messing around with the primary element
        $this->setupPaginationUrl($array, $settings);

        // Look for and return only the items for primary element
        $primaryElement = Hash::get($settings, 'primaryElement');

        if ($primaryElement && $usePrimaryElement) {
            $array = EasyApi::$plugin->data->findPrimaryElement($primaryElement, $array);
        }

        return ['success' => true, 'data' => $array];
    }
}
