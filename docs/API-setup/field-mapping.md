---
layout: page
title: Field Mapping
permalink: /setup/mapping
---

# Field Mapping

Now that you've informed Easy API about the origin of your data, it's time to establish how individual items in the API correspond to new or existing elements in Craft.

The process follows a general pattern, albeit with variations based on your content model and the incoming data structure:

1. Locate the target field in the **Field** column.
1. Choose a source for the field's data from the menu in the **API Element** column of its respective row.
1. Customize options for the type of data being imported.
1. Optionally, set a static or dynamic default value.

## Native Fields

The native fields available for mapping depend on the selected target element type. For example, entries support fields like **Title**, **Slug**, **Parent**, **Post Date**, **Expiry Date**, **Status**, and **Author**, in addition to custom fields attached via the field layout.

<div class="alert alert-primary">
Native fields share similar options with custom fields, and this section will highlight the unique ones.
</div>

### Element IDs

Mapping data in your API to the element ID is useful when certain about which element you want a given API item to update. However, exercise caution and avoid using this for importing new data, as IDs from other systems may not match Craft's.

Recommended scenarios for setting IDs include re-importing modified data exported from Craft or importing/synchronizing data from external systems that already track Craft element IDs. In most cases, matching based on a different unique identifier is preferable.

<div class="alert alert-danger">
<p><strong>Do not use this when importing “new” data!</strong></p>

<p>Content from another system (ExpressionEngine, WordPress, etc.) will _not_ have the same IDs as their corresponding elements in Craft, by virtue of how records are created in the database. If you specify the _wrong_ element ID (deliberately or coincidentally), you run the risk of updating completely unrelated content (i.e. an asset when you meant to update an entry).</p>

<p>There are only two situations in which setting IDs is recommended:</p>
<ul>
  <li>When re-importing data that was exported from Craft, then modified;</li>
  <li>Importing or synchronizing data from external systems that already track Craft element IDs;</li>
</ul>

<p>In most cases, incoming data should be matched based on a different unique identifier.</p>
</div>

## Custom Fields

Like native fields, configuration options for each custom field type depend on the data it stores.

### Scalar Data

Basic data types like text, numbers, booleans, and others require no additional configuration.

### Dates

Easy API can parse [most date formats](https://www.php.net/manual/en/function.strtotime.php). Specify a specific pattern to handle ambiguous date formats.

### Relational Fields

Define how Easy API should locate referenced elements in assets, categories, entries, tags, or users fields.

#### Nested Fields

When importing relational data, map values onto nested fields. Note that nested field values apply uniformly to all relations. Enable **Element fields (x)** to expand controls for those nested fields.

<div class="alert alert-primary">
Keep in mind that nested field values will be applied uniformly to all relations.
</div>

### Matrix

See the [Importing into Matrix](../mapping/fields#matrix) guide to learn more about this special field type.

### Plugin Fields

Easy API supports various plugin-provided field types, including:

Field Type | Developer
--- | ---
[Calendar Events](https://plugins.craftcms.com/calendar) | Solspace
[Commerce Products](https://plugins.craftcms.com/commerce) | Pixel & Tonic
[Commerce Variants](https://plugins.craftcms.com/commerce) | Pixel & Tonic
[Entries Subset](https://plugins.craftcms.com/entriessubset) | Nathaniel Hammond
[Google Maps](https://plugins.craftcms.com/google-maps) | Double Secret Agency
[Linkit](https://plugins.craftcms.com/linkit) | Pressed Digital
[Simplemap](https://plugins.craftcms.com/simplemap) | Ether Creative
[Super Table](https://plugins.craftcms.com/supertable) | Verbb
[Typed Link](https://plugins.craftcms.com/typedlinkfield) | Sebastian Lenz

 
<div class="alert alert-primary">
Other fields that store simple text values (like Redactor) will work automatically.
</div>

## Default Values

When the source for a native or custom field is set to "Use default value," you can provide a value in the third column to override any default value defined by the field itself. If `parseTwig` is enabled, textual fields are treated as Twig "object templates" with access to other fields on the imported element.

## Unique Identifiers

Selecting a **Unique Identifier** is crucial for your API to align with the chosen **Import Strategy**. This identifier, used when comparing against existing entries, includes native fields (title, slug, status, or ID) and custom field values.

<div class="alert alert-primary">
There are limitations — the matching engine won't work for content stored in Matrix and other nested, Matrix-like fields such as Super Table and Neo, which can't be easily or reliably serialized.
</div>

<div class="alert alert-danger">
If data used as part of a unique identifier is altered between imports, Easy API may not match it again. This, when combined with the **Delete missing elements** import strategy, can lead to inadvertent data loss.
</div>

<div style="display: flex; justify-content: space-between">
<a href="/setup/primary-element">← Primary Element</a><a href="/setup/importing">Importing Your Content →</a>
</div>