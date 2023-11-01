---
layout: default
title: Extras
nav_order: 5
---

# Extras

## Always show debug bar in dev environment

Regardless of logged-in state or permissions, the Craft Debug Bar will always be visible.

## Front-end edit entry Links

When visiting an element URL on the front-end, an edit entry link to edit that element in the CP will be present in the lower left hand corner. This will always be present in the dev environment regardless of logged-in state or permissions.

In order to prevent these links from being cached by Blitz (or simply to remove it from any site), `^v5.0.4` adds a boolean `disableEditButton` option to the module config which prevents the `Edit Entry` links from being output. The default value is `false` but can be changed in a project’s `config/viget.php` to `true` or `!App::devMode()`:

```php
<?php

use craft\helpers\App;

return [
    'disableEditButton' => !App::devMode(), // default is false
];
```
