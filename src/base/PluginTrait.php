<?php

namespace runwildstudio\easyapi\base;

use Craft;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\services\AuthTypes;
use runwildstudio\easyapi\services\DataTypes;
use runwildstudio\easyapi\services\Elements;
use runwildstudio\easyapi\services\Apis;
use runwildstudio\easyapi\services\Fields;
use runwildstudio\easyapi\services\Logs;
use runwildstudio\easyapi\services\Process;
use runwildstudio\easyapi\services\Service;

trait PluginTrait
{
    // Properties
    // =========================================================================

    /**
     * @var EasyApi
     */
    public static EasyApi $plugin;

    /**
     * @var string $apiName Keeping state for logging
     */
    public static string $apiName = '';

    /**
     * @var
     */
    public static mixed $stepKey = null;


    // Static Methods
    // =========================================================================

    /**
     * @param $message
     * @param array $params
     * @param array $options
     * @throws \yii\base\InvalidConfigException
     */
    public static function error($message, array $params = [], array $options = []): void
    {
        EasyApi::$plugin->getLogs()->log(__METHOD__, $message, $params, $options);
    }

    /**
     * @param $message
     * @param array $params
     * @param array $options
     * @throws \yii\base\InvalidConfigException
     */
    public static function info($message, array $params = [], array $options = []): void
    {
        EasyApi::$plugin->getLogs()->log(__METHOD__, $message, $params, $options);
    }

    /**
     * @param $message
     */
    public static function debug($message): void
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            return;
        }

        if (Craft::$app->getRequest()->getSegment(-1) === 'debug') {
            echo "<pre>";
            print_r($message);
            echo "</pre>";
        }
    }


    // Public Methods
    // =========================================================================

    /**
     * @return AuthTypes
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuth(): AuthTypes
    {
        return $this->get('auth');
    }

    /**
     * @return DataTypes
     * @throws \yii\base\InvalidConfigException
     */
    public function getData(): DataTypes
    {
        return $this->get('data');
    }

    /**
     * @return Elements
     * @throws \yii\base\InvalidConfigException
     */
    public function getElements(): Elements
    {
        return $this->get('elements');
    }

    /**
     * @return Apis
     * @throws \yii\base\InvalidConfigException
     */
    public function getApis(): Apis
    {
        return $this->get('apis');
    }

    /**
     * @return Fields
     * @throws \yii\base\InvalidConfigException
     */
    public function getFields(): Fields
    {
        return $this->get('fields');
    }

    /**
     * @return Logs
     * @throws \yii\base\InvalidConfigException
     */
    public function getLogs(): Logs
    {
        return $this->get('logs');
    }

    /**
     * @return Process
     * @throws \yii\base\InvalidConfigException
     */
    public function getProcess(): Process
    {
        return $this->get('process');
    }

    /**
     * @return Service
     * @throws \yii\base\InvalidConfigException
     */
    public function getService(): Service
    {
        return $this->get('service');
    }
}
