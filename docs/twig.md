---
layout: default
title: Twig
nav_order: 2
---

# Twig

The base modules adds custom functionality to Twig

## Custom partial() Twig method

The custom `partial()` Twig method as a shorthand for:

```twig
{% include 'path/to/file' ignore missing with {
    key: 'value',
} only %}
```

With this module enabled, to prevent leaky templates you should do:

```twig
{{ partial('path/to/file', {
    key: 'value',
}) }}
```

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

```twig
{% set featuredArticles = craft.viget.util.fillInEntries(
    entry.featuredArticles,
    {
        section: 'article',
    },
    4
) %}
```
