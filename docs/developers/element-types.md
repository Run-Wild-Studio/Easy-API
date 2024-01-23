# Element Types

### The `registerEasyApiElements` event
Plugins can register their own elements.

```php
use runwildstudio\easyapi\events\RegisterEasyApiElementsEvent;
use runwildstudio\easyapi\services\Elements;
use yii\base\Event;

Event::on(Elements::class, Elements::EVENT_REGISTER_EASY_API_ELEMENTS, function(RegisterEasyApiElementsEvent $e) {
    $e->elements[] = MyElement::class;
});
```

### The `beforeParseAttribute` event
Plugins can get notified before a element's attribute has been parsed.

```php
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\events\ElementEvent;
use yii\base\Event;

Event::on(Element::class, Element::EVENT_BEFORE_PARSE_ATTRIBUTE, function(ElementEvent $e) {

});
```

### The `parseAttribute` event
Plugins can get notified after a element's attribute has been parsed.

```php
use runwildstudio\easyapi\base\Element;
use runwildstudio\easyapi\events\ElementEvent;
use yii\base\Event;

Event::on(Element::class, Element::EVENT_AFTER_PARSE_ATTRIBUTE, function(ElementEvent $e) {
    $parsedValue = $e->parsedValue;
});
```
