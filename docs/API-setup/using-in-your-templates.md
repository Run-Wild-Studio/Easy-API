---
layout: page
title: Using in your templates
permalink: /setup/templates
---
# Using in your Templates

To capture API data on-demand without saving it as an entry, you can utilize Twig templates with the following code. APIs are cached for performance (default to 60 seconds), and you can adjust this duration through a tag parameter or in the plugin settings.

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

- `url` (string, required) - URL to the API.
- `type` (string, optional) - The type of API you're fetching data from. Valid options are json or xml (defaults to xml).
- `element` (string, optional) - Element to start API from. Useful for deep APIs.
- `cache` (bool or number, optional) - Whether or not to cache the request. If true, it will use the default duration set in the plugin settings. If a number is provided, it will use that as the cache duration. Setting to false will disable caching completely.

### Example template code

<pre>
&lt;?xml version="1.0" encoding="UTF-8" ?&gt;
&lt;entries&gt;
    &lt;entry&gt;
        &lt;title&gt;Monday&lt;/title&gt;
        &lt;item&gt;
            &lt;title format="html"&gt;Event 1&lt;/title&gt;
            &lt;type&gt;All-day&lt;/type&gt;
        &lt;/item&gt;
    &lt;/entry&gt;
    &lt;entry&gt;
        &lt;title&gt;Tuesday&lt;/title&gt;
        &lt;item&gt;
            &lt;title format="html"&gt;Event 2&lt;/title&gt;
            &lt;type&gt;Half-day&lt;/type&gt;
        &lt;/item&gt;
    &lt;/entry&gt;
&lt;/entries&gt;
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

<div class="alert alert-primary">
There's a special case for XML-based apis, which is illustrated above when attributes are present on a node. To retrieve the node value, use <code>['@']</code>, and to fetch the attribute value, use <code>['@attribute_name']</code>.
</div>

<div style="display: flex; justify-content: space-between">
<a href="/setup/importing">← Importing Your Content</a>
</div>