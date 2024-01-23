# Field Types

### The `registerEasyApiFields` event
Plugins can register their own field.

```php
use runwildstudio\easyapi\events\RegisterEasyApiFieldsEvent;
use runwildstudio\easyapi\services\Fields;
use yii\base\Event;

Event::on(Fields::class, Fields::EVENT_REGISTER_EASY_API_FIELDS, function(RegisterEasyApiFieldsEvent $e) {
    $e->fields[] = DataType::class;
});
```

### The `beforeParseField` event
Plugins can get notified before a field's data has been parsed.

```php
use runwildstudio\easyapi\events\FieldEvent;
use runwildstudio\easyapi\services\Fields;
use yii\base\Event;

Event::on(Fields::class, Fields::EVENT_BEFORE_PARSE_FIELD, function(FieldEvent $e) {

});
```

### The `afterParseField` event
Plugins can get notified after a field's data has been parsed.

```php
use runwildstudio\easyapi\events\FieldEvent;
use runwildstudio\easyapi\services\Fields;
use yii\base\Event;

Event::on(Fields::class, Fields::EVENT_AFTER_PARSE_FIELD, function(FieldEvent $e) {
    $parsedValue = $e->parsedValue;
});
```
