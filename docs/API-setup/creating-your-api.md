---
layout: page
title: Creating your API
permalink: /setup/creating
---
# Creating your Api

Each field is fairly self-explanatory, but any additional information is provided below.

### Name

Setup a name for your api, so you can easily keep track of what you're importing.

### Target Site

If you have a multi-site Craft installation, you'll have an additional “Target Site” setting where you can select which site the elements should be initially saved in. The content will get propagated to your other sites from there, according to your fields’ Translation Method settings.

### Api URL

Provide the URL for your api. This can be an absolute URL, relative (to the web root) and make use of any [aliases](https://docs.runwildstudio.co.nz/v3/config/#aliases). If this API is fetching data relative to another entity insert {{ Id }} in the URL and select Parent Element Type and Parent Element Id Field to dynamically update the URL per parent entity record.

### Parent Element Type

Select the [element type](../mapping/element-types.md) you wish to use when fetching data from an API which is related to another entity.

### Parent Element Id Field

Select the field that is used from the parent element when you are using a parent element to determine the API URL.

### Content Type

Set the Content Type to match the type of data you're importing. Your options are:

- JSON
- XML

### Authorization

Enter the authorization value for the API call.

### Element Type

Select the [element type](../content-mapping/element-types.md) you wish to import your api content into.

### Use API from front end

Tick this checkbox when you want to use the Easy API entry from the front end of your site to get live calls.

### Use Job Queue

Tick this checkbox if you want to use Craft's job queue to continuously fetch data from the API to update your site.

### Job Queue Process Order

When using job queue enter an integer to determine the sequence of Easy API entries that are processed (1 is processed first).

### Import Strategy

The **Import Strategy** tells Easy API how to act when (or if) it comes across elements that are similar to what you’re importing. If you’ve imported your content once, there will very likely be elements with the same title or content as what you're trying to import.

::: tip
The actual matching behavior is determined by a [unique identifier](field-mapping.md#unique-identifiers), which you’ll configure in a moment.
:::

For example: you have an existing entry called “About Us,” but you also have an item in your api with exactly the same title. You should tell Easy API what to do when it comes to processing this entry in your api. Do you want to update that same entry, or add a new one?

You can select from any combination of the following:

Attribute | Description
--- | ---
**Create new elements** | Adds new elements if they do not already exist (as determined by a _unique identifier_). If an element _does_ exist, it will only be updated if **Update existing elements** is enabled.
**Update existing elements** | Updates elements that match the _unique identifier_. If no existing element matches, one will be only be created if **Create new elements** is also enabled.
**Disable missing elements** | Disables elements that are not updated by this api.
**Disable missing elements in the target site** | Disables elements that are not updated by this api, but only in the api’s [target site](#target-site).
**Delete missing elements** | Deletes elements that are not updated by this api. **Be careful when deleting**.
**Update search indexes** | Whether search indexes should be updated.

### Passkey

A generated, unique string to increase security against imports being run inadvertently. This is mainly used when triggering an import via the direct api link.

### Backup

Enable a backup of your database to be taken on each import. Please note the [performance implications](../troubleshooting.md#performance) when switching this on.

### Set Empty Values

When enabled, empty values in an api item are considered valid and will clear the corresponding fields when your [Import Strategy](#import-strategy) includes _update existing elements_. When disabled, empty values are ignored or treated as unchanged.

Keys omitted from an api item are not considered “empty” and will not clear values on existing entries.

* * *

Click **Save & Continue** to be taken to the [Primary Element](primary-element.md) screen, or **Save** to continue making changes on this screen.
