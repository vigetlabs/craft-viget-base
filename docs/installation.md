---
layout: default
title: Installation
nav_order: 1
---

# Installation

## If you do not have an existing module

Use the [Craft Starter Module](https://github.com/vigetlabs/craft-starter-module)

## If you have an existing module

```
composer require viget/craft-viget-base
```

Lock it at a specific version if desired.

Open the main `Module` class that you want to use. At the top of your `init` method, add the following:

```php
// Initialize all the viget base code
$this->setModules([
    'viget-base' => [
        'class' => '\viget\base\Module',
    ],
]);

$this->getModule('viget-base');
```

### Configure Phone Home

Add the following ENV variables to your enviroment file in all environments

```
AIRTABLE_API_KEY
AIRTABLE_BASE
```

Using the base ID from the Craft Inventory Airtable and the API key from 1Password for the Viget Airtable account.

### Delete code

If you used anything that was previously in the [Starter Module](https://github.com/vigetlabs/craft-starter-module), you can likely delete a bunch of code from your module. Carefully examine the docs to see what you can remove.
