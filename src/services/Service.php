<?php

namespace runwildstudio\easyapi\services;

use ArrayAccess;
use Cake\Utility\Hash;
use Craft;
use craft\base\Component;
use runwildstudio\easyapi\EasyApi;
use craft\helpers\DateTimeHelper;
use DateTime;
use GuzzleHttp\Client;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param $key
     * @param null $apiId
     * @return array|ArrayAccess|mixed|null
     */
    public function getConfig($key, $apiId = null): mixed
    {
        $settings = EasyApi::$plugin->getSettings();

        // Get the config item from the global settings
        $configItem = Hash::get($settings, $key);

        // Or, check if there's a setting set per-api
        if ($apiId) {
            $configApiItem = Hash::get($settings, 'apiOptions.' . $apiId . '.' . $key);

            if ($configApiItem) {
                $configItem = $configApiItem;
            }
        }

        return $configItem;
    }

    /**
     * @param null $apiId
     * @return Client
     */
    public function createGuzzleClient($apiId = null): Client
    {
        $options = $this->getConfig('clientOptions', $apiId);

        return Craft::createGuzzleClient($options);
    }

    /**
     * @param $dateTime
     * @return DateTime|false
     * @throws \Exception
     */
    public function formatDateTime($dateTime): DateTime|bool
    {
        return DateTimeHelper::toDateTime($dateTime);
    }
}
