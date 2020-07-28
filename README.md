# Craft Viget Base Module

## Test Suite Setup

This repo utilizes Ddocker in order to run the test suite.

1. Install [Docker Desktop for Mac](https://docs.docker.com/docker-for-mac/install/).

2. Build the containers

    Run `docker-compose build`. This will take a little while.

3. Run the app

    Run `docker-compose up`.

4. Run the tests

    Run `bin/codecept run`

## Out of the box, the module includes:

### Airtable Inventory

We have an Airtable setup to store the inventory of all of our Craft sites. A request will be made (as a queue job) once per day when someone visits the CP.

You will need to add the following `ENV` values:

```
AIRTABLE_API_KEY
AIRTABLE_BASE
```

### Custom partial() Twig method

The custom `partial()` Twig method as a shorthand for:

```twig
{% include 'path/to/file' ignore missing with {
    key: 'value',
} only %}
```

With this module enabled, you can do:

```twig
{{ partial('path/to/file', {
    key: 'value',
}) }}
```

### Customized CP Navigation

When in `devMode`, we are customizing the Craft CP navigation via a `/config/dev.php` (which should be gitignored). By default, we are adding links to **Sections** and **Fields** (controlled by `useDefaults`), and the three most **recent entries**. You can add links by adding to `navItems`.

Here's a full sample configuration:

```php
// /config/dev.php
<?php

return [
    'useDefaults' => true,
    'navItems' => [
        0 => [
            'url' => '#TODO',
            'label' => 'Custom Thing',
        ],
    ],
    'showRecentEntries' => 3,
    'icon' => 'disabled',
];
```

If you'd like to restore the stock Craft navigation, use:

```php
// /config/dev.php

<?php

return [
    'useDefaults' => false,
    'showRecentEntries' => false,
];
```

### Always show debug bar in dev environment

Regardless of logged-in state or permissions, the Craft Debug Bar will always be visible.

### Front-end edit entry Links

When visiting an element URL on the front-end, an edit entry link to edit that element in the CP will be present in the lower left hand corner. This will always be present in the dev environment regardless of logged-in state or permissions.

### Components exposed to Twig `craft.viget` sub-object

#### `craft.viget.util.fillInEntries()`

This method queries for additional, deduped entries to a fill a number of needed entries
