# Events

Events can be used to extend the functionality of Easy API.

## Apis related events

### The `beforeSaveApi` event

Plugins can get notified before an api has been saved (through the control panel).

```php
use runwildstudio\easyapi\events\ApiEvent;
use runwildstudio\easyapi\services\Apis;
use yii\base\Event;

Event::on(Apis::class, Apis::EVENT_BEFORE_SAVE_API, function(ApiEvent $event) {

});
```

### The `afterSaveApi` event

Plugins can get notified after an api has been saved (through the control panel).

```php
use runwildstudio\easyapi\events\ApiEvent;
use runwildstudio\easyapi\services\Apis;
use yii\base\Event;

Event::on(Apis::class, Apis::EVENT_AFTER_SAVE_API, function(ApiEvent $event) {

});
```


## Data Fetching related events

### The `beforeFetchApi` event

Plugins can get notified before an api's data has been fetched. You can also return with a response to bypass Easy API's default fetching.

```php
use runwildstudio\easyapi\events\ApiDataEvent;
use runwildstudio\easyapi\services\DataTypes;
use yii\base\Event;

Event::on(DataTypes::class, DataTypes::EVENT_BEFORE_FETCH_API, function(ApiDataEvent $event) {
    // This will set the api's data
    $event->response = [
        'success' => true,
        'data' => '<?xml version="1.0" encoding="UTF-8"?><entries><entry><title>Some Title</title></entry></entries>',
    ];
});
```

### The `afterFetchApi` event

Plugins can get notified after an api's data has been fetched. Note the api data hasn't been parsed at this point.

```php
use runwildstudio\easyapi\events\ApiDataEvent;
use runwildstudio\easyapi\services\DataTypes;
use yii\base\Event;

Event::on(DataTypes::class, DataTypes::EVENT_AFTER_FETCH_API, function(ApiDataEvent $event) {

});
```

### The `afterParseApi` event

Plugins can get notified after an api's data has been fetched and parsed into an array.

```php
use runwildstudio\easyapi\events\ApiDataEvent;
use runwildstudio\easyapi\services\DataTypes;
use yii\base\Event;

Event::on(DataTypes::class, DataTypes::EVENT_AFTER_PARSE_API, function(ApiDataEvent $event) {

});
```


## Api Processing related events

### The `beforeProcessApi` event

Plugins can get notified before the api processing has started.

```php
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\services\Process;
use yii\base\Event;

Event::on(Process::class, Process::EVENT_BEFORE_PROCESS_API, function(ApiProcessEvent $event) {

});
```

### The `afterProcessApi` event

Plugins can get notified after the api processing has completed (all items are done).

```php
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\services\Process;
use yii\base\Event;

Event::on(Process::class, Process::EVENT_AFTER_PROCESS_API, function(ApiProcessEvent $event) {

});
```

### The `stepBeforeElementMatch` event

Triggered for each api item, plugins can get notified before existing elements are tried to be matched.

```php
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\services\Process;
use yii\base\Event;

Event::on(Process::class, Process::EVENT_STEP_BEFORE_ELEMENT_MATCH, function(ApiProcessEvent $event) {

});
```

### The `stepBeforeElementSave` event

Triggered for each api item, plugins can get notified before the prepared element is about to be saved.

```php
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\services\Process;
use yii\base\Event;

Event::on(Process::class, Process::EVENT_STEP_BEFORE_ELEMENT_SAVE, function(ApiProcessEvent $event) {

});
```

### The `stepAfterElementSave` event

Triggered for each api item, plugins can get notified after the prepared element has been saved.

```php
use runwildstudio\easyapi\events\ApiProcessEvent;
use runwildstudio\easyapi\services\Process;
use yii\base\Event;

Event::on(Process::class, Process::EVENT_STEP_AFTER_ELEMENT_SAVE, function(ApiProcessEvent $event) {

});
```

## Field parsing related events

### The `beforeParseField` event

Triggered before a field value is parsed. Plugins can get notified before a field value is parsed.

```php
use runwildstudio\easyapi\events\FieldEvent;
use runwildstudio\easyapi\services\Fields;
use yii\base\Event;

Event::on(Fields::class, Fields::EVENT_BEFORE_PARSE_FIELD, function(FieldEvent $event) {

});
```

### The `afterParseField` event

Triggered after a field value is parsed. Plugins can get notified before a field value is parsed and alter the parsed value. 

```php
use runwildstudio\easyapi\events\FieldEvent;
use runwildstudio\easyapi\services\Fields;
use yii\base\Event;

Event::on(Fields::class, Fields::EVENT_AFTER_PARSE_FIELD, function(FieldEvent $event) {

});
```