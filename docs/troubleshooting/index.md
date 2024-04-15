---
layout: page
title: Trobleshooting
permalink: /troubleshooting/
---

# Troubleshooting Tips

### Performance

If you're encountering slow processing for your API, try the following:

- **Turn off `devMode`** - Craft's built-in logging in devMode significantly slows down the import process and causes high memory overhead.
- **Disable the debug toolbar** - Ensure it is turned off under your user account preferences during import, as it can lead to high memory overhead and other resource-related issues.
- **CompareContent setting** - Consider turning on the `compareContent configuration setting to prevent unnecessary content overwriting (defaulted to true).
- **Duplication handling** - Depending on your requirements, select the "Add Entries" option for duplication handling.
- **Opt for a JSON API** - JSON APIs generally have significantly less processing overhead compared to XML.
- **Adjust PHP settings** - You may need to modify the `memory_limit` and `max_execution_time` values in your php.ini file if you encounter memory issues.

### Unexpected Results

If you're experiencing unexpected results during an import, isolate the issue by selectively mapping fields. Start with a bare-minimum import and gradually add mapped fields until you encounter issues.

For example, if mapping 20+ fields for an Entry import isn't working, try mapping just the Title field and progressively add more fields.

### Logging

Easy API logs events for nearly every action, including errors and status information. If you face issues or unexpected results, consult the Logs tab for insights. If any logs appear unclear or you are unable to find the problem, please feel free to contact us through our website.

### Debugging

Easy API includes a dedicated view to assist with debugging your API in case of issues or errors during an import. With devMode enabled, click the "gear" in the problematic API's row to expand its utility drawer, then click Debug.

The debug output combines print_r-formatted objects and log messages, offering comprehensive information about your API settings, field mappings, and data. If exceptions occur during API processing, they'll be visible on this page.

<div class="alert alert-danger">
Debugging an API attempts to actually run the import, so make sure you have a backup, or are working in a disposable environment and have enough cap in your API call limit!
</div>
