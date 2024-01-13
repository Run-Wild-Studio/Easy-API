<?php

namespace runwildstudio\easyapi\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $pluginName = 'Easy API';

    /**
     * @var int
     */
    public int $jobQueueInterval = 60;

    /**
     * @var int
     */
    public int $cache = 60;

    /**
     * @var string|int|array
     */
    public string|int|array $enabledTabs = '*';

    /**
     * @var array
     */
    public array $clientOptions = [];

    /**
     * @var bool
     */
    public bool $compareContent = true;

    /**
     * @var string
     */
    public string $skipUpdateFieldHandle = '';

    /**
     * @var bool
     */
    public bool $parseTwig = false;

    /**
     * @var array
     */
    public array $apiOptions = [];

    /**
     * @var int
     */
    public int $sleepTime = 0;

    /**
     * @var bool|string
     */
    public bool|string $logging = true;

    /**
     * @var bool
     */
    public bool $runGcBeforeApi = false;

    /**
     * @var int|null
     */
    public ?int $queueTtr = null;

    /**
     * @var int|null
     */
    public ?int $queueMaxRetry = null;
}
