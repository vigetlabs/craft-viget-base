---
layout: default
title: Helpers
nav_order: 6
---

# Helpers

## Search Index
We include a helper for common tasks related to Craft's searchindex table which can become unweildy when large amounts of data are in the system. To access the helper from anywhere in the application you can grab it with `use viget\base\helpers\SearchIndex;`

For example you can optimize that table:
```php
use viget\base\helpers\SearchIndex;

...

try {
    SearchIndex::optimize();
} catch (\Exception $e) {
    // Handle the error
}
```
