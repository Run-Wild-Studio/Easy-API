---
layout: page
title: Using in your templates
permalink: /setup/templates
---
# Using in your Templates

While you can create an api queue job to insert data as elements, there are times which you may prefer to capture api data on-demand, rather than saving as an entry. You can easily do this through your twig templates using the below.

Apis are cached for performance (default to 60 seconds), which can be set by a tag parameter, or in the plugin settings.

<pre>
&#123;% set params = &#123;
    url: 'http://path.to/api/',
    type: 'xml',
    element: 'item',
    cache: 60,
&#125; %&#125;

&#123;% set api = craft.easyApi.api(params) %&#125;

&#123;% for node in api %&#125;
    &#123;# Your template code goes here #&#125;
&#123;% endfor %&#125;
</pre>

#### Parameters

- `url` (string, required) - URL to the api.
- `type` (string, optional) - The type of api you're fetching data from. Valid options are json or xml (defaults to xml).
- `element` (string, optional) - Element to start api from. Useful for deep apis.
- `cache` (bool or number, optional) - Whether or not to cache the request. If true, will use the default as set in the plugin settings, or if a number, will use that as its duration. Setting to false will disable cache completely.

### Example template code

<pre>
<?xml version="1.0" encoding="UTF-8" ?>
<entries>
    <entry>
        <title>Monday</title>
        <item>
            <title format="html">Event 1</title>
            <type>All-day</type>
        </item>
    </entry>
    
    <entry>
        <title>Tuesday</title>
        <item>
            <title format="html">Event 2</title>
            <type>Half-day</type>
        </item>
    </entry>
</entries>
</pre>

With the above example XML, we would use the following Twig code to loop through each `entry` to extract its data.

<pre>
&#123;% set params = &#123;
    url: 'http://path.to/api/',
    type: 'xml',
    element: 'entry',
    cache: 60,
&#125; %&#125;

&#123;% set api = craft.easyApi.api(params) %&#125;

&#123;% for node in api %&#125;
    Title: &#123;&#123; node.title &#125;&#125;
    Item: &#123;&#123; node.item.title['@'] &#125;&#125;
    Item Format: &#123;&#123; node.item.title['@format'] &#125;&#125;
    Type: &#123;&#123; node.item.type &#125;&#125;
&#123;% endfor %&#125;

&#123;# Producing the following output #&#125;
Title: Monday
Item: Event 1
Item Format: html
Type: All-day

Title: Tuesday
Item: Event 2
Item Format: html
Type: Half-day
</pre>

:::tip
There's a special case for XML-based apis, which is illustrated above when attributes are present on a node. To retrieve the node value, use `['@']`, and to fetch the attribute value, use `['@attribute_name']`.
:::