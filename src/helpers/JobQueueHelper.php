<?php

namespace runwildstudio\easyapi\helpers;

use Craft;
use craft\queue\BaseJob;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\console\controllers\ApisController;
use runwildstudio\easyapi\queue\jobs\ApiImport;

class JobQueueHelper extends BaseJob
{
    public function execute($queue): void
    {
        $apis = EasyApi::getInstance()->getApis();
        $settings = EasyApi::$plugin->getSettings();
        $apisController = new ApisController(null, null);

        $processedElementIds = [];
        foreach ($apis->getApis('queueOrder') as $api) {
            if ($api->queueRequest) {
                // $apisController->actionQueue($api->id);
                // $tally++;

                //$apiModel = EasyApi::$plugin->apis->getApiById($api->id);
                EasyApi::getInstance()->module->queue->push(new ApiImport([
                    'api' => $api,
                    'limit' => null,
                    'offset' => null,
                    'processedElementIds' => $processedElementIds
                ]));
            }
        }

        $job = new \runwildstudio\easyapi\helpers\JobQueueHelper([
            'description' => 'API Integration background process',
        ]);

        $delayInSeconds = $settings->jobQueueInterval * 60;
                
        Craft::$app->getQueue()->delay($delayInSeconds)->push($job);
    }
}
