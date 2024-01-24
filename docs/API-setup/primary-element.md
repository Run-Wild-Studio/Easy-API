---
layout: page
title: Primary element
permalink: /setup/primary-element
---
# Primary Element

Understanding the primary element may initially be perplexing, but it is crucial to ensure that Easy API accurately identifies the content in your API. Consider the following example in both XML and JSON formats:

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="element-xml-tab" data-bs-toggle="tab" data-bs-target="#element-xml" type="button" role="tab" aria-controls="element-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="element-json-tab" data-bs-toggle="tab" data-bs-target="#element-json" type="button" role="tab" aria-controls="element-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="element-xml" role="tabpanel" aria-labelledby="element-xml-tab">
      <pre>
        &lt;?xml version="1.0" encoding="UTF-8"?&gt;
            &lt;rss&gt;
                &lt;channel&gt;
                    &lt;item&gt;
                        &lt;title&gt;My Title&lt;/title&gt;
                        &lt;slug&gt;my-title&lt;/slug&gt;
                    &lt;/item&gt;
                    &lt;item&gt;
                        &lt;title&gt;Another Title&lt;/title&gt;
                        &lt;slug&gt;another-title&lt;/slug&gt;
                    &lt;/item&gt;
                &lt;/channel&gt;
            &lt;/rss&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="element-json" role="tabpanel" aria-labelledby="element-json-tab">
      <pre>
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
      </pre>
    </div>
  </div>
</div>

In this case, your Primary Element would be `item`. This repeatable node is typically one level above the content you wish to import (such as `title` and `slug`). In the JSON example, it is a plain array, but the same principle applies.

As a helpful indicator, Easy API displays the number of elements on each node, providing a clue as to which node to select as the primary element. Given the diverse nature of APIs, this step aims to standardize them for effective processing by Easy API.

## Pagination URL

Certain APIs paginate their content for performance reasons. Instead of handling a massive API with 600 items, it is divided into 6 APIs with 100 items each. In such cases, your API should include the full URL to the next collection of items for Easy API to fetch.

Use this option to designate the node in your API containing the full URL to the subsequent collection of items. Easy API will automatically initiate a new queue job to process this new set of data after completing the first page.

<div class="alert alert-primary">
    Note that your pagination URL should not be nested within an array or numerically-indexed key in your API.
</div>

Press **Save & Continue** to be taken to the **Field Mapping** screen, or press **Save** to continue editing this screen.

<div style="display: flex; justify-content: space-between">
<a href="/setup/creating">← Creating Your API Feed</a><a href="/setup/mapping">Field Mapping →</a>
</div>