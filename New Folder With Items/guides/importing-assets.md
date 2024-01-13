# Importing Assets

Assets are more complex to import because each is an element that’s created with either a local file or remote URL. (Easy API supports both.)

Not only can you use Asset importing to create/upload image files, you can also use it to update them, changing things like custom fields, titles, or even filenames.

Below we’ll look at a real-world example of importing a collection of remote images into Craft.

:::tip
If you’ve already got local image files ready to import, you can skip Easy API and [update asset indexes](https://runwildstudio.co.nz/docs/3.x/assets.html#updating-asset-indexes):

1. Copy the image files/folders into your volume’s root folder.
2. In the control panel, go to **Utilities** → **Asset Indexes**.
3. Select the volume from step 1 and choose **Update asset indexes**.

This will scan the folder and add images into Craft as assets.
:::

### Example Api Data

This data is what we’ll use for this guide:

::: code
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Images>
    <Image>
        <Title>Easy API Social Card</Title>
        <URL>https://runwildstudio.co.nz/uploads/plugins/easyapi/_800x455_crop_center-center_none/easyapi-social-card.png</URL>
        <Caption>Some Caption</Caption>
    </Image>

    <Image>
        <Title>Craft Commerce Social Card</Title>
        <URL>https://runwildstudio.co.nz/uploads/plugins/commerce/_800x455_crop_center-center_none/commerce-social-card.png</URL>
        <Caption>Another Caption</Caption>
    </Image>
</Images>
```

```json
{
    "Image": [
        {
            "Title": "Easy API Social Card",
            "URL": "https://runwildstudio.co.nz/uploads/plugins/easyapi/_800x455_crop_center-center_none/easyapi-social-card.png",
            "Caption": "Some Caption"
        },
        {
            "Title": "Craft Commerce Social Card",
            "URL": "https://runwildstudio.co.nz/uploads/plugins/commerce/_800x455_crop_center-center_none/commerce-social-card.png",
            "Caption": "Another Caption"
        }
    ]
}
```
:::

Choose either the XML or JSON (depending on your preference), and save as a file in the root of your public directory. We’ll assume its URL is `http://craft.local/assets-api.xml`.

## Set Up Your Api

With your api data in place, go to Easy API’s main control panel view and add a new api.

Enter the following details:

- **Name** - Property Api
- **Api URL** - `http://craft.local/assets-api.xml`
- **Api Type** - _XML or JSON_
- **Primary Element** - `image`
- **Element Type** - Asset
- **Asset Source** - General
- **Import Strategy** - `Create new elements`, and `Update existing elements`
- **Passkey** - Leave as generated
- **Backup** - Turn on

Choose **Save & Continue** to set up the primary element.

## Primary Element

The primary element can be confusing at first, but it’s vitally important to ensure Easy API can locate your api’s content. Refer to [Primary Element →](../feature-tour/primary-element.md) for a detailed explanation.

Enter the following details:

- **Primary Element** - `/Images/Image`
- **Pagination URL** - `No Pagination URL`

Choose **Save & Continue** to set up the field mapping.

## Field Mapping

Use screenshot below as a guide for mapping data asset fields:

- We’re providing the full URL to the image to be uploaded as an asset.
- We’re telling Easy API that if it finds an asset with the same filename to use that instead of uploading a potential duplicate.
- We’re providing the full URL again for the **Filename** field. Easy API will automatically determine the filename from the URL, but you can optionally specify another node from the api if you’d like.
- We’re checking against existing assets using their filename.

:::tip
The URL or Path field will only be shown if your api’s **Import strategy** includes `Create new elements`.
:::

:::tip
This examples uses remote URLs, but you can also provide a local path to the filename of the image you want to add. The path should be relative to your project’s web root.

For example, `to-upload/easyapi.png` would be a folder in your web root named `to-upload` with the file `easyapi.png` inside it.
:::

### Folders

This example imports images into the top level of the selected volume, but you may also designate the subfolder each asset gets imported to.

#### Import All Assets to a Subfolder

To import all the api’s assets to the same folder, select **Use default value** in the folder’s **Api Element** column, then select your desired subfolder in the **Default Value** column.

#### Specify an Existing Subfolder for Each Asset

Your api can include a folder ID for each item which you can then use to determine where each imported asset ends up. Select that field from the **Field Element** column and optionally set a folder in the **Default Value** column as a fallback in case the api’s folder ID is missing. (Invalid IDs will result in an error.)

::: tip
Every folder has an ID whether it’s the top level or a subfolder, and you can find it in the database or inspecting the markup in the Assets section of the control panel. (Look for the `data-folder-id` attribute on menu elements.)
:::

![Apiint Guide Mapping](../screenshots/easyapi-guide-asset-field-mapping.png)

Choose **Save & Import** to begin importing your content.

## Importing Your Content

Wait for the api processing to finish. Remember, you can always navigate away from this confirmation screen.

![Apiint Start](../screenshots/easyapi-start.png)

:::tip
If you’re having issues, see the [Troubleshooting](../troubleshooting.md) section.
:::

You should now have two brand new assets in your General volume.

![Apiint Guide Finish](../screenshots/easyapi-guide-asset-finish.png)
