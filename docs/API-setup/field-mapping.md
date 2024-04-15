---
layout: page
title: Field Mapping
permalink: /setup/mapping
---

# Field Mapping

Now that you've setup the origins of your data, it's time to establish how individual items in the API correspond to new or existing elements in Craft.

The process follows a general pattern, albeit with variations based on your content model and the incoming data structure:

1. Locate the target field in the **Field** column.
1. Choose a source for the field's data from the menu in the **API Element** column of its respective row.
1. Customize options for the type of data being imported.
1. Optionally, set a static or dynamic default value.

Field mapping proceeds in the same fashion as detailed in the excellent documentation provided by FeedMe. Please refer to their documentation for further details on field mapping and mapping specific field types and plugin compatibility.

<div class="alert alert-primary">
There are limitations — the matching engine won't work for content stored in Matrix and other nested, Matrix-like fields such as Super Table and Neo, which can't be easily or reliably serialized.
</div>

<div class="alert alert-danger">
If data used as part of a unique identifier is altered between imports, Easy API may not match it again. This, when combined with the **Delete missing elements** import strategy, can lead to inadvertent data loss.
</div>

<div style="display: flex; justify-content: space-between">
<a href="/setup/primary-element">← Primary Element</a><a href="/setup/importing">Importing Your Content →</a>
</div>