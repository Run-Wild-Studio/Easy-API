---
layout: page
title: Creating your API Feed
permalink: /setup/creating
---
# Creating your API Feed

Setting up your API feed involves configuring various fields. Although each field is quite self-explanatory, additional details are provided below:

### Name

Set a name for your API feed to easily track your imports.

### Target Site

For multi-site Craft installations, choose a **Target Site** where elements will be initially saved. Content will then propagate to other sites based on **Translation Method** settings.

### API URL

Specify the API URL. It can be absolute, relative (to the web root), and may utilize aliases. For dynamic URL updates per parent entity record, insert the URL relative to another entity and select Parent Element Type and Parent Element Id Field.

### Parent Element Type

Select the [element type](/mapping/element-types) when fetching data from an API related to another entity.

### Parent Element Id Field

Choose the field used from the parent element to determine the API URL.

### Content Type

Set the Content Type to match the data type being imported. Your options are:

- JSON
- XML

### Authorization

Enter the authorization value for the API call.

### Element Type

Specify the [element type](/mapping/element-types) where you want to import your API content.

### Use API from front end

Check this box to enable Easy API entry from the front end for live calls. This will disable the various content importing fields and setup such as the job queue and element types.

### Use Job Queue

Enable Craft’s job queue if you want continuous data fetching from the API to update your site.

### Job Queue Process Order

Enter an integer to determine the processing sequence of Easy API entries when using job queue. Jobs are processed in ascending order, i.e. 1 will be processed, then 2 etc.

### Import Strategy

Define how Easy API should handle similar elements during import. Options include creating new elements, updating existing ones, disabling missing elements, and more.

<div class="alert alert-primary">
  Matching behavior is determined by a <a href="/setup/mapping#unique-identifiers">unique identifier</a>, configured later.
</div>

You can select from any combination of the following (handles for configuration in brackets):

- **Create new elements** (`add`) - Adds new elements if they do not already exist (as determined by a _unique identifier_). If an element _does_ exist, it will only be updated if **Update existing elements** is enabled.
- **Update existing elements** (`update`) - Updates elements that match the _unique identifier_. If no existing element matches, one will be only be created if **Create new elements** is also enabled.
- **Disable missing elements** (`disable`) - Disables elements that are not updated by this API.
- **Disable missing elements in the target site** (`disableForSite`) - Disables elements that are not updated by this API, but only in the API’s target site.
- **Delete missing elements** (`delete`) - Deletes elements that are not updated by this API. **Be careful when deleting**.
- **Update search indexes** (`updateSearchIndexes`) - Whether search indexes should be updated.

Setting the import strategy in a configuration file requires a nested array in the following format:
```php
<?php
  'apiOptions' => [
      '1' => [
          'duplicateHandle' => ['add', 'update', 'delete'],
          'updateSearchIndexes' => true,
      ]
  ]
```

### Passkey

A unique, generated string for increased import security when triggered via the direct API link.

### Backup

Enable database backup on each import, considering performance implications.

### Set Empty Values

When enabled, empty values in an API item are considered valid and clear corresponding fields when updating existing elements in your Import Strategy.

Keys omitted from an API item are not considered "empty" and won't clear values on existing entries.

Click **Save & Continue** to proceed to the Primary Element screen, or simply **Save** to continue making changes on this screen.

<div style="display: flex; justify-content: space-between">
<a href="/setup/overview">← Feed Setup</a><a href="/setup/primary-element">Primary Element →</a>
</div>