---
layout: default
title: Control Panel
nav_order: 3
---

# Tailwind

For projects with a Tailwind config, this module lets you access those tokens in your PHP and Twig. This is especially useful for accessing your system colors.

## Setup

Your Tailwind config must be parsed and written to a JSON file, typically via Webpack (FedPack example link coming soon). The only configuration available is the path to that JSON file, relative to `CRAFT_BASE_PATH`. You can override that value via the same config file used for all base module features: `/config/viget.php`. These settings should be nested under a `tailwind` key.

| Key          | Default                             | Description                     |
|:-------------|:------------------------------------|:--------------------------------|
| `configPath` | `/config/tailwind/tailwind.json`    | Path to your Tailwind JSON file |

Here's a full sample configuration:

```php
// /config/viget.php
<?php

return [
    'tailwind' => [
        'configPath' => '../config/custom-tailwind-name.json',
    ],
    // other base module settings
    'partsKit' => [
        //...
    ],
];
```

## Components exposed to Twig `craft.viget.tailwind` sub-object

### `getFullConfig()`

This method returns the entire config as an object.

Returns: `Object|null`

Example:
<!-- {% raw %} -->
```twig
{% set config = craft.viget.tailwind.getFullConfig() %}
{% set font = tw.theme.fontFamily.sans[0] ?? null %}
```
<!-- {% endraw %} -->

***

### `getColors()`

This method returns an array of all the colors, with formatted names (respecting nested objects) as the keys.

Returns: `Array`

Example:
<!-- {% raw %} -->
```twig
{% set colors = craft.viget.tailwind.getColors() %}

{% for name, hex in colors %}
  {{ name }}: {{ hex }}<br>
{% endfor %}
```
<!-- {% endraw %} -->




