---
layout: default
title: Parts Kit
nav_order: 3
---

# Parts Kit

A [Storybook](https://storybook.js.org/)-esque parts kit framework comes bundled with this module. It automatically generates a navigation and layout to make creating a parts kit very low effort.

## Setup

Inside of your `templates` directory, create a `parts-kit` directory. The module will automatically read all subdirectories and files and generate a navigation.

**Note: This only supports one level of subdirectories.**

### Example Directory Structure

```
|-- templates
    |-- parts-kit
        |-- button
            |-- blue.html
            |-- default.html
        |-- cta
            |-- dark.html
            |-- default.html
```

This would generate the following navigation:

<img src="resources/parts-kit-nav.png" alt="Parts Kit Navigation">

### Component Files

Your parts kit files will need to use the layout that comes with this module:

```twig
{% extends 'viget-base/_layouts/parts-kit' %}

{% block main %}
    Here is your component code
{% endblock %}
```

This layout extends from the `_layouts/app` file that lives in your `templates` directory. This also makes the assumption that the block being used in your layout is named `content`

## Cusomization

You can override the following values with a `config/parts-kit.php`

| Key         | Default        | Description                              |
|:------------|:---------------|:-----------------------------------------|
| `directory` | `parts-kit`    | Directory of your parts kit              |
| `layout`    | `_layouts/app` | Your layout that should be extended from |
| `volume`    | `partsKit`     | Craft Asset volume for sample images     |

### Example

```php
<?php

return [
    'directory' => 'parts-kit',
    'layout' => '_layouts/app',
    'volume' => 'partsKit',
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

```twig
{% set partsKitImage = craft.viget.partsKit.getImage('350x230.png') %}
{% if partsKitImage %}
    <img src="{{ partsKitImage.url }}" alt="">
{% endif %}
```

#### `getText($words)`

This method returns random Lorem ipsum text

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `10`    |

Returns: `string`

Example:

```twig
<p>{{ craft.viget.partsKit.getText(40) }}</p>
```

#### `getTitle($words)`

This method returns random Lorem ipsum "title length" text (basically the same functionality as `getText`)

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `5`     |

Returns: `string`

Example:

```twig
<p>{{ craft.viget.partsKit.getTitle(10) }}</p>
```

#### `getSentence($words)`

This method returns a random Lorem ipsum title sentence (including a period)

Parameters:

| Name    | Type  | Default |
|:--------|:------|:--------|
| `words` | `int` | `10`    |

Returns: `string`

Example:

```twig
<p>{{ craft.viget.partsKit.getSentence(10) }}</p>
```

#### `getParagraph()`

This method returns a random Lorem ipsum paragraph made up of 5 sentences

Parameters: none

Returns: `string`

Example:

```twig
<p>{{ craft.viget.partsKit.getParagraph() }}</p>
```

#### `getRichTextShort()`

This method returns 2 paragraphs of rich text Lorem ipsum content including the following inline HTML:

- `strong`
- `b`
- `a`
- `em`
- `i`

Parameters: none

Returns: `Markup`

Example:

```twig
{{ craft.viget.partsKit.getRichTextShort() }}
```

#### `getRichTextFull()`

This method returns "kitchen sink" HTML content

Parameters: none

Returns: `Markup`

Example:

```twig
{{ craft.viget.partsKit.getRichTextFull() }}
```
