<?php

namespace runwildstudio\easyapi\helpers;

use Craft;
use craft\feedme\models\FeedModel;
use craft\feedme\services\Feeds as FeedService;
use craft\feedme\queue\jobs\FeedImport;
use craft\queue\BaseJob;
use runwildstudio\easyapi\EasyApi;
use runwildstudio\easyapi\console\controllers\ApisController;

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
                $feedService = new FeedService();
                $feed = $feedService->getFeedById($api->feedId);
                
                EasyApi::getInstance()->module->queue->push(new FeedImport([
                    'feed' => $feed,
                    'limit' => null,
                    'offset' => null,
                    'processedElementIds' => $processedElementIds
                ]));
            }
        }

        $job = new \runwildstudio\easyapi\helpers\JobQueueHelper([
            'description' => 'Easy API background process',
        ]);

        $delayInSeconds = $settings->jobQueueInterval * 60;
                
        Craft::$app->getQueue()->delay($delayInSeconds)->push($job);
    }
}
