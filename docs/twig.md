---
layout: default
title: Twig
nav_order: 1
---

# Twig

The base modules adds custom functionality to Twig

## Custom partial() Twig method

The custom `partial()` Twig method as a shorthand for:

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
