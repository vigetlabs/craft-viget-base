---
layout: default
title: Control Panel
nav_order: 3
---

# Control Panel

## Customized CP Navigation

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

## Airtable Inventory

We have an Airtable setup to store the inventory of all of our Craft sites. A request will be made (as a queue job) once per day when someone visits the CP.

You will need to add the following `ENV` values:

```
AIRTABLE_API_KEY
AIRTABLE_BASE
```
