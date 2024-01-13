---
layout: page
title: Primary element
permalink: /setup/primary-element
---
# Primary Element

The primary element can be confusing at first, but its vitally important to ensure Easy API can hone in on the content in your api correctly.

Take the following example:

::: code
```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss>
    <channel>
        <item>
            <title>My Title</title>
            <slug>my-title</slug>
        </item>

        <item>
            <title>Another Title</title>
            <slug>another-title</slug>
        </item>
    </channel>
</rss>
```

```json
{
    "channel": {
        "item": [
            {
                "title": "My Title",
                "slug": "my-title"
            },
            {
                "title": "Another Title",
                "slug": "another-title"
            }
        ]
    }
}
```
:::

Your Primary Element would be `item`. This is the node that's repeatable, and you can usually pick it as it'll be the node thats one level above the content you want to import (`title` and `slug` in this case). In the JSON example, you'll see its a plain array, but the same applies.

As a helper, Easy API will show you how many elements on each node, which will give you a clue as to which node you want to select as the primary element. As each api can be vastly different, this step seeks to normalise them for Easy API to effectively process.

## Pagination URL

Some apis paginate their content due to their sheer size. This is also a good idea for performance, where instead of a massive api to update 600 items, there are 6 apis with 100 items each. In this instance, your api should contain the full URL to the next collection of items for Easy API to fetch.

Use this option to select the node in your api that contains the full URL to the next collection of items. Easy API will automatically spawn a new queue job to process this new set of data after the first page has finished.

::: warning
Your pagination URL can’t be nested within an array or numerically-indexed key in your api.
:::

Press _Save & Continue_ to be taken to the [Field Mapping](field-mapping.md) screen, or press _Save_ to continue editing this screen.
