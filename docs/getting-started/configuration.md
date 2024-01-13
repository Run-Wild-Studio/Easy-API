---
layout: page
title: Configuration
permalink: /getting-started/configuration/
---

# Configuration

Create an `easyapi.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

```php
<?php

return [
    '*' => [
        'pluginName' => 'Easy API',
        'jobQueueInterval' => 60,
        'cache' => 60,
        'skipUpdateFieldHandle' => 'skipEasyApiUpdate',
        'parseTwig' => false,
        'compareContent' => true,
        'sleepTime' => 0,
        'logging' => true,
        'runGcBeforeApi' => false,
        'queueTtr' => 300,
        'queueMaxRetry' => 5,
        'apiOptions' => [
            '1' => [
                'apiUrl' => 'https://specialurl.io/api.json'
            ]
        ],
    ]
];
```

### Configuration options

- `pluginName` - Optionally change the name of the plugin.
- `jobQueueInterval` - This is the interval between job queue runs in minutes for background processing.
- `cache` - For template calls, change the default cache time.
- `skipUpdateFieldHandle` - A provided field handle attached to your elements (often a Lightswitch or similar). If this field has a value during processing, Easy API will skip the element.
- `parseTwig` - Whether to parse field data and default values for Twig. Disabled by default.
- `compareContent` - Whether to check against existing element content before updating. This can have considerable performance improvements and prevent against needless updating.
- `sleepTime` - Add the number of seconds to sleep after each api item has been processed.
- `logging` - Set the level of logging to do. Possible values are `true` (default) to log everything, `false` to disable logging or `error` to only record errors.
- `runGcBeforeApi` - Whether to run the Garbage Collection service before running an api.
- `queueTtr` - Set the 'time to reserve' time in seconds, to prevent the job being cancelled after 300 seconds (default).
- `queueMaxRetry` - Set the maxiumum amount of retries the queue job should have before failing.
- `apiOptions` - Provide an array of any of the above options or [Api Settings](../feature-tour/api-overview.md) to set specifically for certain apis. Use the Api ID as the key for the array.

## Control Panel

You can also manage configuration settings through the Control Panel by visiting Settings → Easy API.