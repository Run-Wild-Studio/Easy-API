# Data Types

### The `registerEasyApiDataTypes` event
Plugins can register their own data types.

```php
use runwildstudio\easyapi\events\RegisterEasyApiDataTypesEvent;
use runwildstudio\easyapi\services\DataTypes;
use yii\base\Event;

Event::on(DataTypes::class, DataTypes::EVENT_REGISTER_EASY_API_DATA_TYPES, function(RegisterEasyApiDataTypesEvent $e) {
    $e->dataTypes[] = MyDataType::class;
});
```
