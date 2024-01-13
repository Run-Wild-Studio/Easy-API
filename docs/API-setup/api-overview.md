---
layout: page
title: Overview
permalink: /setup/overview
---
# Api Overview

Go to the main Easy API section via your CP sidebar menu. You'll be presented with a table listing of your saved apis, or none if you haven't set any up yet.

This overview shows the following:

- **Name** - Name your api something useful so you'll remember what it does.
- **Api URL** The URL to the external api.
- **Type** - The data type you're importing.
- **Element Type** - The element type you are importing into.
- **Element Group** - Depending on the element type chosen. Entries will show Section/Entry Type, Categories will show Group, etc.
- **Parent Element Type** - The element type you are linking the imported data to.
- **Parent Element Group** - Depending on the parent element type chosen. Entries will show Section/Entry Type, Categories will show Group, etc.
- **Strategy** - What [import strategy](creating-your-api.md#import-strategy) you have chosen: how you'd like to handle duplicate api items (if you're going to be re-running this api).
- **Run Api** - Runs the api immediately
- **Settings (icon)** - Additional settings pane (see below).
- **Delete (icon)** - Delete this api (be careful).

### Settings Pane
Clicking on the settings cog icon will expand additional settings for each api.
- **Debug Api** - Opens in a new window and runs the [Debug action](../troubleshooting.md#debugging).
- **Api Status** - Takes you to an overview screen showing the process of your api if currently running.
- **Duplicate Api** - Duplicates this api and all settings into a new api.
- **Direct Api URL** - Can be used in your [Cron job setup](trigger-import-via-cron.md) to directly trigger the job.

Create a new api by pressing the red _\+ New Api_ button in the top-right, or click on the Name column in your table. You'll then be taken to [Create your Api →](creating-your-api.md).
