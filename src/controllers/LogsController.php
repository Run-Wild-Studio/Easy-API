<?php

namespace runwildstudio\easyapi\controllers;

use Craft;
use runwildstudio\easyapi\EasyApi;
use craft\web\Controller;
use yii\web\Response;

class LogsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @return Response
     * @throws \yii\base\Exception
     */
    public function actionLogs(): Response
    {
        $show = Craft::$app->getRequest()->getParam('show');
        $logEntries = EasyApi::$plugin->getLogs()->getLogEntries($show);

        // Limit to 300 for UI
        $logEntries = array_slice($logEntries, 0, 300);

        return $this->renderTemplate('easyapi/logs/index', [
            'show' => $show,
            'logEntries' => $logEntries,
        ]);
    }

    /**
     * @return Response
     */
    public function actionClear(): Response
    {
        EasyApi::$plugin->getLogs()->clear();

        return $this->redirect('easyapi/logs');
    }
}
