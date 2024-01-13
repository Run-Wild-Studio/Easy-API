# About Easy API

Easy API is a Craft plugin for super-simple importing of content, either once-off, at regular intervals or from a front end call. With support for XML or JSON apis, you'll be able to import your content as Entries, Categories, Craft Commerce Products (and variants), and more.

## Features

- Import data from XML or JSON apis.
- Built-in importers for [several element types](content-mapping/element-types.md), plus an importer API. 
- Apis are saved to allow easy re-processing on-demand, or to be used in a Cron job.
- Simple field-mapping interface to match your api data with your element fields.
- Duplication handling - control what happens when apis are processed again.
- Uses Craft's Queue service to process apis in the background and schedule recurring processing.
- Troubleshoot api processing issues with logs.
- Grab api data directly from your twig templates. Test
