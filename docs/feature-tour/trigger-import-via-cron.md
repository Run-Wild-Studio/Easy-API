# Trigger Import via Cron

:::tip
Triggering an import via Cron still uses Craft's Queue system, so it won't affect the performance of your site.
:::

Once your api is configured properly, you can trigger the api processing directly using a special URL. Find this URL by copying the Direct Api Link from the [Api Overview](api-overview.md) screen. You'll receive a URL similar to:

```
http://your.domain/actions/easyapi/apis/run-task?direct=1&apiId=1&authorization=FwafY5kg3c
```

#### Parameters

- `direct` (required) - Must be set to 1 or true. Tells Easy API this is a externally-triggered queue job.
- `apiId` (required) - The ID of the api you wish to process.
- `authorization` (required) - A unique, generated identifier for this api. Ensures not just anyone can trigger the import.
- `url` (optional) - If your api URL changes, you can specify it here. Ensure the structure of the api matches your field mappings.

#### Setup

To setup this api to run via cron, use one of the following commands - replacing the URL with the one for your api. Which command you use will depend on your server capabilities, but `wget` is the most common.

```
/usr/bin/wget -O - -q -t 1 "http://your.domain/actions/easyapi/apis/run-task?direct=1&apiId=1&authorization=FwafY5kg3c"

curl --silent --compressed "http://your.domain/actions/easyapi/apis/run-task?direct=1&apiId=1&authorization=FwafY5kg3c"

/usr/bin/lynx -source "http://your.domain/actions/easyapi/apis/run-task?direct=1&apiId=1&authorization=FwafY5kg3c
```

### Console command

You can also trigger your api to process via a console command by passing in a comma-separated list of api IDs to process. You can also use `limit` and `offset` parameters.

```bash
> php craft easyapi/apis/queue 1

> php craft easyapi/apis/queue 1,2,3

> php craft easyapi/apis/queue 1 --limit=1

> php craft easyapi/apis/queue 1 --limit=1 --offset=1

> php craft easyapi/apis/queue 1 --continue-on-error
```

You can also supply a `--all` parameter to push all apis into the queue. Not that this parameter will ignore any `--limit` and `--offset` parameters supplied.

```bash
> php craft easyapi/apis/queue --all
````

Note that the `easyapi/apis/queue` command will only queue up the importing job. To actually run the import, you will need to run your queue. You can do that by running the `queue/run` command:

```bash
> php craft queue/run
```
