<?php

namespace runwildstudio\easyapi\queue\jobs;

use Craft;
use runwildstudio\easyapi\models\ApiModel;
use runwildstudio\easyapi\EasyApi;
use craft\elements\Entry;
use craft\queue\BaseJob;
use Throwable;
use yii\queue\RetryableJobInterface;

/**
 *
 * @property-read mixed $ttr
 */
class ApiImport extends BaseJob implements RetryableJobInterface
{
    // Properties
    // =========================================================================

    /**
     * @var ApiModel
     */
    public ApiModel $api;

    /**
     * @var int|null
     */
    public ?int $limit = null;

    /**
     * @var int|null
     */
    public ?int $offset = null;

    /**
     * @var array|null
     */
    public ?array $processedElementIds = null;

    /**
     * @var bool Whether to continue processing an api (and subsequent pages) if an error occurs
     * @since 4.3.0
     */
    public bool $continueOnError = true;

    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function getTtr()
    {
        return EasyApi::$plugin->getSettings()->queueTtr ?? EasyApi::getInstance()->queue->ttr;
    }

    /**
     * @inheritDoc
     */
    public function canRetry($attempt, $error): bool
    {
        $attempts = EasyApi::$plugin->getSettings()->queueMaxRetry ?? EasyApi::getInstance()->queue->attempts;
        return $attempt < $attempts;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue): void
    {
        try {
            if ($this->api->parentElementType != null && $this->api->parentElementType != "") {
                $sectionId = $this->api->parentElementGroup[$this->api->parentElementType]["section"];
                $entryTypeId = $this->api->parentElementGroup[$this->api->parentElementType]["entryType"];

                $entries = Entry::find()
                    ->siteId($this->api->siteId)
                    ->sectionId($sectionId)
                    ->typeId($entryTypeId)
                    ->all();

                $originalUrl = $this->api->apiUrl;

                foreach ($entries as $entry) {
                    // Access entry fields
                    $dynamicValue = $entry->getFieldValue($this->api->parentElementIdField); // Replace 'yourDynamicField' with the handle of your dynamic field

                    // Original string with placeholder
                    $originalString = $originalUrl;

                    // Replace the placeholder with the dynamic value
                    $modifiedString = str_replace('{{ Id }}', $dynamicValue, $originalString);
                    
                    $this->api->apiUrl = $modifiedString;
                    $this->processApi($queue);
                }
                $this->api->apiUrl = $originalUrl;
            } else {
                $this->processApi($queue);
            }
        } catch (Throwable $e) {
            // Even though we catch errors on each step of the loop, make sure to catch errors that can be anywhere
            // else in this function, just to be super-safe and not cause the queue job to die.
            EasyApi::error('`{e} - {f}: {l}`.', ['e' => $e->getMessage(), 'f' => basename($e->getFile()), 'l' => $e->getLine()]);
            Craft::$app->getErrorHandler()->logException($e);
        }
    }

    private function processApi($queue): void
    {
        $apiData = $this->api->getApiData();

        if ($this->offset) {
            $apiData = array_slice($apiData, $this->offset);
        }

        if ($this->limit) {
            $apiData = array_slice($apiData, 0, $this->limit);
        }

        // Do we even have any data to process?
        if (!$apiData) {
            EasyApi::info('No api items to process.');
            return;
        }

        $apiSettings = EasyApi::$plugin->process->beforeProcessApi($this->api, $apiData);

        $apiData = $apiSettings['apiData'];

        $totalSteps = count($apiData);

        $index = 0;

        foreach ($apiData as $data) {
            try {
                EasyApi::$plugin->process->processApi($index, $apiSettings, $this->processedElementIds);
            } catch (Throwable $e) {
                if (!$this->continueOnError) {
                    throw $e;
                }

                // We want to catch any issues in each iteration of the loop (and log them), but this allows the
                // rest of the api to continue processing.
                EasyApi::error('`{e} - {f}: {l}`.', ['e' => $e->getMessage(), 'f' => basename($e->getFile()), 'l' => $e->getLine()]);
                Craft::$app->getErrorHandler()->logException($e);
            }

            $this->setProgress($queue, $index++ / $totalSteps);
        }

        // Check if we need to paginate the api to run again
        if ($this->api->getNextPagination()) {
            EasyApi::getInstance()->queue->push(new self([
                'api' => $this->api,
                'limit' => $this->limit,
                'offset' => $this->offset,
                'processedElementIds' => $this->processedElementIds,
            ]));
        } else {
            // Only perform the afterProcessApi function after any/all pagination is done
            EasyApi::$plugin->process->afterProcessApi($apiSettings, $this->api, $this->processedElementIds);
        }
    }


    // Protected Methods
    // =========================================================================

    /**
     * @return string
     */
    protected function defaultDescription(): string
    {
        return Craft::t('easyapi', 'Running {name} api.', ['name' => $this->api->name]);
    }
}
