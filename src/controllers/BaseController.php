<?php

namespace runwildstudio\easyapi\controllers;

use Craft;
use runwildstudio\easyapi\EasyApi;
use craft\web\Controller;
use yii\db\Exception;
use yii\web\Response;

class BaseController extends Controller
{
    /**
     * @var string[]
     */
    protected int|bool|array $allowAnonymous = ['actionClearTasks'];

    // Public Methods
    // =========================================================================

    /**
     * @return Response
     */
    public function actionSettings(): Response
    {
        $settings = EasyApi::$plugin->getSettings();

        return $this->renderTemplate('easyapi/settings/general', [
            'settings' => $settings,
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionClearTasks(): Response
    {
        $settings = EasyApi::$plugin->getSettings();
        
        // Function to clear (delete) all stuck tasks.
        Craft::$app->getDb()->createCommand()
            ->delete('{{%queue}}')
            ->execute();

        return $this->renderTemplate('easyapi/settings/general', [
            'settings' => $settings,
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionStartJobQueue(): Response
    {
        $settings = EasyApi::$plugin->getSettings();

        $job = new \runwildstudio\easyapi\helpers\JobQueueHelper([
            'description' => 'API Integration background process',
        ]);

        $delayInSeconds = $settings->jobQueueInterval * 60;
        
        Craft::$app->getQueue()->push($job);

        return $this->renderTemplate('easyapi/settings/general', [
            'settings' => $settings,
        ]);
    }
}
