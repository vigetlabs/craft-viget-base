---
layout: default
title: Control Panel
nav_order: 3
---

# Control Panel

## Customized CP Navigation

When in `devMode`, we are customizing the Craft CP navigation via the same config file used for all base module features: `/config/viget.php`. These settings should be nested under a `cpNav` key.

By default, we are adding links to **Sections** and **Fields** (controlled by `useDefaults`), and the three most **recent entries**. You can add links by adding to `navItems`.

Here's a full sample configuration:

```php
// /config/viget.php
<?php

return [
    'cpNav' => [
        'useDefaults' => true,
        'navItems' => [
            [
                'url' => '#TODO',
                'label' => 'Custom Thing',
            ],
        ],
        'showRecentEntries' => 3,
        'icon' => 'disabled',
    ],
    // other base module settings
    'partsKit' => [
        //...
    ],
];
```

If you'd like to restore the stock Craft navigation, use:

```php
// /config/viget.php

<?php

return [
    'cpNav' => [
        'useDefaults' => false,
        'showRecentEntries' => false,
    ],
];
```
