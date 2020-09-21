---
layout: default
title: Parts Kit
nav_order: 4
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

<!-- {% raw %} -->
```twig
{% extends 'viget-base/_layouts/parts-kit' %}

{% block main %}
    Here is your component code
{% endblock %}
```
<!-- {% endraw %} -->

This layout extends from the `_layouts/app` file that lives in your `templates` directory. This also makes the assumption that the block being used in your layout is named `content`

## Customization

You can override the following values with a `config/parts-kit.php`

| Key         | Default        | Description                              |
|:------------|:---------------|:-----------------------------------------|
| `directory` | `parts-kit`    | Directory of your parts kit              |
| `layout`    | `_layouts/app` | Your layout that should be extended from |
| `volume`    | `partsKit`     | Craft Asset volume for sample images     |
| `theme`     | `light`        | Choose from provided light & dark themes or provide your own |

### Example

```php
<?php

return [
    'directory' => 'parts-kit',
    'layout' => '_layouts/app',
    'volume' => 'partsKit',
    'theme' => 'light',
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

#### `getParagraph()`

This method returns a random Lorem ipsum paragraph made up of 5 sentences

Parameters: none

Returns: `string`

Example:

<!-- {% raw %} -->
```twig
<p>{{ craft.viget.partsKit.getParagraph() }}</p>
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

## Theming

By default, the **light** theme is applied.

### Dark Mode

You can easily switch to a provided **dark theme** by specifying `dark` in your config:

```php
// config/parts-kit.php

<?php

return [
    'theme' => 'dark',
];
```

### Customize

Alternatively, you can pass an array with values corresponding to all of the CSS custom properties used to style the theme. For example, the following config contains all of the supported values and would generate a dark blue theme:

```php
// config/parts-kit.php

<?php

return [
    'theme' => [
        'background' => '#2c3e50',
        'main-background' => '#34495e',
        'text' => 'white',
        'nav-icon' => '#2ecc71',
        'nav-item-text-hover' => 'white',
        'nav-item-background-hover' => 'rgba(255, 255, 255, 0.1)',
        'nav-subitem-text-hover' => 'white',
        'nav-subitem-background-hover' => 'rgba(255, 255, 255, 0.1)',
        'nav-subitem-background-active' => '#2ecc71',
        'nav-subitem-text-active' => '#fff',
        'controls-text' => 'rgba(255, 255, 255, 0.3)',
        'controls-border' => 'rgba(255, 255, 255, 0.1)',
    ],
];
```
