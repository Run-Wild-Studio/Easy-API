---
layout: page
title: Fields
permalink: /mapping/fields/
---

# Field Types

Easy API supports all native [Craft Fields](https://runwildstudio.co.nz/docs/4.x/fields.html), and even some third-party ones.

### Assets

Accepts single or multiple values. You should supply the filename only, excluding the full path to the asset, but including the filename. If you're uploading remote assets, you'll need to produce fully-qualified URLs.

#### Additional Options

- Upload remote asset (choose how to handle existing assets - Replace/Keep/Ignore)
- [Inner-element fields](#inner-element-fields)

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="assets-xml-tab" data-bs-toggle="tab" data-bs-target="#assets-xml" type="button" role="tab" aria-controls="assets-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="assets-json-tab" data-bs-toggle="tab" data-bs-target="#assets-json" type="button" role="tab" aria-controls="assets-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="assets-xml" role="tabpanel" aria-labelledby="assets-xml-tab">
      <pre>
        &lt;Asset&gt;my_filename.jpg&lt;/Asset&gt;
        // Or
        &lt;Assets&gt;
            &lt;Asset&gt;my_filename.jpg&lt;/Asset&gt;
            &lt;Asset&gt;my_other_filename.jpg&lt;/Asset&gt;
        &lt;/Assets&gt;
        //
        // When selecting upload
        //
        &lt;Asset&gt;http://mydomain.com/my_filename.jpg&lt;/Asset&gt;
        // Or
        &lt;Assets&gt;
            &lt;Asset&gt;http://mydomain.com/my_filename.jpg&lt;/Asset&gt;
            &lt;Asset&gt;http://mydomain.com/my_other_filename.jpg&lt;/Asset&gt;
        &lt;/Assets&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="assets-json" role="tabpanel" aria-labelledby="assets-json-tab">
      <pre>
        {
            "Asset": "my_filename.jpg"
        }
        // Or
        {
            "Assets": [
                "my_filename.jpg",
                "my_other_filename.jpg"
            ]
        }
        //
        // When selecting upload
        //
        {
            "Asset": "http://mydomain.com/my_filename.jpg"
        }
        // Or
        {
            "Assets": [
                "http://mydomain.com/my_filename.jpg",
                "http://mydomain.com/my_other_filename.jpg"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Categories

Accepts single or multiple values.

#### Additional Options

- Create category if it does not exist
- [Inner-element fields](#inner-element-fields)
- [Set element attribute](#inner-element-fields) for data being imported
- Title
- ID
- Slug

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="categories-xml-tab" data-bs-toggle="tab" data-bs-target="#categories-xml" type="button" role="tab" aria-controls="categories-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="categories-json-tab" data-bs-toggle="tab" data-bs-target="#categories-json" type="button" role="tab" aria-controls="categories-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="categories-xml" role="tabpanel" aria-labelledby="categories-xml-tab">
      <pre>
        &lt;Category>My Category&lt;/Category&gt;
        // Or
        &lt;Categories&gt;
            &lt;Category&gt;My Category&lt;/Category&gt;
            &lt;Category&gt;Another Category&lt;/Category&gt;
        &lt;/Categories&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="categories-json" role="tabpanel" aria-labelledby="categories-json-tab">
      <pre>
        {
            "Category": "My Category"
        }
        // Or
        {
            "Categories": [
                "My Category",
                "Another Category"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Checkboxes

Accepts single or multiple values. You must provide the Value of the option to check, not the Label.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="checkboxes-xml-tab" data-bs-toggle="tab" data-bs-target="#checkboxes-xml" type="button" role="tab" aria-controls="checkboxes-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="checkboxes-json-tab" data-bs-toggle="tab" data-bs-target="#checkboxes-json" type="button" role="tab" aria-controls="checkboxes-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="checkboxes-xml" role="tabpanel" aria-labelledby="checkboxes-xml-tab">
      <pre>
        <Checkbox&gt;option1</Checkbox&gt;
        // Or
        &lt;Checkboxes&gt;
            &lt;Option&gt;option1&lt;/Option&gt;
            &lt;Option&gt;option2&lt;/Option&gt;
        &lt;/Checkboxes&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="checkboxes-json" role="tabpanel" aria-labelledby="checkboxes-json-tab">
      <pre>
        {
            "Checkbox": "option1"
        }
        // Or
        {
            "Checkboxes": [
                "option1",
                "option2"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Colour

Accepts a single valid Colour value - usually in Hexadecimal.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="colour-xml-tab" data-bs-toggle="tab" data-bs-target="#colour-xml" type="button" role="tab" aria-controls="colour-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="colour-json-tab" data-bs-toggle="tab" data-bs-target="#colour-json" type="button" role="tab" aria-controls="colour-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="colour-xml" role="tabpanel" aria-labelledby="colour-xml-tab">
      <pre>
      &lt;Colour&gt;#ffffff&lt;/Colour&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="colour-json" role="tabpanel" aria-labelledby="colour-json-tab">
      <pre>
        {
            "Color": "#ffffff"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Date/Time
Accepts a single valid date and time string. Supports many different formats, using PHP's [date\_parse](http://php.net/manual/en/function.date-parse.php) function.


<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="date-xml-tab" data-bs-toggle="tab" data-bs-target="#date-xml" type="button" role="tab" aria-controls="date-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="date-json-tab" data-bs-toggle="tab" data-bs-target="#date-json" type="button" role="tab" aria-controls="date-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="date-xml" role="tabpanel" aria-labelledby="date-xml-tab">
      <pre>
        &lt;Date&gt;Tue, 24 Feb 2015 09:00:53 +0000&lt;/Date&gt;
        &lt;Date&gt;2015-02-24 09:00:53&lt;/Date&gt;
        &lt;Date&gt;02/24/2015&lt;/Date&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="date-json" role="tabpanel" aria-labelledby="date-json-tab">
      <pre>
        {
            "Date": "Tue, 24 Feb 2015 09:00:53 +0000"
        }
        {
            "Date": "2015-02-24 09:00:53"
        }
        {
            "Date": "02/24/2015"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Dropdown

Accepts a single value. You must provide the Value of the option to select, not the Label.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="xml-tab" data-bs-toggle="tab" data-bs-target="#xml" type="button" role="tab" aria-controls="xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="json-tab" data-bs-toggle="tab" data-bs-target="#json" type="button" role="tab" aria-controls="json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="xml" role="tabpanel" aria-labelledby="xml-tab">
      <pre>
      &lt;Dropdown&gt;option2&lt;/Dropdown&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="json" role="tabpanel" aria-labelledby="json-tab">
      <pre>
        {
            "Dropdown": "option2"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Entries

Accepts single or multiple values.

#### Additional Options

- Create entry if it does not exist
- [Inner-element fields](#inner-element-fields)
- [Set element attribute](#inner-element-fields) for data being imported
- Title
- ID
- Slug

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="entry-xml-tab" data-bs-toggle="tab" data-bs-target="#entry-xml" type="button" role="tab" aria-controls="entry-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="entry-json-tab" data-bs-toggle="tab" data-bs-target="#entry-json" type="button" role="tab" aria-controls="entry-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="entry-xml" role="tabpanel" aria-labelledby="entry-xml-tab">
      <pre>
      &lt;Entry&gt;Entry Title&lt;/Entry&gt;
      // Or
      &lt;Entries&gt;
        &lt;Entry&gt;Entry Title&lt;/Entry&gt;
        &lt;Entry&gt;Another Entry&lt;/Entry&gt;
      &lt;/Entries&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="entry-json" role="tabpanel" aria-labelledby="entry-json-tab">
      <pre>
        {
            "Entry": "My Entry"
        }
        // Or
        {
            "Entries": [
                "My Entry",
                "Another Entry"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Lightswitch

Accepts a single value. Can be provided as any boolean-like string.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="lightswitch-xml-tab" data-bs-toggle="tab" data-bs-target="#lightswitch-xml" type="button" role="tab" aria-controls="lightswitch-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="lightswitch-json-tab" data-bs-toggle="tab" data-bs-target="#lightswitch-json" type="button" role="tab" aria-controls="lightswitch-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="lightswitch-xml" role="tabpanel" aria-labelledby="lightswitch-xml-tab">
      <pre>
      // 1/0
      &lt;Lightswitch&gt;1&lt;/Lightswitch&gt;
      // true/false
      &lt;Lightswitch&gt;true&lt;/Lightswitch&gt;
      // Yes/No/0
      &lt;Lightswitch&gt;Yes&lt;/Lightswitch&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="lightswitch-json" role="tabpanel" aria-labelledby="lightswitch-json-tab">
      <pre>
        // 1/0
        {
            "Lightswitch": "1"
        }
        // true/false
        {
            "Lightswitch": "true"
        }
        // Yes/No
        {
            "Lightswitch": "Yes"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Matrix

Accepts a nested set of tags.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="matrix-xml-tab" data-bs-toggle="tab" data-bs-target="#matrix-xml" type="button" role="tab" aria-controls="matrix-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="matrix-json-tab" data-bs-toggle="tab" data-bs-target="#matrix-json" type="button" role="tab" aria-controls="matrix-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="matrix-xml" role="tabpanel" aria-labelledby="matrix-xml-tab">
      <pre>
        &lt;Matrix&gt;
          &lt;MatrixBlock&gt;
            &lt;HeadingSize&gt;h1&lt;/HeadingSize&gt;
            &lt;HeadingText&gt;This is an H1 tag&lt;/HeadingText&gt;
          &lt;/MatrixBlock&gt;
          &lt;MatrixBlock&gt;
            &lt;Copy&gt;&lt;![CDATA[&lt;p>=&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam lectus nisl, mattis et luctus ut, varius vitae augue. Integer non lacinia urna, nec molestie enim. Aenean ultricies mattis ligula vel consectetur. Etiam ultrices fringilla lectus nec mollis.&lt;/p&gt; &lt;p&gt;Nunc elit magna, semper ac faucibus ut, volutpat eu augue. Vivamus id nibh facilisis, fermentum massa vitae, rhoncus mi. Praesent sit amet efficitur dui.&lt;/p&gt;]]&gt;&lt;/Copy&gt;
          &lt;/MatrixBlock&gt;
          &lt;MatrixBlock&gt;
            &lt;HeadingSize&gt;h2&lt;/HeadingSize&gt;
            &lt;HeadingText&gt;This is an H2 tag&lt;/HeadingText&gt;
          &lt;/MatrixBlock&gt;
          &lt;MatrixBlock&gt;
            &lt;Images&gt;
              &lt;Image&gt;img_fjords.jpg&lt;/Image&gt;
              &lt;Image&gt;recent-images-11.jpg&lt;/Image&gt;
            &lt;/Images&gt;
          &lt;/MatrixBlock&gt;
          &lt;MatrixBlock&gt;
            &lt;Copy&gt;&lt;![CDATA[&lt;p&gt;Etiam lectus nisl, mattis et luctus ut, varius vitae augue. Integer non lacinia urna, nec molestie enim. Aenean ultricies mattis ligula vel consectetur. Etiam ultrices fringilla lectus nec mollis.&lt;/p&gt; &lt;p>Nunc elit magna, semper ac faucibus ut, volutpat eu augue. Vivamus id nibh facilisis, fermentum massa vitae, rhoncus mi. Praesent sit amet efficitur dui.&lt;/p&gt;]]&gt;&lt;/Copy&gt;
          &lt;/MatrixBlock&gt;
        &lt;/Matrix&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="matrix-json" role="tabpanel" aria-labelledby="matrix-json-tab">
      <pre>
        "Matrix": {
          "MatrixBlock": [
            {
              "HeadingSize": "h1",
              "HeadingText": "This is an H1 tag"
            },
            {
              "Copy": {}
            },
            {
              "HeadingSize": "h2",
              "HeadingText": "This is an H2 tag"
            },
            {
              "Images": {
                "Image": [
                  "img_fjords.jpg",
                  "recent-images-11.jpg"
                ]
              }
            },
            {
              "Copy": {}
            }
          ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Money

Accepts a single value.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="money-xml-tab" data-bs-toggle="tab" data-bs-target="#money-xml" type="button" role="tab" aria-controls="money-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="money-json-tab" data-bs-toggle="tab" data-bs-target="#money-json" type="button" role="tab" aria-controls="money-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="money-xml" role="tabpanel" aria-labelledby="money-xml-tab">
      <pre>
      &lt;Money&gt;20&lt;/Money&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="money-json" role="tabpanel" aria-labelledby="money-json-tab">
      <pre>
        {
            "Money": "10"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Multi-select

Accepts single or multiple values. You must provide the Value of the option to select, not the Label.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="multi-xml-tab" data-bs-toggle="tab" data-bs-target="#multi-xml" type="button" role="tab" aria-controls="multi-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="multi-json-tab" data-bs-toggle="tab" data-bs-target="#multi-json" type="button" role="tab" aria-controls="multi-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="multi-xml" role="tabpanel" aria-labelledby="multi-xml-tab">
      <pre>
        &lt;MultiSelect&gt;option&lt;/MultiSelect&gt;
        // Or
        &lt;MultiSelects&gt;
            &lt;MultiSelect&gt;option1&lt;/MultiSelect&gt;
            &lt;MultiSelect&gt;option2&lt;/MultiSelect&gt;
        &lt;/MultiSelects&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="multi-json" role="tabpanel" aria-labelledby="multi-json-tab">
      <pre>
        {
          "MultiSelect": "option1"
        }
        // Or
        {
            "MultiSelects": [
                "option1",
                "option2"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Number

Accepts a single value.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="number-xml-tab" data-bs-toggle="tab" data-bs-target="#number-xml" type="button" role="tab" aria-controls="number-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="number-json-tab" data-bs-toggle="tab" data-bs-target="#number-json" type="button" role="tab" aria-controls="number-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="number-xml" role="tabpanel" aria-labelledby="number-xml-tab">
      <pre>
      &lt;Number&gt;20&lt;/Number&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="number-json" role="tabpanel" aria-labelledby="number-json-tab">
      <pre>
        {
            "Number": "10"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Plain Text

Accepts a single value.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="text-xml-tab" data-bs-toggle="tab" data-bs-target="#text-xml" type="button" role="tab" aria-controls="text-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="text-json-tab" data-bs-toggle="tab" data-bs-target="#text-json" type="button" role="tab" aria-controls="text-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="text-xml" role="tabpanel" aria-labelledby="text-xml-tab">
      <pre>
      &lt;PlainText&gt;Lorem ipsum dolor sit amet&lt;/PlainText&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="text-json" role="tabpanel" aria-labelledby="text-json-tab">
      <pre>
        {
            "PlainText": "Lorem ipsum dolor sit amet"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Radio Buttons

Accepts a single value. You must provide the Value of the option to select, not the Label.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="radio-xml-tab" data-bs-toggle="tab" data-bs-target="#radio-xml" type="button" role="tab" aria-controls="radio-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="radio-json-tab" data-bs-toggle="tab" data-bs-target="#radio-json" type="button" role="tab" aria-controls="radio-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="radio-xml" role="tabpanel" aria-labelledby="radio-xml-tab">
      <pre>
      &lt;Radio&gt;Option2&lt;/Radio&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="radio-json" role="tabpanel" aria-labelledby="radio-json-tab">
      <pre>
        {
            "Radio": "option2"
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Table

Each Table field row has multiple columns, so you map each field value to a column, rather than the entire Table field. You also group your columns into rows, as shown below.

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="table-xml-tab" data-bs-toggle="tab" data-bs-target="#table-xml" type="button" role="tab" aria-controls="table-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="table-json-tab" data-bs-toggle="tab" data-bs-target="#table-json" type="button" role="tab" aria-controls="table-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="table-xml" role="tabpanel" aria-labelledby="table-xml-tab">
      <pre>
        &lt;Table&gt;
          &lt;Row&gt;
              &lt;ColumnOne&gt;Content&lt;/ColumnOne&gt;
              &lt;ColumnTwo&gt;For&lt;/ColumnTwo&gt;
          &lt;/Row&gt;
          &lt;Row&gt;
              &lt;ColumnOne&gt;Table&lt;/ColumnOne&gt;
              &lt;ColumnTwo&gt;Field&lt;/ColumnTwo&gt;
          &lt;/Row&gt;
      &lt;/Table&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="table-json" role="tabpanel" aria-labelledby="table-json-tab">
      <pre>
        {
            "Table": [{
                "ColumnOne": "Content",
                "ColumnTwo": "For"
            },{
                "ColumnOne": "Table",
                "ColumnTwo": "Field"
            }]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Tags

Accepts single or multiple values.

#### Additional Options

- Create tag if it does not exist
- [Inner-element fields](#inner-element-fields)

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tags-xml-tab" data-bs-toggle="tab" data-bs-target="#tags-xml" type="button" role="tab" aria-controls="tags-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tags-json-tab" data-bs-toggle="tab" data-bs-target="#tags-json" type="button" role="tab" aria-controls="tags-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="tags-xml" role="tabpanel" aria-labelledby="tags-xml-tab">
      <pre>
        &lt;Tag&gt;My Tag&lt;/Tag&gt;
        // Or
        &lt;Tags&gt;
            &lt;Tag&gt;First Tag&lt;/Tag&gt;
            &lt;Tag&gt;Second Tag&lt;/Tag&gt;
        &lt;/Tags&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="tags-json" role="tabpanel" aria-labelledby="tags-json-tab">
      <pre>
        {
            "Tag": "My Tag"
        }
        // Or
        {
            "Tags": [
                "First Tag",
                "Second Tag"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

### Users

Accepts single or multiple values.

#### Additional Options

- Create user if they do not exist
- [Inner-element fields](#inner-element-fields)
- [Set element attribute](#inner-element-fields) for data being imported
- Email
- ID
- Username
- Full Name

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="user-xml-tab" data-bs-toggle="tab" data-bs-target="#user-xml" type="button" role="tab" aria-controls="user-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="user-json-tab" data-bs-toggle="tab" data-bs-target="#user-json" type="button" role="tab" aria-controls="user-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="user-xml" role="tabpanel" aria-labelledby="user-xml-tab">
      <pre>
      &lt;User&gt;123@nothing.com&lt;/User&gt;
      // Or
      &lt;Users&gt;
          &lt;User&gt;123@nothing.com&lt;/User&gt;
          &lt;User&gt;123@something.com&lt;/User&gt;
      &lt;/Users&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="user-json" role="tabpanel" aria-labelledby="user-json-tab">
      <pre>
        {
            "User": "123@nothing.com"
        }
        // Or
        {
            "Users": [
                "123@nothing.com",
                "123@something.com"
            ]
        }
      </pre>
    </div>
  </div>
</div>

<hr>

## Third Party

The following third-party fields are supported.

- [Google Maps](https://github.com/doublesecretagency/craft-googlemaps)
- [Smart Map](https://github.com/doublesecretagency/craft-smartmap)
- [Simple Map](https://github.com/ethercreative/simplemap)
- [Super Table](https://verbb.io/craft-plugins/super-table)
- [Solspace Calendars](https://solspace.com/craft/calendar)
- [Digital Products](https://github.com/craftcms/digital-products)
- [Commerce Products](https://docs.craftcms.com/commerce/v2/products-fields.html)
- [Commerce Variants](https://docs.craftcms.com/commerce/v2/products-fields.html)
- [Linkit](https://github.com/fruitstudios/linkit)
- [Typed Link](https://github.com/sebastian-lenz/craft-linkfield)

<hr>

## Element Attributes

For element fields (Assets, Categories, Entries, Tags and Users), you'll want to check against any existing elements. Easy API gives you the flexibility to choose how to match against existing elements. These will depend on what element it is, but will often be `slug` or `title`.

What this means in practical terms, is that your api data can provide the ID, Title or Slug of an Entry - or the ID, Username, Name or Email for a User, and so on.

For instance, look at the following example api data we want to import into a Categories field:

<div class="code">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="attributes-xml-tab" data-bs-toggle="tab" data-bs-target="#attributes-xml" type="button" role="tab" aria-controls="attributes-xml" aria-selected="true">XML</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="attributes-json-tab" data-bs-toggle="tab" data-bs-target="#attributes-json" type="button" role="tab" aria-controls="attributes-json" aria-selected="false">JSON</button>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="attributes-xml" role="tabpanel" aria-labelledby="attributes-xml-tab">
      <pre>
        // Title provided
        &lt;Category&gt;My Category&lt;/Category&gt;
        // Slug provided
        &lt;Category&gt;my-category&lt;/Category&gt;
        // ID provided
        &lt;Category&gt;23&lt;/Category&gt;
      </pre>
    </div>
    <div class="tab-pane fade" id="attributes-json" role="tabpanel" aria-labelledby="attributes-json-tab">
      <pre>
        // Title provided
        {
            "Category": "My Category"
        }
        // Slug provided
        {
            "Category": "my-category"
        }
        // ID provided
        {
            "Category": "23"
        }
      </pre>
    </div>
  </div>
</div>

Depending on what data your api contains, you'll need to select the appropriate attribute, to tell Easy API how to deal with your data.

<hr>

## Inner Element Fields

As each Element (Assets, Categories, Entries, Tags, Users) can have custom fields themselves, Easy API gives you the chance to map to those fields as well. They'll appear under any row when mapping to an Element field.

See the introduction to field mapping for more information on setting up nested fields.
