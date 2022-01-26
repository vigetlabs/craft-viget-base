---
layout: default
title: Installation
nav_order: 1
---

# Installation

```
composer require viget/craft-viget-base
```

Lock it at a specific version if desired.

Open the `config/app.php` and bootstrap the module (merging with existing modules if they exist):

```php
<?php

return [
    'modules' => [
        'viget-base' => [
            'class' => \viget\base\Module::class,
        ],
    ],
    'bootstrap' => [
        'viget-base',
    ],
];
```

## Upgrading

If you were using a version of the base module pre 4.0.0, you were previously initializing the base module within an existing module. The following code should now be removed and you should bootstrap in the `config/app.php` file:

```php
// Initialize all the viget base code
$this->setModules([
    'viget-base' => [
        'class' => '\viget\base\Module',
    ],
]);

$this->getModule('viget-base');
```

## Configure Phone Home

Add the following ENV variables to your enviroment file in all environments

```
AIRTABLE_API_KEY
AIRTABLE_BASE
```

Using the base ID from the Craft Inventory Airtable and the API key from 1Password for the Viget Airtable account.

### Delete code

If you used anything that was previously in the [Starter Module](https://github.com/vigetlabs/craft-starter-module), you can likely delete a bunch of code from your module. Carefully examine the docs to see what you can remove.
