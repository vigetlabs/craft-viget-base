---
layout: default
title: Twig
nav_order: 2
---

# Twig

The base modules adds custom functionality to Twig

## partial() function

The custom `partial()` Twig function as a shorthand for:

<!-- {% raw %} -->
```twig
{% include 'path/to/file' ignore missing with {
    key: 'value',
} only %}
```
<!-- {% endraw %} -->

With this module enabled, to prevent leaky templates you should do:

<!-- {% raw %} -->
```twig
{{ partial('path/to/file', {
    key: 'value',
}) }}
```
<!-- {% endraw %} -->

## gtm() function

This function is useful for generating `data-track-gtm` attributes and automatically getting the imploding and character escaping. You can pass string parameters or an array of strings

<!-- {% raw %} -->
```twig
<div data-track-gtm="{{ gtm('One', 'Two', 'Three') }}">
</div>

<div data-track-gtm="{{ gtm(['One', 'Two', 'Three']) }}">
</div>
```
<!-- {% endraw %} -->

## Components exposed to Twig `craft.viget` sub-object

### `craft.viget.util.*`

#### `fillInEntries($entries, $params, $limit, $additionalIdsToSkip)`

This method queries for additional, deduped entries to a fill a number of needed entries

Parameters:

| Name                  | Type    | Default |
|:----------------------|:--------|:--------|
| `entries`             | `array` | `[]`    |
| `params`              | `array` | `[]`    |
| `limit`               | `int`   | `0`     |
| `additionalIdsToSkip` | `array` | `[]`    |

Returns: `array`

Example:

<!-- {% raw %} -->
```twig
{% set featuredArticles = craft.viget.util.fillInEntries(
    entry.featuredArticles,
    {
        section: 'article',
    },
    4
) %}
```
<!-- {% endraw %} -->
