---
layout: page
title: Overview
permalink: /setup/overview
---
# API Overview

Go to the main Easy API section via your CP sidebar menu. You'll be presented with a table listing of your saved APIs, or none if you haven't set any up yet. Setup is very similar to a feed in FeedMe, with the addition of being able to select parent elements and parent element groups.

This overview shows the following (field handle for configuration in brackets):

- **Name** (`name`) - Name your API something useful so you'll remember what it does.
- **API URL** (`APIUrl`) The URL to the external API.
- **Type** (`contentType`) - The data type you're importing.
- **Parent Element Type** (`parentElementType`) - The element type you are linking the imported data to.
- **Parent Element Group** (`parentElementGroup`) - Depending on the parent element type chosen. Entries will show Section/Entry Type, Categories will show Group, etc.
- **Element Type** (`elementType`) - The element type you are importing into.
- **Element Group** (`elementGroup`) - Depending on the element type chosen. Entries will show Section/Entry Type, Categories will show Group, etc.
- **Strategy** (`duplicateHandle`) - What import strategy you have chosen: how you'd like to handle duplicate API items (if you're going to be re-running this API).
- **Run API** - Runs the API immediately
- **Settings (icon)** - Additional settings pane (see below).
- **Delete (icon)** - Delete this API (be careful).

### Settings Panel
Clicking on the settings cog icon will expand additional settings for each API.
- **Debug API** - Opens in a new window and runs the Debug action.
- **API Status** - Takes you to an overview screen showing the process of your API if currently running.
- **Duplicate API** - Duplicates this API and all settings into a new API.

Create a new API by pressing the red _\+ New API_ button in the top-right, or click on the Name column in your table. You'll then be taken to "Create your API".

<div style="display: flex; justify-content: space-between">
<a href="/getting-started/configuration">← Configuration</a><a href="/setup/creating">Creating Your API Feed →</a>
</div>