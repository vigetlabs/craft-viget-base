---
layout: default
title: Parts Kit
nav_order: 4
---

# Parts Kit

A [Storybook](https://storybook.js.org/)-esque parts kit framework comes bundled with this module. It automatically generates a navigation and layout to make creating a parts kit very low effort.

The UI for this parts kit is a JavaScript app that is decoupled from the `craft-viget-base` repo. It can work with any CMS or application platform.

See: [Viget Parts Kit UI](https://github.com/vigetlabs/parts-kit)

## Setup

Inside your `templates` directory, create a `parts-kit` directory. The module will automatically read all subdirectories and files and generate a navigation.

**Note: This supports an infinite level of subdirectories, but typically 1 - 3 is enough.**

### Example Directory Structure

```
|-- templates
    |-- parts-kit
        |-- tokens
            |-- colors.twig
            |-- spacing.twig
        |-- components
            |-- button
                |-- blue.twig
                |-- default.twig
            |-- card
                |-- default.twig
                |-- wide.twig
```

This would generate the following navigation:

![Example of parts kit navigation](resources/parts-kit-nav.png)

### Component Files

Your parts kit files will need to use the layout that comes with this module:

<!-- {% raw %} -->
```twig
{% extends 'viget-base/_layouts/parts-kit' %}

{% block main %}
    Here is your component code
{% endblock %}
```
<!-- {% endraw %} -->

This layout extends from the `_layouts/app` file that lives in your `templates` directory. This also makes the assumption that the block being used in your layout is named `content`

### Hiding your header & footer in the parts.

If you configure the parts kit to use your project's default layout file, you'll typically end up with a header & footer in all of your parts kit pages.

To hide the header and footer, you can use the following service method to check if your on a parts kit page.

```twig
{% if not craft.viget.partsKit.isRequest %}
    {# Include your header or footer #}
{% endif %}
```

Another option is to store your scripts and styles in their own partial.

You can then create a separate "Parts Kit" layout that only includes a basic html boilerplate and your projects scripts & styles. 

The customization section shows how to configure a different parts kit layout file.

## Customization

You can override the following values via the same config file used for all base module features: `/config/viget.php`. These settings should be nested under a `partsKit` key.

| Key         | Default        | Description                                                                               |
|:------------|:---------------|:------------------------------------------------------------------------------------------|
| `directory` | `parts-kit`    | Directory of your parts kit                                                               |
| `layout`    | `_layouts/app` | Your projects primary layout. It should contain the styles and JavaScript for your parts. |
| `volume`    | `partsKit`     | Craft Asset volume for sample images                                                      |

### Example

```php
// /config/viget.php
<?php

return [
    'partsKit' => [
        'directory' => 'parts-kit',
        'layout' => '_layouts/app',
        'volume' => 'partsKit',
    ],
    // other base module settings
    'tailwind' => [
        //...
    ],
];
```

## Components exposed to Twig `craft.viget` sub-object

There are a number of helper methods exposed to Twig to help with dummy content.

### `craft.viget.partsKit.*`

#### `getImage($name)`

This method returns an [Asset](https://docs.craftcms.com/api/v3/craft-elements-asset.html) from your parts kit volume based on filename

Parameters:

| Name   | Type     | Default |
|:-------|:---------|:--------|
| `name` | `string` | `null`  |

Returns: `Asset|null`

Example:

<!-- {% raw %} -->
```twig
{% set partsKitImage = craft.viget.partsKit.getImage('350x230.png') %}
{% if partsKitImage %}
    <img src="{{ partsKitImage.url }}" alt="">
{% endif %}
```
<!-- {% endraw %} -->

***

#### `getText($words)`

This method returns random Lorem ipsum text

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `10`    |

Returns: `string`

Example:

<!-- {% raw %} -->
```twig
<p>{{ craft.viget.partsKit.getText(40) }}</p>
```
<!-- {% endraw %} -->

***

#### `getTitle($words)`

This method returns random Lorem ipsum "title length" text (basically the same functionality as `getText`)

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `5`     |

Returns: `string`

Example:

<!-- {% raw %} -->
```twig
<p>{{ craft.viget.partsKit.getTitle(10) }}</p>
```
<!-- {% endraw %} -->

***

#### `getSentence($words)`

This method returns a random Lorem ipsum title sentence (including a period)

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `10`    |

Returns: `string`

Example:

<!-- {% raw %} -->
```twig
<p>{{ craft.viget.partsKit.getSentence(10) }}</p>
```
<!-- {% endraw %} -->

***

#### `getParagraph($sentences)`

This method returns a random Lorem ipsum paragraph made up of any number of sentences

Parameters:

| Name        | Type  | Default |
|:------------|:------|:--------|
| `sentences` | `int` | `5`     |

Returns: `string`

Example:

<!-- {% raw %} -->
```twig
<p>{{ craft.viget.partsKit.getParagraph(2) }}</p>
```
<!-- {% endraw %} -->

***

#### `getRichTextShort()`

This method returns 3 paragraphs of rich text Lorem ipsum content including the following inline HTML:

- `strong`
- `b`
- `a`
- `em`
- `i`

Parameters: none

Returns: `Markup`

Example:

<!-- {% raw %} -->
```twig
{{ craft.viget.partsKit.getRichTextShort() }}
```
<!-- {% endraw %} -->

***

#### `getRichTextFull()`

This method returns "kitchen sink" HTML content

Parameters: none

Returns: `Markup`

Example:

<!-- {% raw %} -->
```twig
{{ craft.viget.partsKit.getRichTextFull() }}
```
<!-- {% endraw %} -->

