# About Easy API

Easy API is a Craft plugin for super-simple importing of content, either once-off, at regular intervals or live through a Twig variable. With support for XML or JSON apis, you'll be able to import your content as Entries, Categories, Craft Commerce Products (and variants), and more.

## Features

- Import data from XML or JSON APIs.
- Built-in importers for [several element types](content-mapping/element-types.md), plus an importer API. 
- APIs are saved to allow easy re-processing on-demand, or to be used in a Cron job.
- Simple field-mapping interface to match your API data with your element fields.
- Duplication handling - control what happens when APIs are processed again.
- Uses Craft's Queue service to process APIs in the background and schedule recurring processing.
- Troubleshoot API processing issues with logs.
- View API data directly into your templates for templating live pulls.